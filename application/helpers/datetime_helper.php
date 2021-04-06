<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('datetimeToString')) {
    /**
     * Convert datetime string to a specific format string
     *
     * @param string $format
     * @param string $datetimeString
     * @return string $string
     */
    function datetimeToString($datetimeString, $format = DATE_TIME_DEFAULT)
    {
        if ($datetimeString == "0000-00-00 00:00:00") {
            return "";
        }
        $unixTimestamp = strtotime($datetimeString);
        return ($unixTimestamp) ? date($format, $unixTimestamp) : "";
    }
}
