<?php

namespace Jidaikobo\A11yc\Element\Get;

class Each
{
    public static function firstTag($str)
    {
        $str = trim($str);
        if (strpos($str, '<') === false) {
            return '';
        }

        preg_match('/\<("[^"]*"|\'[^\']*\'|[^\'">])*\>/is', $str, $mms);
        if (! isset($mms[0])) {
            return '';
        }
        $str = $mms[0];
        $str = str_replace('/>', ' />', $str);
        return $str;
    }

    public static function textFromElement($str, $context = null)
    {
        $text = '';

        if (strpos($str, 'img') !== false) {
            $imgs = explode('>', $str);
            foreach ($imgs as $img) {
                if (strpos($img, 'img') === false) {
                    continue;
                }
                $attrs = \Jidaikobo\A11yc\Element\Get::attributes($img . ">", $context);
                foreach ($attrs as $kk => $vv) {
                    if (strpos($kk, 'alt') !== false) {
                        $text .= $vv;
                    }
                }
            }
            $text = trim($text);
        }

        $text = strip_tags($str) . $text;
        $text = trim($text);
        return $text;
    }

    public static function doctype($url, $context = null)
    {
        $html = \Jidaikobo\A11yc\Element\Get::rawHtml($url, $context);
        if ($html === '') {
            return false;
        }
        preg_match("/\<!DOCTYPE [^\>]+?\>/is", $html, $ms);
        if (! isset($ms[0])) {
            return false;
        }

        $doctype = null;
        $target_str = strtolower(str_replace(array("\n", ' '), '', $ms[0]));

        if (strpos($target_str, 'doctypehtml>') !== false) {
            $doctype = 'html5';
        } elseif (strpos($target_str, 'dtdhtml4.0') !== false) {
            $doctype = 'html4';
        } elseif (strpos($target_str, 'dtdxhtml1') !== false) {
            $doctype = 'xhtml1';
        }

        return $doctype;
    }

    public static function lang($url, $context = null)
    {
        $cached = \Jidaikobo\A11yc\Element\Get::cachedLang($url, $context);
        if ($cached !== null) {
            return $cached;
        }
        $html = \Jidaikobo\A11yc\Element\Get::rawHtml($url, $context);
        if ($html === '') {
            return '';
        }

        preg_match("/\<html ([^\>]+?)\>/is", $html, $ms);
        if (! isset($ms[0])) {
            return '';
        }

        $attrs = \Jidaikobo\A11yc\Element\Get::attributes($ms[0], $context);
        if (! isset($attrs['lang'])) {
            \Jidaikobo\A11yc\Element\Get::setCachedLang($url, '', $context);
            return '';
        }
        \Jidaikobo\A11yc\Element\Get::setCachedLang($url, $attrs['lang'], $context);
        return $attrs['lang'];
    }
}
