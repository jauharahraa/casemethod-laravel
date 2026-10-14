<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $table = 'cases';
    protected $fillable = ['user_id', 'classroom_id', 'title', 'description', 'instructions', 'objectives', 'deadline', 'status'];
    protected $casts = ['deadline' => 'datetime'];

    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function rubricVersions() { return $this->hasMany(RubricVersion::class, 'case_id'); }
    public function currentRubric() { return $this->hasOne(RubricVersion::class, 'case_id')->latestOfMany('version'); }
    public function submissions() { return $this->hasMany(Submission::class, 'case_id'); }
    public function isPublished(): bool { return $this->status === 'published'; }
}
