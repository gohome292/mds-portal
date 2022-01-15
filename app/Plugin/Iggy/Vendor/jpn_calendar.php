<?php
// @param string $date
// @param array $options
// string "format"
// @return string
function jpn_calendar($date, $options = array())
{
    static $jpn_calendars;
    if (!isset($jpn_calendars)) {
        $jpn_calendars = fgetyml('jpn_calendars');
    }
    $pattern = '/^(\d{4})\-(\d{2})\-(\d{2})$/';
    // @exception
    if (!preg_match($pattern, $date, $dates)) return false;
    extract($options, EXTR_SKIP);
    _default($format, '');
    foreach ($jpn_calendars as $jpn_calendar) {
        extract($jpn_calendar);
        if ($date <= $end_date && $start_date <= $date) {
            preg_match($pattern, $start_date, $start_dates);
            $year = $dates[1] - $start_dates[1] + 1;
            switch ($format) {
            case 'array':
                return array(
                    'c' => $name,
                    'y' => $year,
                    'm' => $dates[2],
                    'd' => $dates[3],
                );
            default:
                if ($year === 1) $year = '元';
                return sprintf(
                    '%s%s年%s月%s日',
                    $name,
                    $year,
                    $dates[2],
                    $dates[3]
                );
            }
        }
    }
}
