<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'avatar',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isIntern(): bool
    {
        return $this->role === 'intern';
    }

    // Relationships
    public function supervisorProfile()
    {
        return $this->hasOne(SupervisorProfile::class, 'user_id');
    }

    public function internProfile()
    {
        return $this->hasOne(InternProfile::class, 'user_id');
    }

    public function supervisedInterns()
    {
        return $this->hasMany(InternProfile::class, 'supervisor_id');
    }

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class, 'intern_id');
    }

    public function reviewsToMake()
    {
        return $this->hasMany(DailyActivity::class, 'supervisor_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'intern_id');
    }

    public function supervisedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'supervisor_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function allowanceClaims()
    {
        return $this->hasMany(AllowanceClaim::class, 'intern_id');
    }

    public function supervisedAllowanceClaims()
    {
        return $this->hasMany(AllowanceClaim::class, 'supervisor_id');
    }

    public function assessments()
    {
        return $this->hasMany(InternAssessment::class, 'intern_id');
    }

    public function supervisedAssessments()
    {
        return $this->hasMany(InternAssessment::class, 'supervisor_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'intern_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'intern_id');
    }
}

