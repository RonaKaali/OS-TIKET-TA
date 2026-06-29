<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeZone;
use Throwable;

class WorkingHoursAccessService
{
    private const DEFAULT_START = '08:00';

    private const DEFAULT_END = '17:00';

    private const DEFAULT_DAYS = [1, 2, 3, 4, 5];

    /**
     * Evaluate whether a user may start a login session at the given time.
     *
     * @return array<string, bool|int|string|array<int, int>>
     */
    public function evaluate(User $user, ?CarbonInterface $at = null): array
    {
        $timezone = $this->validTimezone(
            (string) config('zero_trust.working_hours.timezone', 'Asia/Makassar')
        );
        $now = $at
            ? CarbonImmutable::instance($at)->setTimezone($timezone)
            : now($timezone)->toImmutable();

        $start = $this->validTime(
            (string) config('zero_trust.working_hours.start', self::DEFAULT_START),
            self::DEFAULT_START
        );
        $end = $this->validTime(
            (string) config('zero_trust.working_hours.end', self::DEFAULT_END),
            self::DEFAULT_END
        );
        $days = $this->workingDays(config('zero_trust.working_hours.days', self::DEFAULT_DAYS));

        $startMinutes = $this->minutes($start);
        $endMinutes = $this->minutes($end);
        if ($startMinutes >= $endMinutes) {
            $start = self::DEFAULT_START;
            $end = self::DEFAULT_END;
            $startMinutes = $this->minutes($start);
            $endMinutes = $this->minutes($end);
        }

        $enabled = filter_var(
            config('zero_trust.working_hours.enabled', true),
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE
        ) ?? true;
        $hasException = (bool) $user->allow_after_hours_access;
        $currentMinutes = ($now->hour * 60) + $now->minute;
        $isWorkingDay = in_array($now->dayOfWeekIso, $days, true);
        $isWorkingTime = $currentMinutes >= $startMinutes && $currentMinutes < $endMinutes;
        $withinWorkingHours = $isWorkingDay && $isWorkingTime;
        $allowed = !$enabled || $hasException || $withinWorkingHours;

        return [
            'allowed' => $allowed,
            'enabled' => $enabled,
            'has_exception' => $hasException,
            'within_working_hours' => $withinWorkingHours,
            'is_working_day' => $isWorkingDay,
            'is_working_time' => $isWorkingTime,
            'reason' => $allowed ? $this->allowedReason($enabled, $hasException) : 'outside_working_hours',
            'current_time' => $now->format('H:i'),
            'current_date' => $now->format('d-m-Y'),
            'current_day' => $this->dayName($now->dayOfWeekIso),
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'days_label' => $this->daysLabel($days),
            'schedule_label' => $this->daysLabel($days) . ', ' . $this->displayTime($start) . '-' . $this->displayTime($end),
            'timezone' => $timezone,
            'timezone_label' => $this->timezoneLabel($timezone),
        ];
    }

    private function allowedReason(bool $enabled, bool $hasException): string
    {
        if (!$enabled) {
            return 'policy_disabled';
        }

        return $hasException ? 'user_exception' : 'within_working_hours';
    }

    private function validTime(string $value, string $fallback): string
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) === 1
            ? $value
            : $fallback;
    }

    private function validTimezone(string $timezone): string
    {
        try {
            new DateTimeZone($timezone);

            return $timezone;
        } catch (Throwable) {
            return 'Asia/Makassar';
        }
    }

    private function minutes(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return ($hour * 60) + $minute;
    }

    /**
     * @return array<int, int>
     */
    private function workingDays(mixed $configuredDays): array
    {
        $values = is_array($configuredDays)
            ? $configuredDays
            : explode(',', (string) $configuredDays);

        $days = array_values(array_unique(array_filter(
            array_map('intval', $values),
            fn (int $day): bool => $day >= 1 && $day <= 7
        )));
        sort($days);

        return $days === [] ? self::DEFAULT_DAYS : $days;
    }

    /**
     * @param array<int, int> $days
     */
    private function daysLabel(array $days): string
    {
        if ($days === self::DEFAULT_DAYS) {
            return 'Senin-Jumat';
        }

        return implode(', ', array_map(fn (int $day): string => $this->dayName($day), $days));
    }

    private function dayName(int $day): string
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ][$day] ?? 'Tidak diketahui';
    }

    private function displayTime(string $time): string
    {
        return str_replace(':', '.', $time);
    }

    private function timezoneLabel(string $timezone): string
    {
        return match ($timezone) {
            'Asia/Jakarta' => 'WIB (Asia/Jakarta)',
            'Asia/Makassar' => 'WITA (Asia/Makassar)',
            'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
            default => $timezone,
        };
    }
}
