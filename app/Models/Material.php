<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Material extends Model
{
    /** Tema warna sampul (kelas Tailwind lengkap agar terdeteksi). */
        const THEMES = [
        'blue' => 'from-[#5f7cae] via-[#738fbd] to-[#a8c3d4]',
        'indigo' => 'from-[#6f6a9a] via-[#8f87b3] to-[#b9b2cf]',
        'emerald' => 'from-[#5d86a3] via-[#7fa3bb] to-[#a8c3d4]',
        'amber' => 'from-[#c77f8f] via-[#e0a0a8] to-[#eec6c7]',
        'rose' => 'from-[#c4708f] via-[#db88a4] to-[#eeaabd]',
        'violet' => 'from-[#a8648f] via-[#cc8eb1] to-[#e0b4cf]',
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