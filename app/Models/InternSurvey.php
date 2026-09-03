<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'satisfaction_rating',
        'supervisor_rating',
        'facility_rating',
        'feedback_notes',
        'recommend_score',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(User::class, 'intern_id');
    }
}
