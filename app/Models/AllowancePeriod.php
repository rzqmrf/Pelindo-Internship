<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AllowancePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'submission_start_date',
        'submission_end_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submission_start_date' => 'date',
        'submission_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Total logbook days in this period.
     */
    public function getLogbookDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Total submission open days.
     */
    public function getSubmissionDaysAttribute(): int
    {
        if (!$this->submission_start_date || !$this->submission_end_date) return 0;
        return $this->submission_start_date->diffInDays($this->submission_end_date) + 1;
    }

    /**
     * Check if currently open for submission.
     */
    public function getIsOpenAttribute(): bool
    {
        if (!$this->is_active) return false;
        $today = now()->startOfDay();
        return $today->gte($this->submission_start_date->startOfDay()) && $today->lte($this->submission_end_date->endOfDay());
    }

    /**
     * Get remaining days for submission.
     */
    public function getRemainingDaysAttribute(): int
    {
        $today = now()->startOfDay();
        if ($today->gt($this->submission_end_date)) return 0;
        return $today->diffInDays($this->submission_end_date) + 1;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
