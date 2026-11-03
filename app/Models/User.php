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
    public function roleDefinition(): ?Role
    {
        return Role::findBySlug($this->role);
    }

    public function basePortal(): string
    {
        $def = $this->roleDefinition();
        if ($def && !empty($def->base_portal)) {
            return $def->base_portal;
        }

        return in_array($this->role, ['superadmin', 'admin', 'supervisor', 'intern'], true)
            ? $this->role
            : 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin' || $this->basePortal() === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function usesAdminPortal(): bool
    {
        return $this->role === 'admin' || $this->basePortal() === 'admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor' || $this->basePortal() === 'supervisor';
    }

    public function isIntern(): bool
    {
        return $this->role === 'intern' || $this->basePortal() === 'intern';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $def = $this->roleDefinition();
        if ($def) {
            return $def->hasPermission($permission);
        }

        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        $def = $this->roleDefinition();
        if ($def && !empty($def->name)) {
            return $def->name;
        }

        return match ($this->role) {
            'superadmin' => 'Superadmin IT',
            'admin' => 'Admin HR',
            'supervisor' => 'Pembimbing',
            'intern' => 'Peserta Magang',
            default => ucfirst(str_replace(['_', '-'], ' ', (string) $this->role)),
        };
    }

    public function roleBadgeClass(): string
    {
        $def = $this->roleDefinition();
        if ($def) {
            return $def->badgeClasses();
        }

        return match ($this->role) {
            'superadmin' => 'bg-slate-900 text-white',
            'admin' => 'bg-blue-100 text-[#005B9C]',
            'supervisor' => 'bg-sky-50 text-sky-800 border border-sky-200',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function homeRoute(): string
    {
        $portal = $this->basePortal();

        if ($portal === 'superadmin') {
            return route('superadmin.dashboard');
        }
        if ($portal === 'supervisor') {
            return route('supervisor.dashboard');
        }
        if ($portal === 'intern') {
            return route('intern.dashboard');
        }

        foreach (Role::permissionCatalog() as $permKey => $meta) {
            if ($this->hasPermission($permKey) && !empty($meta['route'])) {
                return route($meta['route']);
            }
        }

        return route('profile.show');
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'slug');
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

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}

