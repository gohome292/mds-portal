<?php
// @param string $date : "2011/01/01"
// @param bolean $is_end : FALSE:"00:00:00", TRUE:"23:59:59"
// @return string
function time_push($date, $is_end = false)
{
    if (strlen($date) != 10) return $date;
    // 指定日付の最後
    if ($is_end) {
        return "{$date} 23:59:59";
    // 指定日付の最初
    } else {
        return "{$date} 00:00:00";
    }
}
