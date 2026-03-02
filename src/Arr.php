<?php

namespace Jidaikobo\A11yc;

class Arr
{
    public static function value($var)
    {
        return ($var instanceof \Closure) ? $var() : $var;
    }

    public static function get($array, $key, $default = null)
    {
        if (! is_array($array) and ! $array instanceof \ArrayAccess) {
            return false;
        }

        if (is_null($key)) {
            return $array;
        }

        if (is_array($key)) {
            $return = array();
            foreach ($key as $k) {
                $return[$k] = static::get($array, $k, $default);
            }
            return $return;
        }

        is_object($key) and $key = (string) $key;
        is_bool($key) and $key = '';

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $key_part) {
            if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false) {
                if (! is_array($array) or ! array_key_exists($key_part, $array)) {
                    return static::value($default);
                }
            }

            $array = $array[$key_part];
        }

        return $array;
    }

    public static function set(&$array, $key, $value = null)
    {
        if (is_null($key)) {
            $array = $value;
            return;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::set($array, $k, $v);
            }
        } else {
            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (! isset($array[$key]) or ! is_array($array[$key])) {
                    $array[$key] = array();
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;
        }
    }
}
