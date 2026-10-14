<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['user_id', 'name', 'description'];

    public function classrooms() { return $this->hasMany(Classroom::class); }
}
