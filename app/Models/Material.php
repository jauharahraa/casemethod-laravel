<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Material extends Model
{
    /** Tema warna sampul (kelas Tailwind lengkap agar terdeteksi). */
    const THEMES = [
        'blue' => 'from-blue-900 via-blue-700 to-sky-500',
        'indigo' => 'from-indigo-900 via-indigo-700 to-violet-500',
        'emerald' => 'from-emerald-800 via-emerald-600 to-teal-400',
        'amber' => 'from-amber-600 via-orange-500 to-yellow-400',
        'rose' => 'from-rose-700 via-rose-500 to-pink-400',
        'violet' => 'from-fuchsia-800 via-purple-600 to-violet-400',
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