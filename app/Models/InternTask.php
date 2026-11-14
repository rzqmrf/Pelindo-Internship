<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'assigned_by',
        'title',
        'description',
        'due_date',
        'status',
        'submission_note',
        'submission_file',
        'reviewer_note',
        'reviewed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
