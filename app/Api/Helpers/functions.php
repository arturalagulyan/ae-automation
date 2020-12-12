<?php

if (!function_exists('_strtotime')) {
    /**
     * @param int|string $date
     * @return false|int|string
     */
    function _strtotime($date)
    {
        return is_numeric($date) ? $date : strtotime(str_replace('/', '-', $date));
    }
}

if (!function_exists('carbon')) {
    /**
     * @param $date
     * @return \Carbon\Carbon|null
     */
    function carbon($date)
    {
        return $date ? \Carbon\Carbon::createFromTimestamp(_strtotime($date)) : null;
    }
}

if (!function_exists('carbon_tz')) {
    /**
     * @param $date
     * @return \Carbon\Carbon
     */
    function carbon_tz($date)
    {
        if (!defined('API_TIMEZONE')) {
            return carbon($date);
        }

        $local = carbon($date)->setTimezone(API_TIMEZONE);
        $server = carbon($date)->setTimezone(config('app.timezone'));

        $local = carbon($local->toDateTimeString());
        $server = carbon($server->toDateTimeString());

        $diff = $server->diffInHours($local);
        $diff = $server->greaterThan($local) ? $diff : -$diff;

        return carbon($date)->addHours($diff);
    }
}

if (!function_exists('_humanize')) {

    /**
     * @param $val
     * @return string
     */
    function _humanize($val)
    {
        $val = str_replace("_", "", $val);
        $matches = preg_split('/(?=[A-Z])/', $val);
        return trim(implode(" ", $matches));
    }
}

if (!function_exists('get_class_constants')) {
    /**
     * returns the list of constants of the given class
     *
     * @param $className
     * @param bool|false $reverse
     * @param bool|true $humanize
     * @return array
     * @throws Exception
     */
    function get_class_constants($className, $reverse = false, $humanize = true)
    {
        if (!class_exists($className)) {
            throw new Exception(sprintf('%s class does not exist', $className));
        }

        $refl = new ReflectionClass($className);
        $constants = $refl->getConstants();

        if ($reverse) {
            $constants = array_flip($constants);

            array_walk($constants, function (&$val, $k) use ($humanize) {
                if ($humanize) {
                    $val = _humanize($val);
                }
            });
        }

        return $constants;
    }
}

if (!function_exists('get_class_constants_string')) {
    /**
     * @param $className
     * @return string
     * @throws Exception
     */
    function get_class_constants_string($className)
    {
        $constants = get_class_constants($className);

        return implode(',', $constants);
    }
}
