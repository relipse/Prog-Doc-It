<?php
/**
 * Time Utilities class
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

class TimeUtil{
    /**
     * Show Time ago
     * @param $when
     * @return string
     */
    public static function Ago($when){
        if (is_numeric($when)){
            $seconds_ago = time() - $when;
        }else {
            $seconds_ago = time() - strtotime($when);
        }

        $orig = $seconds_ago;
        $seconds_ago = abs($seconds_ago);

        // Thanks (modified https://stackoverflow.com/a/27330857)
        if ($seconds_ago >= 31536000) {
            return intval($seconds_ago / 31536000) . " years ".self::PastOrFuture($orig, $seconds_ago);
        } else if ($seconds_ago >= 2419200) {
            return intval($seconds_ago / 2419200) . " months ".self::PastOrFuture($orig, $seconds_ago);
        } else if ($seconds_ago >= 86400) {
            return intval($seconds_ago / 86400) . " days ".self::PastOrFuture($orig, $seconds_ago);
        } else if ($seconds_ago >= 3600) {
            return intval($seconds_ago / 3600) . " hours ".self::PastOrFuture($orig, $seconds_ago);
        } else if ($seconds_ago >= 60) {
            return intval($seconds_ago / 60) . " minutes ".self::PastOrFuture($orig, $seconds_ago);
        } else {
            return "less than a minute ".self::PastOrFuture($orig, $seconds_ago);
        }
    }

    /**
     * "ago" or "from now"
     * @param $orig
     * @param $abs
     * @return string
     */
    protected static function PastOrFuture($orig, $abs){
        if ($orig === $abs){
            return "ago";
        }else{
            return "from now";
        }
    }
}