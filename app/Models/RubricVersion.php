<?php

namespace App\Models;

use App\Services\RubricService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RubricVersion extends Model
{
    protected $fillable = ['case_id', 'version', 'data'];

    /**
     * Kolom JSON MySQL mengurutkan ulang key objek (mis. "solution" sebelum "identification").
     * Accessor ini mengembalikan urutan aspek standar dan skor 4..0 agar tampilan selalu konsisten.
     */
    protected function data(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $d = is_string($value) ? json_decode($value, true) : $value;
                if (! is_array($d) || ! isset($d['aspects'])) return $d;

                $ordered = [];
                foreach (array_keys(RubricService::ASPECTS) as $k) {
                    if (! isset($d['aspects'][$k])) continue;
                    $a = $d['aspects'][$k];
                    foreach ($a['criteria'] as $i => $c) {
                        if (isset($c['levels']) && is_array($c['levels'])) {
                            krsort($c['levels']);
                            $a['criteria'][$i] = $c;
                        }
                    }
                    $ordered[$k] = $a;
                }
                $d['aspects'] = $ordered + $d['aspects'];
                return $d;
            },
            set: fn ($value) => json_encode($value),
        );
    }
}