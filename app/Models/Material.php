<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Material extends Model
{
    /** Tema warna sampul (kelas Tailwind lengkap agar terdeteksi). */
        const THEMES = [
        'blue' => 'from-[#2F6772] via-[#3E828E] to-[#A6C9B6]',
        'indigo' => 'from-[#27153D] via-[#4A3068] to-[#3E828E]',
        'emerald' => 'from-[#3E828E] via-[#6FA28C] to-[#A6C9B6]',
        'amber' => 'from-[#E27A7D] via-[#F6B6B7] to-[#FFEBED]',
        'rose' => 'from-[#27153D] via-[#B5686F] to-[#F6B6B7]',
        'violet' => 'from-[#4A3068] via-[#8F6FA3] to-[#F6B6B7]',
    ];

    const ICONS = ['book-open', 'lightbulb', 'flask-conical', 'calculator', 'globe', 'landmark', 'cpu', 'leaf', 'briefcase', 'scale', 'heart-pulse', 'palette'];

    protected $fillable = ['user_id', 'classroom_id', 'title', 'summary', 'content', 'theme', 'icon', 'link_url', 'link_label', 'status'];

    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function reads() { return $this->hasMany(MaterialRead::class); }
    public function isPublished(): bool { return $this->status === 'published'; }

    public function gradient(): string
    {
        return self::THEMES[$this->theme] ?? self::THEMES['blue'];
    }

    public function readingMinutes(): int
    {
        $words = count(preg_split('/\s+/u', trim(strip_tags((string) $this->content)), -1, PREG_SPLIT_NO_EMPTY));
        return max(1, (int) ceil($words / 200));
    }

    /** Markdown -> HTML aman (HTML mentah dibuang, tautan berbahaya diblokir). */
    public function html(): HtmlString
    {
        return new HtmlString(Str::markdown((string) $this->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }
}