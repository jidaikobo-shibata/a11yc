<?php

namespace Jidaikobo\A11yc;

class Lang
{
    public static function getLangs($dir)
    {
        static $langs = array();
        if (! empty($langs)) {
            return $langs;
        }
        $langs = array_map('basename', glob($dir . '/*'));
        return $langs;
    }

    public static function getLang()
    {
        $lang_path = defined('A11YC_LANG_PATH') ? A11YC_LANG_PATH : '';
        if (empty($lang_path) || ! is_dir($lang_path)) {
            return RuntimeConfig::defaultLang();
        }
        $langs = self::getLangs($lang_path);
        $base_url = defined('A11YC_URL') ? dirname(A11YC_URL) : '';
        $requests = explode('/', substr(Util::uri(), strlen($base_url) + 1));

        $lang = '';
        if (in_array(Arr::get($requests, 0), $langs)) {
            $lang = Arr::get($requests, 0);
        }

        if (empty($lang) && count($requests) < 2) {
            $lang = RuntimeConfig::defaultLang();
        }
        return $lang;
    }
}
