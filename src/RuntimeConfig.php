<?php

namespace Jidaikobo\A11yc;

final class RuntimeConfig
{
    /** @var array<int, array<string, string>> */
    private static $overrideStack = array();

    public static function rootPath()
    {
        return dirname(__DIR__);
    }

    public static function defaultLang()
    {
        $lang = self::overrideValue('lang');
        if ($lang !== '') {
            return $lang;
        }

        return defined('A11YC_LANG') ? A11YC_LANG : 'en';
    }

    public static function resourcePath()
    {
        $resource_path = self::overrideValue('resource_path');
        if ($resource_path !== '') {
            return $resource_path;
        }

        if (defined('A11YC_RESOURCE_PATH')) {
            return A11YC_RESOURCE_PATH;
        }
        return self::rootPath() . '/resources/' . self::defaultLang();
    }

    public static function docResourcePath()
    {
        $doc_resource_path = self::overrideValue('doc_resource_path');
        if ($doc_resource_path !== '') {
            return $doc_resource_path;
        }

        return defined('A11YC_DOC_RESOURCE_PATH') ? A11YC_DOC_RESOURCE_PATH : '';
    }

    public static function docUrl()
    {
        return defined('A11YC_DOC_URL') ? A11YC_DOC_URL : '';
    }

    public static function refWcag20Url()
    {
        return defined('A11YC_REF_WCAG20_URL') ? A11YC_REF_WCAG20_URL : 'https://www.w3.org/TR/WCAG20/';
    }

    public static function refWcag20UnderstandingUrl()
    {
        return defined('A11YC_REF_WCAG20_UNDERSTANDING_URL') ?
            A11YC_REF_WCAG20_UNDERSTANDING_URL :
            'https://www.w3.org/TR/UNDERSTANDING-WCAG20/';
    }

    public static function refWcag20TechUrl()
    {
        return defined('A11YC_REF_WCAG20_TECH_URL') ? A11YC_REF_WCAG20_TECH_URL : 'https://www.w3.org/TR/WCAG20-TECHS/';
    }

    public static function langConst($name, $default = '')
    {
        return defined($name) ? constant($name) : $default;
    }

    public static function withOverrides(array $overrides, callable $callback)
    {
        $filtered = array();

        foreach (array('lang', 'resource_path', 'doc_resource_path') as $key) {
            if (! array_key_exists($key, $overrides)) {
                continue;
            }

            $value = $overrides[$key];
            if (! is_string($value) || $value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        if ($filtered === array()) {
            return $callback();
        }

        self::$overrideStack[] = $filtered;

        try {
            return $callback();
        } finally {
            array_pop(self::$overrideStack);
        }
    }

    private static function overrideValue(string $name): string
    {
        if (self::$overrideStack === array()) {
            return '';
        }

        $current = self::$overrideStack[count(self::$overrideStack) - 1];
        if (! array_key_exists($name, $current)) {
            return '';
        }

        return $current[$name];
    }
}
