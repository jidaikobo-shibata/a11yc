<?php

namespace Jidaikobo\A11yc;

class Input
{
    public static function deleteNullByte($str = '')
    {
        return str_replace("\0", '', $str);
    }

    public static function referrer($default = '')
    {
        return static::server('HTTP_REFERER', $default);
    }

    public static function userAgent($default = '')
    {
        return static::server('HTTP_USER_AGENT', $default);
    }

    public static function isPostExists()
    {
        return (static::server('REQUEST_METHOD') == 'POST');
    }

    public static function param(
        $index,
        $default = null,
        $filter = FILTER_DEFAULT,
        $options = array()
    ) {
        $val = self::get($index, $default, $filter, $options);
        if (is_null($val) || empty($val)) {
            $val = self::post($index, $default, $filter, $options);
        }
        return $val ? $val : $default;
    }

    public static function get(
        $index,
        $default = null,
        $filter = FILTER_DEFAULT,
        $options = array()
    ) {
        $val = filter_input(INPUT_GET, $index, $filter, $options);
        $val = self::deleteNullByte($val);
        return $val ?: $default;
    }

    public static function getArr(
        $index,
        $default = array(),
        $filter = FILTER_DEFAULT
    ) {
        return static::get($index, $default, $filter, FILTER_REQUIRE_ARRAY);
    }

    public static function post(
        $index,
        $default = null,
        $filter = FILTER_DEFAULT,
        $options = array()
    ) {
        $val = filter_input(INPUT_POST, $index, $filter, $options);
        $val = self::deleteNullByte($val);
        return $val ?: $default;
    }

    public static function postArr(
        $index,
        $default = array(),
        $filter = FILTER_DEFAULT
    ) {
        return static::post($index, $default, $filter, FILTER_REQUIRE_ARRAY);
    }

    public static function cookie(
        $index,
        $default = null,
        $filter = FILTER_DEFAULT,
        $options = array()
    ) {
        $val = filter_input(INPUT_COOKIE, $index, $filter, $options);
        $val = self::deleteNullByte($val);
        return $val ?: $default;
    }

    public static function server(
        $index,
        $default = null,
        $filter = FILTER_DEFAULT,
        $options = array()
    ) {
        $val = filter_input(INPUT_SERVER, $index, $filter, $options);

        if (! $val) {
            $val = filter_input(INPUT_ENV, $index, $filter, $options);
        }

        if ($val == null && isset($_SERVER[$index])) {
            $val = $_SERVER[$index];
        }
        return $val ? $val : $default;
    }

    public static function file($index = null, $default = null)
    {
        $files = $_FILES;

        if (func_num_args() === 0) {
            return $files;
        }

        if (! is_null($index) && isset($files[$index])) {
            return $files[$index];
        }

        return $default;
    }
}
