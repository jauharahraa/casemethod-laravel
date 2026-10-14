<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $fillable = ['user_id', 'subject_id', 'name', 'code'];

    public function subject() { return $this->belongsTo(Subject::class); }
    public function students() { return $this->belongsToMany(Student::class, 'classroom_student')->orderBy('full_name'); }
    public function cases() { return $this->hasMany(CaseStudy::class, 'classroom_id'); }

    public static function newCode(): string
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (static::where('code', $code)->exists());
        return $code;
    }
}
