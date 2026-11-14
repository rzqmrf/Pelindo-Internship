<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentoringNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'mentor_id',
        'session_date',
        'topic',
        'notes',
        'action_items',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
