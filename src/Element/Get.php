<?php

namespace Jidaikobo\A11yc\Element;

use Jidaikobo\A11yc\ElementAnalysisContext;
use Jidaikobo\A11yc\Element\Get\Each;
use Jidaikobo\A11yc\Fetcher as CoreFetcher;

class Get extends Element
{
    private static function resolveContext($url, $context = null)
    {
        if ($context instanceof ElementAnalysisContext) {
            return $context;
        }

        return new ElementAnalysisContext();
    }

    public static function setSourceHtml($url, $html, $context = null)
    {
        $context = static::resolveContext($url, $context);
        $context->sourceHtml = (string) $html;
        $context->ignoredHtml = null;
        $context->lang = null;
        $context->langResolved = false;
    }

    public static function rawHtml($url, $context = null)
    {
        $context = static::resolveContext($url, $context);
        if ($context->sourceHtml !== '') {
            return $context->sourceHtml;
        }

        $context->sourceHtml = (new CoreFetcher())->fetchBody($url);
        return $context->sourceHtml;
    }

    public static function ignoredHtml($url, $force = false, $context = null)
    {
        $context = static::resolveContext($url, $context);
        if (is_string($context->ignoredHtml) && ! $force) {
            return $context->ignoredHtml;
        }

        $str = self::ignoreElementsByStr(static::rawHtml($url, $context));
        $context->ignoredHtml = $str;
        return $context->ignoredHtml;
    }

    public static function cachedLang($url, $context = null)
    {
        $context = static::resolveContext($url, $context);
        if (! $context->langResolved) {
            return null;
        }

        return $context->lang;
    }

    public static function setCachedLang($url, $lang, $context = null)
    {
        $context = static::resolveContext($url, $context);
        $context->lang = $lang;
        $context->langResolved = true;
    }

    public static function attributes($str, $context = null)
    {
        if ($context instanceof ElementAnalysisContext) {
            if (isset($context->attributes[$str])) {
                return $context->attributes[$str];
            }
        }
        $keep = $str;

        $str = Each::firstTag($str);
        list($str, $suspicious_end_quote, $no_space_between_attributes) = self::prepareStrings($str);
        $attrs = self::explodeStrings($str);
        $attrs['suspicious_end_quote'] = $suspicious_end_quote;
        $attrs['no_space_between_attributes'] = $no_space_between_attributes;
        if ($context instanceof ElementAnalysisContext) {
            $context->attributes[$keep] = $attrs;
        }

        return $attrs;
    }

    public static function elementsByRe($str, $ignore_type, $type = 'tags', $force = false, $context = null)
    {
        $cache_key = hash('sha256', (string) $str) . '|' . $ignore_type . '|' . $type;
        if ($context instanceof ElementAnalysisContext) {
            if (isset($context->elementMatches[$cache_key]) && $force === false) {
                return $context->elementMatches[$cache_key];
            }
        }
        $ret = self::decideRe($str, $type);

        if (isset($ret[1]) && $type == 'imgs') {
            $ret = self::prepareForImage($ret);
        }

        if ($force) {
            return $ret;
        }
        if ($context instanceof ElementAnalysisContext) {
            $context->elementMatches[$cache_key] = $ret;
            return $context->elementMatches[$cache_key];
        }

        return $ret;
    }

    private static function decideRe($str, $type)
    {
        switch ($type) {
            case 'anchors':
                return self::anchors($str);
            case 'anchors_and_values':
                return self::anchorsAndValues($str);
            default:
                return self::tags($str);
        }
    }

    private static function prepareForImage($ret)
    {
        foreach ($ret[1] as $k => $v) {
            if (strtolower($v) != 'img') {
                unset($ret[0][$k], $ret[1][$k], $ret[2][$k]);
            }
        }
        return $ret;
    }

    private static function anchors($str)
    {
        $ret = array(0 => array(), 1 => array(), 2 => array());
        if (preg_match_all("/\<(?:a|area) ([^\>]+?)\>/i", $str, $ms)) {
            $ret = $ms;
        }
        return $ret;
    }

    private static function anchorsAndValues($str)
    {
        $ret = array(0 => array(), 1 => array(), 2 => array(), 3 => array());
        if (preg_match_all("/\<a ([^\>]+)\>(.*?)\<\/a\>|\<area ([^\>]+?)\/\>/si", $str, $ms)) {
            $ret = $ms;
        }
        return $ret;
    }

    private static function tags($str)
    {
        $ret = array(0 => array(), 1 => array(), 2 => array());
        if (preg_match_all('/\<[^\/]("[^"]*"|\'[^\']*\'|[^\'">])*\>/is', $str, $ms)) {
            foreach ($ms[0] as $k => $v) {
                $ret[0][$k] = $v;
                if (strpos($v, ' ') !== false) {
                    $ret[1][$k] = mb_substr($v, 1, mb_strpos($v, ' ') - 1);
                    $ret[2][$k] = mb_substr($v, mb_strpos($v, ' '), -1);
                } else {
                    $ret[1][$k] = mb_substr($v, 1, -1);
                    $ret[2][$k] = '';
                }
            }
        }
        return $ret;
    }

    public static function elementById($str, $id)
    {
        $pattern = '/\<([^\>]+?) [^\>]*?id *?\= *?[\'"]' . $id . '[\'"].*?\>/ism';
        preg_match($pattern, $str, $ms);
        if (empty($ms)) {
            return false;
        }

        $start = preg_quote($ms[0]);
        $elename = $ms[1];
        $end = '\<\/' . $elename . '\>';
        $end_pure = '</' . $elename . '>';

        if (! preg_match('/' . $start . '.+' . $end . '/ism', $str, $mms)) {
            return false;
        }
        $target = $mms[0];
        $close = self::getClosePos($target, $elename, $end_pure);
        if (! $close) {
            return false;
        }

        $target = mb_substr($target, 0, $close) . $end_pure;
        return $target;
    }

    private static function getClosePos($target, $elename, $end_pure)
    {
        $loop = true;
        $open_pos = 1;
        $close_pos = 1;
        $close = 0;
        $failsafe = 0;

        while ($loop) {
            $failsafe++;
            if ($failsafe >= 100) {
                $loop = false;
            }

            $open = mb_strpos($target, '<' . $elename, $open_pos);
            $close = mb_strpos($target, $end_pure, $close_pos);

            if (! $open) {
                break;
            }

            if ($open < $close) {
                $open_pos = $open + 1;
                $close_pos = $close + 1;
                continue;
            }

            $loop = false;
        }
        return $close;
    }
}
