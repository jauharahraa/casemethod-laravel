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

    const MAX_SCORE = 4; // Tetap 4 secara internal agar perhitungan nilai akhir 0-100 tetap berjalan mulus di balik layar

    public static function default(): array
    {
        return ['aspects' => [
            'identification' => [
                'label' => 'Identifikasi Masalah',
                'weight' => 25,
                'criteria' => [['name' => 'Menemukan masalah utama', 'description' => '', 'max' => 4, 'levels' => []]]
            ],
            'analysis' => [
                'label' => 'Analisis Masalah',
                'weight' => 35,
                'criteria' => [['name' => 'Menjelaskan penyebab masalah', 'description' => '', 'max' => 4, 'levels' => []]]
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
            $s = max(0, min(self::MAX_SCORE, (float) ($scores[$k] ?? 0)));
            $aspectScores[$k] = round(($s / self::MAX_SCORE) * 100, 2);
            $final += ($s / self::MAX_SCORE) * $a['weight'];
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
            if ($lockedWeights !== null && abs($w - (float) $lockedWeights[$k]) > 0.001) {
                throw ValidationException::withMessages(['rubric' => 'Bobot tidak dapat diubah setelah kasus dipublikasikan.']);
            }
            $total += $w;
            $out['aspects'][$k] = [
                'label' => $label,
                'weight' => $w,
                'criteria' => [[
                    'name' => trim($input[$k]['criteria'][0]['name']),
                    'description' => '',
                    'max' => self::MAX_SCORE,
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