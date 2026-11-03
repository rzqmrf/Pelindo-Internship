<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\InternActivityController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AllowanceClaimController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SuperAdminController;

// Redirect Root to Login or Dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

// Public Certificate Verification & Download
Route::get('/verify-certificate/{token}', [AssessmentController::class, 'publicVerify'])->name('certificate.verify');
Route::get('/certificate/download/{token}', [AssessmentController::class, 'downloadPdf'])->name('certificate.download');
Route::get('/maintenance-bypass/{token}', [SuperAdminController::class, 'activateMaintenanceBypass'])->name('maintenance.bypass');

Route::get('/manifest.json', function () {
    return response()->json(json_decode(file_get_contents(public_path('manifest.json')), true));
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Common Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Leave Impersonation Session (Accessible from any impersonated role)
    Route::post('/impersonate/leave', [SuperAdminController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // 0. Superadmin / System Developer Routes
    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        // Maintenance Mode & System Broadcast Banner
        Route::get('/maintenance', [SuperAdminController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance', [SuperAdminController::class, 'updateMaintenance'])->name('maintenance.update');
        Route::post('/maintenance/regenerate-bypass', [SuperAdminController::class, 'regenerateMaintenanceBypass'])->name('maintenance.regenerate_bypass');

        // Dynamic Operational Parameters, Gateway Diagnostics & Scheduler Trigger
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/test-wa', [SuperAdminController::class, 'testWhatsAppGateway'])->name('settings.test_wa');
        Route::post('/settings/test-email', [SuperAdminController::class, 'testEmailGateway'])->name('settings.test_email');
        Route::post('/system/reminders', [SuperAdminController::class, 'triggerReminders'])->name('system.reminders');
        Route::get('/system/backup-db', [SuperAdminController::class, 'downloadDatabaseBackup'])->name('system.backup_db');

        // Cache & System Optimizer
        Route::post('/system/optimize', [SuperAdminController::class, 'optimizeSystem'])->name('system.optimize');

        // Cross-Role User Management & Impersonation
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('users.toggle_status');
        Route::delete('/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{id}/impersonate', [SuperAdminController::class, 'impersonate'])->name('users.impersonate');

        // Dynamic Role & Permission Management (RBAC)
        Route::get('/roles', [SuperAdminController::class, 'roles'])->name('roles');
        Route::post('/roles', [SuperAdminController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{id}', [SuperAdminController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{id}', [SuperAdminController::class, 'deleteRole'])->name('roles.delete');

        // Active Sessions & Background Queue Monitor
        Route::get('/sessions', [SuperAdminController::class, 'sessionsAndQueues'])->name('sessions');
        Route::post('/sessions/revoke-others', [SuperAdminController::class, 'revokeOtherSessions'])->name('sessions.revoke_others');
        Route::delete('/sessions/{sessionId}', [SuperAdminController::class, 'revokeSession'])->name('sessions.revoke');
        Route::post('/queues/retry-all', [SuperAdminController::class, 'retryAllFailedJobs'])->name('queues.retry_all');
        Route::post('/queues/{uuid}/retry', [SuperAdminController::class, 'retryFailedJob'])->name('queues.retry');
        Route::delete('/queues/flush', [SuperAdminController::class, 'flushFailedJobs'])->name('queues.flush');
        Route::delete('/queues/{uuid}', [SuperAdminController::class, 'forgetFailedJob'])->name('queues.forget');

        // Laravel Error & Log Viewer
        Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
        Route::get('/logs/download', [SuperAdminController::class, 'downloadLog'])->name('logs.download');
        Route::delete('/logs/clear', [SuperAdminController::class, 'clearLog'])->name('logs.clear');

        // Security Audit Trail
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])->name('audit_logs');
    });

    // Report Routes (Only Interns)
    Route::middleware(['role:intern'])->group(function () {
        Route::get('/reports', [ReportExportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportExportController::class, 'generatePdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportExportController::class, 'exportExcel'])->name('reports.excel');
    });

    // 1. Admin HR & Custom Management Roles Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->middleware('permission:admin.dashboard')
            ->name('dashboard');

        // Manage Interns (Dedicated Full Pages)
        Route::middleware(['permission:interns.manage'])->group(function () {
            Route::get('/interns', [AdminController::class, 'interns'])->name('interns.index');
            Route::get('/interns/create', [AdminController::class, 'createIntern'])->name('interns.create');
            Route::post('/interns', [AdminController::class, 'storeIntern'])->name('interns.store');
            Route::get('/interns/{id}', [AdminController::class, 'showIntern'])->name('interns.show');
            Route::get('/interns/{id}/edit', [AdminController::class, 'editIntern'])->name('interns.edit');
            Route::put('/interns/{id}', [AdminController::class, 'updateIntern'])->name('interns.update');
            Route::delete('/interns/{id}', [AdminController::class, 'deleteIntern'])->name('interns.delete');
        });

        // Manage Supervisors
        Route::middleware(['permission:supervisors.manage'])->group(function () {
            Route::get('/supervisors', [AdminController::class, 'supervisors'])->name('supervisors.index');
            Route::post('/supervisors', [AdminController::class, 'storeSupervisor'])->name('supervisors.store');
            Route::put('/supervisors/{id}', [AdminController::class, 'updateSupervisor'])->name('supervisors.update');
            Route::delete('/supervisors/{id}', [AdminController::class, 'deleteSupervisor'])->name('supervisors.delete');
        });

        // Manage Divisions
        Route::middleware(['permission:divisions.manage'])->group(function () {
            Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
            Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
            Route::put('/divisions/{id}', [DivisionController::class, 'update'])->name('divisions.update');
            Route::delete('/divisions/{id}', [DivisionController::class, 'destroy'])->name('divisions.destroy');
        });

        // Global Monitoring & Attendance
        Route::middleware(['permission:monitoring.view'])->group(function () {
            Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
            Route::get('/monitoring/export-rekap', [AdminController::class, 'exportDivisionRekap'])->name('monitoring.export_rekap');
        });
        Route::get('/attendance', [AttendanceController::class, 'adminIndex'])
            ->middleware('permission:attendance.view')
            ->name('attendance.index');

        // Manage Announcements
        Route::middleware(['permission:announcements.manage'])->group(function () {
            Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
            Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
            Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        });

        // Allowance Claims & Periods
        Route::middleware(['permission:allowance.manage'])->group(function () {
            Route::get('/allowance', [AllowanceClaimController::class, 'hrIndex'])->name('allowance.index');
            Route::post('/allowance/bulk-process', [AllowanceClaimController::class, 'bulkProcessHr'])->name('allowance.bulk_process');
            Route::get('/allowance/export-payroll', [AllowanceClaimController::class, 'exportPayrollExcel'])->name('allowance.export_payroll');
            Route::post('/allowance/{id}/process', [AllowanceClaimController::class, 'hrProcess'])->name('allowance.process');
            Route::post('/allowance-periods', [AllowanceClaimController::class, 'storePeriod'])->name('allowance_periods.store');
            Route::post('/allowance-periods/{id}/toggle', [AllowanceClaimController::class, 'togglePeriod'])->name('allowance_periods.toggle');
            Route::delete('/allowance-periods/{id}', [AllowanceClaimController::class, 'destroyPeriod'])->name('allowance_periods.destroy');
        });

        // Manage Assessments & Certificates
        Route::middleware(['permission:assessments.manage'])->group(function () {
            Route::get('/assessments', [AssessmentController::class, 'adminIndex'])->name('assessments.index');
            Route::post('/assessments/{id}/publish', [AssessmentController::class, 'publishCertificate'])->name('assessments.publish');
        });

        // Intern Exit Satisfaction Survey Recap
        Route::get('/surveys', [\App\Http\Controllers\InternSurveyController::class, 'adminIndex'])
            ->middleware('permission:surveys.view')
            ->name('surveys.index');
    });

    // 2. Supervisor Routes
    Route::middleware(['role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
        Route::get('/interns', [SupervisorController::class, 'myInterns'])->name('interns');
        Route::get('/activities', [SupervisorController::class, 'activities'])->name('activities');
        Route::post('/activities/{id}/approve', [SupervisorController::class, 'approve'])->name('activities.approve');
        Route::get('/statistics', [SupervisorController::class, 'statistics'])->name('statistics');
        Route::get('/attendance', [AttendanceController::class, 'supervisorIndex'])->name('attendance.index');
        
        // Leave request approvals
        Route::get('/leaves', [LeaveRequestController::class, 'supervisorIndex'])->name('leaves.index');
        Route::post('/leaves/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');

        // Allowance Claims
        Route::get('/allowance', [AllowanceClaimController::class, 'supervisorIndex'])->name('allowance.index');
        Route::post('/allowance/{id}/approve', [AllowanceClaimController::class, 'supervisorApprove'])->name('allowance.approve');
        Route::post('/allowance/{id}/reject', [AllowanceClaimController::class, 'supervisorReject'])->name('allowance.reject');

        // Assessments & Evaluation
        Route::get('/assessments', [AssessmentController::class, 'supervisorIndex'])->name('assessments.index');
        Route::get('/assessments/{intern_id}/evaluate', [AssessmentController::class, 'evaluate'])->name('assessments.evaluate');
        Route::post('/assessments/{intern_id}', [AssessmentController::class, 'storeOrUpdate'])->name('assessments.store');
    });

    // 3. Intern Routes
    Route::middleware(['role:intern'])->prefix('intern')->name('intern.')->group(function () {
        Route::get('/dashboard', [InternActivityController::class, 'dashboard'])->name('dashboard');
        Route::get('/activities', [InternActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/create', [InternActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities', [InternActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{id}/edit', [InternActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activities/{id}', [InternActivityController::class, 'update'])->name('activities.update');

        // Attendance & Geofencing
        Route::get('/attendance', [AttendanceController::class, 'internIndex'])->name('attendance.index');
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

        // Leave request submissions
        Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');

        // Allowance Claims
        Route::get('/allowance', [AllowanceClaimController::class, 'index'])->name('allowance.index');
        Route::get('/allowance/calculate', [AllowanceClaimController::class, 'calculate'])->name('allowance.calculate');
        Route::post('/allowance', [AllowanceClaimController::class, 'store'])->name('allowance.store');

        // Certificate, Transcripts & Final Project
        Route::get('/certificate', [AssessmentController::class, 'internView'])->name('certificate.index');
        Route::post('/certificate/final-project', [AssessmentController::class, 'uploadFinalProject'])->name('certificate.final_project');

        // Intern Exit Satisfaction Survey
        Route::get('/survey', [\App\Http\Controllers\InternSurveyController::class, 'showForm'])->name('survey.index');
        Route::post('/survey', [\App\Http\Controllers\InternSurveyController::class, 'store'])->name('survey.store');
    });

    // Notification Routes
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});
