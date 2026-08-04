<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowanceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'supervisor_id',
        'period_number',
        'start_date',
        'end_date',
        'wfo_count',
        'wfh_count',
        'leave_count',
        'total_allowance',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'attachment_path',
        'status',
        'supervisor_note',
        'hr_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
