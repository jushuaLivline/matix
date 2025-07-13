<?php

namespace App\Services;

use Carbon\Carbon;

class CalendarService {
    function groupDatesPerMonth(Carbon $month, $group = 10){
        $days = $month->daysInMonth;
        $data = [];
        $month = Carbon::parse($month->startOfMonth());
        foreach (range(1, $days, $group) as $number) {
            $array = [];
            foreach(range($number, $number + $group - 1, 1) as $day){
                if($days >= $day){
                    $array[] = Carbon::parse($month->startOfMonth())->addDays($day - 1);
                }
            }
            $data[] = $array;
        }

        return $data;
    }
}