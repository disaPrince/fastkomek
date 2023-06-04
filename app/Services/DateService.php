<?php

namespace App\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;

class DateService
{
    public function isToday($date)
    {
        $now = new DateTime("now", new \DateTimeZone('Asia/Almaty'));
        $requestedTime = new DateTime($date, new \DateTimeZone('Asia/Almaty'));
        $current = strtotime($now->format('d.m.Y'));
        $date = strtotime($requestedTime->format('d.m.Y'));

        $datediff = $date - $current;
        $difference = floor($datediff / (60 * 60 * 24));
        Log::debug('$difference');
        Log::debug($difference);
        if ($difference == 0) {
            return true;
        }
        return false;
    }

    public function formatDate($date, $format = 'd.m.Y')
    {
        $dt = new DateTime($date);
        return $dt->format($format);
    }

    public function validateTimeTill($date, $customEnd)
    {
        $twoPm = new \DateTime();
        $twoPm->setTime(20, 0);

        if ($date < $twoPm) {
            return true;
        }
        return false;
    }

    public function validateTime($time, $customStart, $customEnd, $date)
    {
        $now = new \DateTime("now", new \DateTimeZone('Asia/Almaty'));
        $requestedTime = new \DateTime($date, new \DateTimeZone('Asia/Almaty'));
        $timestamp1 = strtotime($now->format('d.m.Y H:i'));
        $timestamp2 = strtotime($requestedTime->format('d.m.Y') . $time);
        $minutes = ($timestamp2 - $timestamp1) / (60);
        Log::debug('$minutes');
        Log::debug($minutes);
        if ($minutes < 0) {
            return false;
        }

        $start = strtotime($customStart);
        $end = strtotime($customEnd);
        $checkTime = strtotime($time);
        Log::debug('$checkTime - $start > 0');
        Log::debug($checkTime - $start > 0);
        Log::debug('$checkTime - $end < 0');
        Log::debug($checkTime - $end < 0);
        if (($checkTime - $start > 0) && ($checkTime - $end < 0)) {
            return true;
        }
        return false;
    }

    public function validateTimeRegex($date)
    {
        if (!preg_match('/([01]?[0-9]|2[0-3]):[0-5][0-9]/', $date)) {
            return false;
        }
        return true;
    }

    public function isNotEndedDate($date)
    {
        $endOfDay = new \DateTime($date);
        $endOfDay->setTime(20, 00);
        $today = new \DateTime("now", new \DateTimeZone('Asia/Almaty'));

        $timestamp1 = strtotime($today->format('d.m.Y H:i'));
        $timestamp2 = strtotime($endOfDay->format('d.m.Y H:i'));
        $hour = ($timestamp2 - $timestamp1) / (60 * 60);

        Log::debug($hour);

        if ($hour < 0) {
            return false;
        }
        return true;
    }

    public function validateDate($date, $format = 'd.m.Y', $customEnd = '20:00')
    {
        $dt = \DateTime::createFromFormat($format, $date);
        if ($format == 'H:i') {
            if ($dt && $dt->format($format) == $date) {
                $start = new \DateTime('08:00');
                $end = new \DateTime($customEnd);
                $checkTime = new \DateTime($date, new \DateTimeZone('Asia/Almaty'));
                if ($checkTime >= $start && $checkTime <= $end) {
                    return true;
                }
            }
            return false;
        }

        return $dt && $dt->format($format);
    }

    public function isWeekend($date)
    {
        // $inputDate = \DateTime::createFromFormat("d-m-Y", $date, new \DateTimeZone("Asia/Almaty"));
        return $date->format('N') >= 6;
    }

    public function getTimePicker(int $hour, int $minutes)
    {
        $prevHourCallback = 'timepicker-hour-';
        if ($hour == 0) {
            $prevHourCallback .= '23' . "-" . $minutes;
        } else {
            $prevHourCallback .= $hour - 1 . "-" . $minutes;
        }

        $nextHourCallback = 'timepicker-hour-';
        if ($hour == 23) {
            $nextHourCallback .= '0' . "-" . $minutes;
        } else {
            $nextHourCallback .= $hour + 1 . "-" . $minutes;
        }

        $prevMinutesCallback = 'timepicker-minutes-' . $hour . "-";
        if ($minutes == 0) {
            $prevMinutesCallback .= '50';
        } else {
            $prevMinutesCallback .= $minutes - 10;
        }

        $nextMinutesCallback = 'timepicker-minutes-' . $hour . "-";
        if ($minutes == 50) {
            $nextMinutesCallback .= '0';
        } else {
            $nextMinutesCallback .= $minutes + 10;
        }

        $timepickerMap = [
            [
                ['text' => '-', 'callback_data' => $prevHourCallback],
                ['text' => '-', 'callback_data' => $prevMinutesCallback],
            ],
            [
                ['text' => $hour, 'callback_data' => 'null_callback'],
                ['text' => $minutes, 'callback_data' => 'null_callback'],
            ],
            [
                ['text' => '+', 'callback_data' => $nextHourCallback],
                ['text' => '+', 'callback_data' => $nextMinutesCallback],
            ],
            [
                ['text' => 'ОК', 'callback_data' => 'timepicker-all-' . $hour . "-" . $minutes],
            ],
        ];

        return $timepickerMap;
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    public function get_calendar(int $month, int $year): array
    {
        $prevMonthCallback = 'calendar-month-';
        if ($month === 1) {
            $prevMonthCallback .= '12-' . ($year - 1);
        } else {
            $prevMonthCallback .= ($month - 1) . '-' . $year;
        }

        $nextMonthCallback = 'calendar-month-';
        if ($month === 12) {
            $nextMonthCallback .= '1-' . ($year + 1);
        } else {
            $nextMonthCallback .= ($month + 1) . '-' . $year;
        }

        $start = new \DateTime(sprintf('%d-%d-01', $year, $month));

        $calendarMap = [
            [
                ['text' => '<', 'callback_data' => $prevMonthCallback],
                ['text' => $start->format('F Y'), 'callback_data' => 'calendar-months_list-' . $year],
                ['text' => '>', 'callback_data' => $nextMonthCallback],
            ],
            [
                ['text' => 'Mon', 'callback_data' => 'null_callback'],
                ['text' => 'Tue', 'callback_data' => 'null_callback'],
                ['text' => 'Wed', 'callback_data' => 'null_callback'],
                ['text' => 'Thu', 'callback_data' => 'null_callback'],
                ['text' => 'Fri', 'callback_data' => 'null_callback'],
                ['text' => 'Sat', 'callback_data' => 'null_callback'],
                ['text' => 'Sun', 'callback_data' => 'null_callback'],
            ],
        ];


        $end = clone $start;
        $end->modify('last day of this month');
        $iterEnd = clone $start;
        $iterEnd->modify('first day of next month');
        $row = 2;
        foreach (new DatePeriod($start, new DateInterval("P1D"), $iterEnd) as $date) {
            /** @var \DateTime $date */

            if (!isset($calendarMap[$row])) {
                $calendarMap[$row] = array_combine([1, 2, 3, 4, 5, 6, 7], [[], [], [], [], [], [], []]);
            }

            $dayIterator = (int)$date->format('N');
            if ($dayIterator != 1 && $start->format('d') === $date->format('d')) {
                for ($i = 1; $i < $dayIterator; $i++) {
                    $calendarMap[$row][$i] = ['text' => ' ', 'callback_data' => 'null_callback'];
                }
            }

            $today =  $date->format('d');
            
            if($today === date('d')){
                $today = "*".$today."*";
            }

            $calendarMap[$row][$dayIterator] = ['text' => $today, 'callback_data' => sprintf('calendar-day-%d-%d-%d', $date->format('d'), $month, $year)];

            if ($dayIterator < 7 && $end->format('d') === $date->format('d')) {
                for ($i = $dayIterator + 1; $i <= 7; $i++) {
                    $calendarMap[$row][$i] = ['text' => ' ', 'callback_data' => 'null_callback'];
                }
                $calendarMap[$row] = array_values($calendarMap[$row]);
                break;
            }

            if ($dayIterator === 7) {
                $calendarMap[$row] = array_values($calendarMap[$row]);
                $row++;
            }
        }

        return $calendarMap;
    }

    public function get_months_list(int $year): array
    {
        $listMap = [
            [
                ['text' => '<', 'callback_data' => 'calendar-year-' . ($year - 1)],
                ['text' => $year, 'callback_data' => 'calendar-years_list-' . $year],
                ['text' => '>', 'callback_data' => 'calendar-year-' . ($year + 1)],
            ],
        ];

        $row = 1;

        for ($month = 1; $month <= 12; $month++) {
            $listMap[$row][] = ['text' => date('F', strtotime(sprintf('%d-%d-01', $year, $month))), 'callback_data' => sprintf('calendar-month-%d-%d', $month, $year)];

            if ($month === 3 || $month === 6 || $month === 9) {
                $row++;
            }
        }

        return $listMap;
    }

    public function get_years_list(int $centerYear): array
    {
        $prevYear = $centerYear - 25;
        $nextYear = $centerYear + 25;
        $listMap = [
            [
                $prevYear <= 76 ? ['text' => ' ', 'callback_data' => 'null_callback'] : ['text' => '<', 'callback_data' => 'calendar-years_list-' . $prevYear],
//            ['text' => ' ', 'callback_data' => 'null_callback'],
                $nextYear >= 10024 ? ['text' => ' ', 'callback_data' => 'null_callback'] : ['text' => '>', 'callback_data' => 'calendar-years_list-' . $nextYear],
            ],
        ];

        $row = 1;
        $i = 0;

        for ($year = ($centerYear - 12); $year <= ($centerYear + 12); $year++) {
            if ($year >= 100 && $year <= 9999) {
                $listMap[$row][] = ['text' => $year, 'callback_data' => sprintf('calendar-months_list-%d', $year)];
                $i++;
            } else {
//            $listMap[$row][] = ['text' => ' ', 'callback_data' => sprintf('calendar-months_list-%d', $year)];
            }

            if ($i === 5 || $i === 10 || $i === 15 || $i === 20) {
                $row++;
            }
        }


        return $listMap;
    }

}
