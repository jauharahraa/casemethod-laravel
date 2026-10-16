<?php

namespace App\Services;

use App\Models\AiFeedback;
use App\Models\SubmissionVersion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiFeedbackService
{
    public function generate(SubmissionVersion $version): AiFeedback
    {
        $key = config('services.gemini.key');
        $mode = $key ? 'gemini' : 'simulasi';
        $submission = $version->submission()->with(['case', 'rubricVersion'])->first();
        $rubric = $submission->rubricVersion->data;

        try {
            $content = $key
                ? $this->callGemini($key, $submission->case, $rubric, $version)
                : $this->simulate($rubric, $version);

            return AiFeedback::updateOrCreate(
                ['submission_version_id' => $version->id],
                ['mode' => $mode, 'status' => 'ok', 'content' => $content, 'error' => null]
            );
        } catch (\Throwable $e) {
            Log::warning('Feedback AI gagal: '.$e->getMessage());
            return AiFeedback::updateOrCreate(
                ['submission_version_id' => $version->id],
                ['mode' => $mode, 'status' => 'failed', 'content' => null, 'error' => Str::limit($e->getMessage(), 300)]
            );
        }
    }

    private function callGemini(string $key, $case, array $rubric, SubmissionVersion $v): array
    {
        $system = <<<TXT
Anda adalah asisten pengajar yang membantu mengevaluasi jawaban siswa berdasarkan kriteria sederhana:
1. Identifikasi Masalah (Skala maksimal 3)
2. Analisis Masalah (Skala maksimal 3)
3. Solusi (Skala maksimal 4)

Untuk setiap aspek, berikan:
- "status": Pilih salah satu dari tiga status berikut: "Sudah sesuai", "Perlu diperkuat", atau "Belum lengkap".
- "reason": Alasan singkat mengapa status tersebut diberikan berdasarkan isi jawaban siswa.
- "feedback": Saran perbaikan yang membangun dan memandu siswa berpikir kritis (jangan memberikan jawaban instan).

Jangan menghitung nilai akhir. Tulis dalam bahasa Indonesia yang ramah dan memotivasi.
TXT;

        $prompt = "<kasus>\nJudul: {$case->title}\n{$case->description}\n</kasus>\n\n"
            ."<jawaban_siswa>\n[Identifikasi Masalah]\n{$v->identification}\n\n[Analisis Masalah]\n{$v->analysis}\n\n[Solusi]\n{$v->solution}\n</jawaban_siswa>";

        $aspect = ['type' => 'OBJECT', 'properties' => [
            'status' => ['type' => 'STRING', 'enum' => ['Sudah sesuai', 'Perlu diperkuat', 'Belum lengkap']],
            'reason' => ['type' => 'STRING'], 
            'feedback' => ['type' => 'STRING'],
        ], 'required' => ['status', 'reason', 'feedback']];

        $model = config('services.gemini.model');

        $res = Http::timeout(60)->retry(1, 800, throw: false)
            ->withHeaders(['x-goog-api-key' => $key])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'systemInstruction' => ['parts' => [['text' => $system]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'aspects' => ['type' => 'OBJECT', 'properties' => [
                                'identification' => $aspect, 'analysis' => $aspect, 'solution' => $aspect,
                            ], 'required' => ['identification', 'analysis', 'solution']],
                            'conclusion' => ['type' => 'STRING'],
                        ],
                        'required' => ['aspects', 'conclusion'],
                    ],
                ],
            ]);

        if (! $res->successful()) {
            throw new \RuntimeException('Gemini HTTP '.$res->status().': '.($res->json('error.message') ?? 'permintaan gagal'));
        }

        $text = $res->json('candidates.0.content.parts.0.text');
        $data = is_string($text) ? json_decode($text, true) : null;
        if (! is_array($data)) {
            throw new \RuntimeException('Respons Gemini tidak berformat JSON yang valid.');
        }
        return self::normalize($data, $rubric);
    }

    public static function normalize(array $data, array $rubric): array
    {
        $aspects = [];
        
        $maxScores = [
            'identification' => 3,
            'analysis' => 3,
            'solution' => 4,
        ];

        foreach (RubricService::ASPECTS as $k => $label) {
            $a = $data['aspects'][$k] ?? null;
            $statusStr = $a['status'] ?? 'Perlu diperkuat';
            $max = $maxScores[$k] ?? 4;

            $score = match ($statusStr) {
                'Sudah sesuai' => $max,
                'Perlu diperkuat' => max(1, (int) round($max * 0.6)),
                default => 1,
            };

            $aspects[$k] = [
                'score' => $score,
                'max_score' => $max,
                'status' => $statusStr,
                'reason' => trim((string) ($a['reason'] ?? '')),
                'feedback' => trim((string) ($a['feedback'] ?? '')),
            ];
        }

        return [
            'aspects' => $aspects,
            'final_score' => RubricService::finalFromAspectScores($rubric, array_map(fn ($a) => $a['score'], $aspects))['final'],
            'conclusion' => trim((string) ($data['conclusion'] ?? 'Evaluasi selesai.')),
            'suggestions' => ['Periksa kembali catatan feedback di atas untuk melakukan perbaikan.'],
            'guiding_questions' => ['Bagaimana cara menyempurnakan jawabanmu berdasarkan saran di atas?'],
        ];
    }

    private function simulate(array $rubric, SubmissionVersion $v): array
    {
        $aspects = [];
        $maxScores = [
            'identification' => 3,
            'analysis' => 3,
            'solution' => 4,
        ];

        foreach (RubricService::ASPECTS as $k => $_) {
            $words = count(preg_split('/\s+/u', trim((string) $v->{$k}), -1, PREG_SPLIT_NO_EMPTY));
            $max = $maxScores[$k] ?? 4;
            $status = $words > 30 ? 'Sudah sesuai' : ($words > 10 ? 'Perlu diperkuat' : 'Belum lengkap');
            
            $score = match ($status) {
                'Sudah sesuai' => $max,
                'Perlu diperkuat' => max(1, (int) round($max * 0.6)),
                default => 1,
            };

            $aspects[$k] = [
                'score' => $score,
                'max_score' => $max,
                'status' => $status,
                'reason' => "Simulasi berdasarkan jumlah kata ($words kata).",
                'feedback' => 'Kembangkan uraian dengan merujuk langsung pada fakta kasus.',
            ];
        }

        return [
            'aspects' => $aspects,
            'final_score' => RubricService::finalFromAspectScores($rubric, array_map(fn ($a) => $a['score'], $aspects))['final'],
            'conclusion' => 'Ini hasil simulasi otomatis.',
            'suggestions' => ['Gunakan referensi kasus yang akurat.'],
            'guiding_questions' => ['Apakah argumen sudah logis?'],
        ];
    }
}