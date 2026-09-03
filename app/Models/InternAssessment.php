<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'supervisor_id',
        'technical_skill',
        'discipline_score',
        'initiative_score',
        'teamwork_score',
        'attitude_score',
        'final_score',
        'predicate',
        'notes_strengths',
        'notes_improvements',
        'supervisor_recommendation',
        'final_project_title',
        'final_project_file',
        'final_project_link',
        'submitted_at',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'technical_skill' => 'integer',
        'discipline_score' => 'integer',
        'initiative_score' => 'integer',
        'teamwork_score' => 'integer',
        'attitude_score' => 'integer',
        'final_score' => 'float',
        'submitted_at' => 'datetime',
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

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'assessment_id');
    }

    /**
     * Helper to compute final score and predicate
     */
    public static function calculateGrade(array $scores): array
    {
        $tech = $scores['technical_skill'] ?? 0;
        $disc = $scores['discipline_score'] ?? 0;
        $init = $scores['initiative_score'] ?? 0;
        $team = $scores['teamwork_score'] ?? 0;
        $atti = $scores['attitude_score'] ?? 0;

        // Weighted calculation: Tech 30%, Disc 20%, Init 15%, Team 15%, Attitude 20%
        $final = ($tech * 0.30) + ($disc * 0.20) + ($init * 0.15) + ($team * 0.15) + ($atti * 0.20);
        $final = round($final, 2);

        if ($final >= 85) {
            $predicate = 'Sangat Baik (A)';
        } elseif ($final >= 75) {
            $predicate = 'Baik (B)';
        } elseif ($final >= 65) {
            $predicate = 'Cukup (C)';
        } else {
            $predicate = 'Kurang (D)';
        }

        return [
            'final_score' => $final,
            'predicate' => $predicate,
        ];
    }
}
