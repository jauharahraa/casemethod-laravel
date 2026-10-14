<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionVersion extends Model
{
    protected $fillable = ['submission_id', 'version', 'identification', 'analysis', 'solution', 'status', 'submitted_at'];
    protected $casts = ['submitted_at' => 'datetime'];

    public function submission() { return $this->belongsTo(Submission::class); }
    public function feedback() { return $this->hasOne(AiFeedback::class, 'submission_version_id'); }
}
