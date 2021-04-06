<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('createRandomPassword')) {
    /**
     * Create random password string
     *
     * @param int $length
     * @param array $pool
     * @return string $password
     */
    function createRandomPassword($pool, $length)
    {
        $password = substr(str_shuffle(implode("", $pool)), 0, $length - 3);
        /* Password has at least one character of every sub-pool */
        foreach ($pool as $subPool) {
            $password = substr_replace($password, $subPool[random_int(0, strlen($subPool) - 1)], random_int(0, strlen($password)), 0);
        }
        return $password;
    }
}

if (!function_exists("createExpiredPasswordDate")) {
    /**
     * Create password expired date
     *
     * @param string $expiredIn
     * @param string $diff
     * @param string $format
     * @return string $date
     */
    function createExpiredPasswordDate($expiredIn, $diff = "-1 day", $format = "Y-m-d")
    {
        $currentTime = new DateTime();
        return $currentTime->modify(str_replace("+", "-", $expiredIn))->modify($diff)->format($format);
    }
}
