<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['submission_id', 'criterion_scores', 'aspect_scores', 'final_score', 'comment', 'published', 'published_at'];
    protected $casts = [
        'criterion_scores' => 'array',
        'aspect_scores' => 'array',
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function submission() { return $this->belongsTo(Submission::class); }
}
