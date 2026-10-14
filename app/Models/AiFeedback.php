<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFeedback extends Model
{
    protected $table = 'ai_feedbacks';
    protected $fillable = ['submission_version_id', 'mode', 'status', 'content', 'error'];
    protected $casts = ['content' => 'array'];
}
