<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RubricService
{
    const ASPECTS = [
        'identification' => 'Identifikasi Masalah',
        'analysis' => 'Analisis Masalah',
        'solution' => 'Solusi',
    ];

    // Batas skor maksimum per aspek sesuai ketentuan baru
    const MAX_SCORES = [
        'identification' => 3,
        'analysis' => 3,
        'solution' => 4,
    ];

    public static function default(): array
    {
        return ['aspects' => [
            'identification' => [
                'label' => 'Identifikasi Masalah',
                'weight' => 25,
                'criteria' => [['name' => 'Menemukan masalah utama', 'description' => '', 'max' => 3, 'levels' => []]]
            ],
            'analysis' => [
                'label' => 'Analisis Masalah',
                'weight' => 35,
                'criteria' => [['name' => 'Menjelaskan penyebab masalah', 'description' => '', 'max' => 3, 'levels' => []]]
            ],
            'solution' => [
                'label' => 'Solusi',
                'weight' => 40,
                'criteria' => [['name' => 'Memberikan solusi yang tepat dan dapat diterapkan', 'description' => '', 'max' => 4, 'levels' => []]]
            ],
        ]];
    }

    public static function isSimplified(array $rubric): bool
    {
        return true;
    }

    public static function finalFromAspectScores(array $rubric, array $scores): array
    {
        $aspectScores = [];
        $final = 0.0;
        
        foreach ($rubric['aspects'] as $k => $a) {
            // Ambil batas max dari kriteria rubrik atau dari MAX_SCORES
            $max = (float) ($a['criteria'][0]['max'] ?? (self::MAX_SCORES[$k] ?? 4));
            if ($max <= 0) $max = 4;

            // Dapatkan nilai raw: tangani jika dikirim dalam bentuk array scores[k][0] atau scores[k]
            $rawScore = $scores[$k] ?? 0;
            if (is_array($rawScore)) {
                $rawScore = reset($rawScore); // Ambil elemen pertama dari array kriteria
            }

            $s = max(0, min($max, (float) $rawScore));
            
            // Konversi skor aspek ke skala 0-100 secara proporsional
            $aspectScores[$k] = round(($s / $max) * 100, 2);
            $final += ($s / $max) * $a['weight'];
        }
        
        return ['aspect_scores' => $aspectScores, 'final' => round($final, 2)];
    }

    public static function fromRequest(Request $r, ?array $lockedWeights = null): array
    {
        $rules = [];
        foreach (self::ASPECTS as $k => $l) {
            $rules["rubric.$k.weight"] = 'required|numeric|min:0|max:100';
            $rules["rubric.$k.criteria.0.name"] = 'required|string|max:150';
        }
        $input = Validator::make($r->all(), $rules, [], ['rubric' => 'rubrik'])->validate()['rubric'];

        $out = ['aspects' => []];
        $total = 0;
        foreach (self::ASPECTS as $k => $label) {
            $w = (float) $input[$k]['weight'];
            if ($lockedWeights !== null && isset($lockedWeights[$k]) && abs($w - (float) $lockedWeights[$k]) > 0.001) {
                throw ValidationException::withMessages(['rubric' => 'Bobot tidak dapat diubah setelah kasus dipublikasikan.']);
            }
            $total += $w;
            $max = self::MAX_SCORES[$k] ?? 4;
            
            $out['aspects'][$k] = [
                'label' => $label,
                'weight' => $w,
                'criteria' => [[
                    'name' => trim($input[$k]['criteria'][0]['name']),
                    'description' => '',
                    'max' => $max,
                    'levels' => [],
                ]],
            ];
        }
        
        if (abs($total - 100) > 0.001) {
            throw ValidationException::withMessages(['rubric' => 'Total bobot harus tepat 100%.']);
        }
        
        return $out;
    }

    public static function weights(array $rubric): array
    {
        return array_map(fn ($a) => $a['weight'], $rubric['aspects']);
    }

    public static function calculate(array $rubric, array $scores): array
    {
        return self::finalFromAspectScores($rubric, $scores);
    }
}