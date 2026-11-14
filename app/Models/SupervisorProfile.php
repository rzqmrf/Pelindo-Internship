<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division_id',
        'employee_id_number',
        'position_title',
        'phone',
        'delegate_supervisor_id',
        'delegation_start_date',
        'delegation_end_date',
    ];

    protected $casts = [
        'delegation_start_date' => 'date',
        'delegation_end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function delegateSupervisor()
    {
        return $this->belongsTo(User::class, 'delegate_supervisor_id');
    }
}
