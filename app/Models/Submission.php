<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    const DRAFT = 'draft';
    const FEEDBACK = 'feedback';
    const REVISING = 'revising';
    const LOCKED = 'locked';

    protected $fillable = ['case_id', 'student_id', 'rubric_version_id', 'status', 'submitted_at', 'locked_at'];
    protected $casts = ['submitted_at' => 'datetime', 'locked_at' => 'datetime'];

    public function case() { return $this->belongsTo(CaseStudy::class, 'case_id'); }
    public function student() { return $this->belongsTo(Student::class); }
    public function rubricVersion() { return $this->belongsTo(RubricVersion::class); }
    public function versions() { return $this->hasMany(SubmissionVersion::class)->orderBy('version'); }
    public function grade() { return $this->hasOne(Grade::class); }

    /** @return array{0:string,1:string} [label, kelas CSS] */
    public static function badge(?string $status, bool $graded = false, bool $published = false): array
    {
        if ($published) return ['Nilai tersedia', 'bg-emerald-100 text-emerald-700'];
        if ($graded) return ['Sudah dinilai', 'bg-indigo-100 text-indigo-700'];
        return match ($status) {
            self::DRAFT => ['Draft', 'bg-slate-100 text-slate-700'],
            self::FEEDBACK => ['Feedback AI', 'bg-amber-100 text-amber-700'],
            self::REVISING => ['Sedang revisi', 'bg-orange-100 text-orange-700'],
            self::LOCKED => ['Selesai', 'bg-blue-100 text-blue-700'],
            default => ['Belum mulai', 'bg-slate-100 text-slate-500'],
        };
    }
}
