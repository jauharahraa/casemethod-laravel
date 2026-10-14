<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'full_name', 'participant_number'];

    public function classrooms() { return $this->belongsToMany(Classroom::class, 'classroom_student'); }
    public function submissions() { return $this->hasMany(Submission::class); }
}
