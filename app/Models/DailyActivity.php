<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'supervisor_id',
        'date',
        'work_location',
        'activity_description',
        'notes',
        'attachment_path',
        'status',
        'supervisor_note',
        'reviewed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
