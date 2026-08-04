<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'quota',
        'head_name',
    ];

    public function supervisors()
    {
        return $this->hasMany(SupervisorProfile::class, 'division_id');
    }

    public function interns()
    {
        return $this->hasMany(InternProfile::class, 'division_id');
    }

    public function getActiveInternsCountAttribute()
    {
        return $this->interns()->where('status', 'active')->count();
    }
}
