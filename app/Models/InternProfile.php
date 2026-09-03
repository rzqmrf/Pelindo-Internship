<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division_id',
        'supervisor_id',
        'student_id_number',
        'nik_number',
        'institution',
        'major',
        'religion',
        'gender',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'address',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
