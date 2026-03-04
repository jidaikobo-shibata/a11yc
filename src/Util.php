<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Values;
use Jidaikobo\A11yc\Yaml;

class Util
{
    public static function uri()
    {
        $http_host = Input::server('HTTP_HOST');
        $request_uri = Input::server('REQUEST_URI');

        if ($http_host && $request_uri) {
            $uri = static::isSsl() ? 'https' : 'http';
            $uri .= '://' . $http_host . rtrim($request_uri, '/');
            return static::s($uri);
        }
        return '';
    }

    public static function addQueryStrings($uri, $query_strings = array())
    {
        $delimiter = strpos($uri, '?') !== false ? '&amp;' : '?';
        $qs = array();
        foreach ($query_strings as $v) {
            $qs[] = $v[0] . '=' . $v[1];
        }
        return $uri . $delimiter . join('&amp;', $qs);
    }

    public static function removeQueryStrings($uri, $query_strings = array())
    {
        if (strpos($uri, '?') !== false) {
            $query_strings = $query_strings ?: array_keys($_GET);
            $uri = str_replace('&amp;', '&', $uri);
            $pos = strpos($uri, '?');
            $base_url = substr($uri, 0, $pos);
            $qs = explode('&', substr($uri, $pos + 1));
            foreach ($qs as $k => $v) {
                foreach ($query_strings as $vv) {
                    if (substr($v, 0, strpos($v, '=')) == $vv) {
                        unset($qs[$k]);
                    }
                }
            }
            $uri = $qs ? $base_url . '?' . join('&amp;', $qs) : $base_url;
        }
        return $uri;
    }

    public static function isSsl()
    {
        return (Input::server('HTTPS') == 'on');
    }

    public static function s($str)
    {
        if (is_bool($str)) {
            return $str;
        }
        if (is_object($str)) {
            return $str;
        }
        if (is_array($str)) {
            return array_map(array(__CLASS__, 's'), $str);
        }
        return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
    }

    public static function truncate($str, $len, $lead = '...')
    {
        $target_len = mb_strlen($str);
        return $target_len > $len ? mb_substr($str, 0, $len) . $lead : $str;
    }

    public static function urlenc($url)
    {
        $url = str_replace(array("\n", "\r"), '', $url);
        $url = static::s($url);
        $url = str_replace(' ', '%20', $url);
        if (strpos($url, '%') === false) {
            $url = urlencode($url);
        } else {
            $url = str_replace('://', '%3A%2F%2F', $url);
        }
        return $url;
    }

    public static function urldec($url)
    {
        $url = str_replace(array("\n", "\r"), '', $url);
        $url = trim($url);
        $url = rtrim($url, '/');
        $url = static::urlenc($url);
        $url = urldecode($url);
        $url = str_replace('&amp;', '&', $url);
        return $url;
    }

    public static function redirect($url)
    {
        $url = self::urldec($url);
        if (strpos($url, Input::server('HTTP_HOST')) === false) {
            self::error();
        }
        header('location: ' . $url);
        exit();
    }

    public static function error($message = '')
    {
        if (! headers_sent()) {
            header('Content-Type: text/plain; charset=UTF-8', true, 403);
        }
        die(self::s($message));
    }

    public static function byte2Str($bytes)
    {
        if (! is_numeric($bytes)) {
            return $bytes;
        }

        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 1) . ' KB';
        } elseif ($bytes === 0) {
            $bytes = '0 bytes';
        } else {
            $bytes .= $bytes == 1 ? ' byte' : ' bytes';
        }
        return $bytes;
    }

    public static function multisort($array, $by = 'seq', $order = 'asc')
    {
        $order = strtolower($order) == 'asc' ? SORT_ASC : SORT_DESC;

        $keys = array();
        foreach ($array as $key => $value) {
            if (! isset($array[$key])) {
                return $array;
            }
            $keys[$key] = Arr::get($value, $by);
        }
        array_multisort($keys, $order, $array);

        return $array;
    }

    public static function keyByColumn($arr, $column = 'id')
    {
        reset($arr);
        if (! isset($arr[key($arr)][$column])) {
            return array();
        }
        $vals = array();
        foreach ($arr as $v) {
            $id = $v[$column];
            unset($v[$column]);
            $vals[$id] = $v;
        }
        return $vals;
    }

    public static function searchWords2Arr($word)
    {
        $word = mb_convert_kana($word, 'asKV');
        $words = array();
        foreach (explode(' ', $word) as $v) {
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            $words[] = $v;
        }
        return $words;
    }

    public static function enuniqueUri($uri, $base_uri = '')
    {
        if (empty($uri)) {
            return '';
        }
        $base_url = $base_uri;
        if (empty($base_url) && defined('A11YC_URL')) {
            $base_url = (new UrlResolver())->hostFromUrl(A11YC_URL);
        }
        if (empty($base_url)) {
            return self::urldec($uri);
        }

        if (strlen($uri) >= 2 && $uri[0] == '/' && $uri[1] != '/') {
            $uri = $base_url . $uri;
        } elseif (strlen($uri) >= 2 && substr($uri, 0, 2) == './') {
            $uri = $base_url . substr($uri, 1);
        } elseif (strlen($uri) >= 3 && substr($uri, 0, 3) == '../') {
            $strs = explode('../', $uri);
            $uri = $base_url . '/' . end($strs);
        } elseif (strlen($uri) >= 3 && substr($uri, 0, 2) == '//') {
            $strs = explode('//', $uri);
            $uri = substr($base_url, 0, strpos($base_url, ':')) . '://' . end($strs);
        } elseif (strpos($uri, 'http') !== 0) {
            $uri = $base_url . '/' . $uri;
        }

        return self::urldec($uri);
    }

    public static function docHtmlWhitelist($txt)
    {
        return str_replace(
            array(
                '&lt;code&gt;',
                '&lt;/code&gt;',
            ),
            array(
                '<code>',
                '</code>',
            ),
            $txt
        );
    }

    public static function num2str($num, $default = '-')
    {
        $num = intval($num);
        if ($num == -1) {
            return '';
        }
        return $num ? str_repeat('A', $num) : $default;
    }

    public static function key2code($str)
    {
        return str_replace('-', '.', $str);
    }

    public static function code2key($str)
    {
        return str_replace('.', '-', $str);
    }

    public static function key2link($text, $doc_url = '')
    {
        preg_match_all("/\\[[^\\]]+?\\]/", $text, $ms);

        if (! $ms[0]) {
            return $text;
        }

        $yml = Yaml::fetch();
        $doc_url = $doc_url ?: RuntimeConfig::docUrl();

        foreach ($ms[0] as $str) {
            $code = ltrim($str, '[');
            $code = rtrim($code, ']');
            $tech = preg_replace('/[\\.\\d]/', '', $code);
            $search = $str;
            $url = '';
            $label = '';

            if (is_numeric($code[0])) {
                $criterion = self::code2key($code);
                $url = $doc_url . self::s($criterion);
                $label = Arr::get($yml['criterions'][$criterion], 'name');
            } elseif (in_array($tech, Values::techsTypes())) {
                $url = RuntimeConfig::refWcag20TechUrl() . self::s($code);
                $label = Arr::get($yml, 'techs.' . $code . '.title', $code);
            }

            if (empty($url)) {
                continue;
            }
            $replace = '"<a href="' . $url . '">' . $label . '</a>"';
            $text = str_replace($search, $replace, $text);
        }

        return $text;
    }

    public static function criterionsOfLevels()
    {
        $yml = Yaml::fetch();
        $levels = array();
        foreach ($yml['levels'] as $v) {
            foreach ($yml['criterions'] as $criterion => $vv) {
                if ($vv['level']['name'] != $v['name']) {
                    continue;
                }
                $levels[$v['name']][] = $criterion;
            }
        }
        $levels['AA'] = array_merge($levels['AA'], $levels['A']);
        $levels['AAA'] = array_merge($levels['AAA'], $levels['AA']);
        return $levels;
    }

    public static function setMassage($succeed, $success_message = null, $error_message = null)
    {
        $success_message = $success_message ?: RuntimeConfig::langConst('A11YC_LANG_UPDATE_SUCCEED', 'Succeeded.');
        $error_message = $error_message ?: RuntimeConfig::langConst('A11YC_LANG_UPDATE_FAILED', 'Failed.');
        if ($succeed) {
            if (class_exists('\Kontiki\Session')) {
                call_user_func(array('\Kontiki\Session', 'add'), 'messages', 'messages', $success_message);
            }
            return;
        }
        if (class_exists('\Kontiki\Session')) {
            call_user_func(array('\Kontiki\Session', 'add'), 'messages', 'errors', $error_message);
        }
    }

    public static function setCounter($exp, $success = 0, $failure = 0)
    {
        if ($exp) {
            $success++;
        } else {
            $failure++;
        }
        return array($success, $failure);
    }
}
