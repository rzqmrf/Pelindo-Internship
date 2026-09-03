<?php

namespace App\Services;

use App\Models\AllowanceClaim;
use App\Models\DailyActivity;
use App\Models\User;
use Carbon\Carbon;

class AllowanceService
{
    public int $wfoRate = 71250; // Rp 71.250 / hari
    public int $wfhRate = 71250; // Rp 71.250 / hari
    public int $wfaRate = 71250; // Rp 71.250 / hari (Work From Anywhere / Tugas Luar)

    /**
     * Get all 20-weekday periods for an intern.
     */
    public function getInternPeriods(User $user): array
    {
        $profile = $user->internProfile;
        if (!$profile || !$profile->start_date) {
            return [];
        }

        $startDate = Carbon::parse($profile->start_date);
        $endDateLimit = Carbon::parse($profile->end_date ?? now());
        if ($endDateLimit->gt(now())) {
            $endDateLimit = now();
        }

        $periods = [];
        $currentDate = $startDate->copy();
        $periodNum = 1;

        while ($currentDate->lte($endDateLimit)) {
            $periodStartDate = $currentDate->copy();
            $weekdayCount = 0;
            $periodEndDate = $currentDate->copy();

            while ($weekdayCount < 20 && $periodEndDate->lte($endDateLimit)) {
                if (!$periodEndDate->isWeekend()) {
                    $weekdayCount++;
                }
                if ($weekdayCount < 20) {
                    $periodEndDate->addDay();
                }
            }

            if ($weekdayCount === 20) {
                $exists = $user->allowanceClaims()
                    ->where('period_number', $periodNum)
                    ->where('status', '!=', 'rejected')
                    ->exists();

                $periods[] = [
                    'number' => $periodNum,
                    'start_date' => $periodStartDate->format('Y-m-d'),
                    'end_date' => $periodEndDate->format('Y-m-d'),
                    'label' => "Periode " . $periodNum . " (" . $periodStartDate->isoFormat('D MMMM') . " - " . $periodEndDate->isoFormat('D MMMM YYYY') . ")",
                    'claimed' => $exists,
                    'in_progress' => false,
                ];
            } else {
                $periods[] = [
                    'number' => $periodNum,
                    'start_date' => $periodStartDate->format('Y-m-d'),
                    'end_date' => $periodEndDate->format('Y-m-d'),
                    'label' => "Periode " . $periodNum . " (" . $periodStartDate->isoFormat('D MMMM') . " - " . $periodEndDate->isoFormat('D MMMM YYYY') . ") [Berjalan]",
                    'claimed' => false,
                    'in_progress' => true,
                ];
                break;
            }

            $currentDate = $periodEndDate->copy()->addDay();
            $periodNum++;
        }

        return $periods;
    }

    /**
     * Calculate allowance breakdown for a date range.
     */
    public function calculateBreakdown(int $userId, string $startDate, string $endDate): array
    {
        $activities = DailyActivity::where('intern_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'pending', 'revision'])
            ->get();

        $wfoCount = $activities->where('work_location', 'WFO')->count();
        $wfhCount = $activities->where('work_location', 'WFH')->count();
        $wfaCount = $activities->where('work_location', 'WFA')->count();
        $leaveCount = $activities->where('work_location', 'Leave')->count();

        $totalAllowance = ($wfoCount * $this->wfoRate) + ($wfhCount * $this->wfhRate) + ($wfaCount * $this->wfaRate);

        return [
            'wfo_count' => $wfoCount,
            'wfh_count' => $wfhCount,
            'wfa_count' => $wfaCount,
            'leave_count' => $leaveCount,
            'wfo_rate' => $this->wfoRate,
            'wfh_rate' => $this->wfhRate,
            'wfa_rate' => $this->wfaRate,
            'wfo_rate_formatted' => 'Rp ' . number_format($this->wfoRate, 0, ',', '.'),
            'wfh_rate_formatted' => 'Rp ' . number_format($this->wfhRate, 0, ',', '.'),
            'wfa_rate_formatted' => 'Rp ' . number_format($this->wfaRate, 0, ',', '.'),
            'total_allowance' => $totalAllowance,
            'total_allowance_formatted' => 'Rp ' . number_format($totalAllowance, 0, ',', '.'),
        ];
    }
}
