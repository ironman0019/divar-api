<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

class JalaliDate
{
    /**
     * Convert a Gregorian date (or already-Jalali string) to Y/m/d Jalali.
     */
    public static function toJalali(DateTimeInterface|string|null $date, string $separator = '/'): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        if ($date instanceof DateTimeInterface) {
            [$jy, $jm, $jd] = self::gregorianToJalali(
                (int) $date->format('Y'),
                (int) $date->format('n'),
                (int) $date->format('j')
            );

            return self::formatParts($jy, $jm, $jd, $separator);
        }

        $parts = self::extractParts($date);
        if ($parts === null) {
            return null;
        }

        [$year, $month, $day] = $parts;

        if ($year < 1700) {
            return self::formatParts($year, $month, $day, $separator);
        }

        [$jy, $jm, $jd] = self::gregorianToJalali($year, $month, $day);

        return self::formatParts($jy, $jm, $jd, $separator);
    }

    /**
     * Convert a Jalali (or already-Gregorian) date string to Y-m-d Gregorian.
     */
    public static function toGregorian(?string $date): ?string
    {
        if ($date === null || trim($date) === '') {
            return null;
        }

        $parts = self::extractParts($date);
        if ($parts === null) {
            return null;
        }

        [$year, $month, $day] = $parts;

        if ($year >= 1700) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        [$gy, $gm, $gd] = self::jalaliToGregorian($year, $month, $day);

        return sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
    }

    public static function toCarbon(?string $date): ?Carbon
    {
        $gregorian = self::toGregorian($date);

        return $gregorian ? Carbon::parse($gregorian) : null;
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    private static function extractParts(string $date): ?array
    {
        $normalized = str_replace(['-', '.', ' '], '/', trim($date));

        if (! preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})/', $normalized, $matches)) {
            return null;
        }

        return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
    }

    private static function formatParts(int $year, int $month, int $day, string $separator): string
    {
        return sprintf('%04d%s%02d%s%02d', $year, $separator, $month, $separator, $day);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = $gm > 2 ? $gy + 1 : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd + $gDaysInMonth[$gm - 1];
        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (intdiv($jy, 33) * 8) + intdiv(($jy % 33) + 3, 4) + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gy = 400 * intdiv($days, 146097);
        $days %= 146097;

        if ($days > 36524) {
            $gy += 100 * intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }

        $gy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $gy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $gd = $days + 1;
        $leap = (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28;
        $salA = [0, 31, $leap, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        for ($gm = 1; $gm < 13 && $gd > $salA[$gm]; $gm++) {
            $gd -= $salA[$gm];
        }

        return [$gy, $gm, $gd];
    }
}
