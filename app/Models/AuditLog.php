<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    const UPDATED_AT = null;
    protected $fillable = ['user_id', 'student_id', 'owner_id', 'action', 'description'];

    /** Catat peristiwa penting. $ownerId = id guru pemilik data terkait. */
    public static function record(string $action, string $description, ?int $ownerId = null, ?int $studentId = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'student_id' => $studentId,
            'owner_id' => $ownerId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}
