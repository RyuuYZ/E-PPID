<?php

namespace App\Services;

use Carbon\Carbon;

class WorkingDayCalculator
{
    /**
     * Add N working days to a date, skipping weekends and holidays.
     */
    public function addWorkingDays(Carbon $from, int $days): Carbon
    {
        $date = $from->copy();
        $added = 0;

        while ($added < $days) {
            $date->addDay();
            if ($this->isWorkingDay($date)) {
                $added++;
            }
        }

        return $date;
    }

    /**
     * Count working days between two dates (exclusive of start, inclusive of end).
     */
    public function diffWorkingDays(Carbon $from, Carbon $to): int
    {
        $date = $from->copy();
        $count = 0;

        while ($date->lt($to)) {
            $date->addDay();
            if ($this->isWorkingDay($date)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check if a date is a working day (not weekend, not holiday).
     */
    public function isWorkingDay(Carbon $date): bool
    {
        // Weekend check (Saturday = 6, Sunday = 0)
        if ($date->isWeekend()) {
            return false;
        }

        // Holiday check
        $holidays = config('holidays', []);
        $dateStr = $date->format('Y-m-d');

        return !in_array($dateStr, $holidays);
    }

    /**
     * Get the remaining working days from now until a deadline.
     * Returns negative if past due.
     */
    public function remainingWorkingDays(Carbon $deadline): int
    {
        $now = Carbon::now()->startOfDay();
        $target = $deadline->copy()->startOfDay();

        if ($now->gte($target)) {
            return -$this->diffWorkingDays($target, $now);
        }

        return $this->diffWorkingDays($now, $target);
    }
}
