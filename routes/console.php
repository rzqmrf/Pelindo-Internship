<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily automated reminders for interns & supervisors
Schedule::command('internship:send-reminders')
    ->dailyAt('17:00')
    ->weekdays()
    ->timezone('Asia/Jakarta');

