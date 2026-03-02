<?php

namespace Jidaikobo\A11yc;

final class RuntimeConfig
{
    public static function rootPath()
    {
        return dirname(__DIR__);
    }

    public static function defaultLang()
    {
        return defined('A11YC_LANG') ? A11YC_LANG : 'ja';
    }

    public static function resourcePath()
    {
        if (defined('A11YC_RESOURCE_PATH')) {
            return A11YC_RESOURCE_PATH;
        }
        return self::rootPath() . '/resources/' . self::defaultLang();
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
}
