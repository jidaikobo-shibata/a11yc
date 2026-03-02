<?php

namespace Jidaikobo\A11yc;

class Image
{
    public static function getImages($url, $base_uri = '', $target_html = '')
    {
        $retvals = array();
        $context = new ElementAnalysisContext();

        if ($target_html !== '') {
            Element\Get::setSourceHtml($url, $target_html, $context);
        }

        $str = Element\Get::ignoredHtml($url, false, $context);
        $n = 0;

        list($retvals, $str, $n) = self::imagesInA($n, $str, $retvals, $context);
        list($retvals, $str, $n) = self::getArea($n, $str, $retvals, $context);
        list($retvals, $str, $n) = self::getButton($n, $str, $retvals, $context);
        $retvals = self::getInput($n, $str, $retvals, $context);

        foreach ($retvals as $k => $v) {
            if (isset($v['attrs']['src'])) {
                $retvals[$k]['attrs']['src'] = Util::enuniqueUri($v['attrs']['src'], $base_uri);
            }

            $retvals = self::tidyAlt($k, $v, $retvals);

            $retvals[$k]['aria'] = array();
            foreach ($retvals[$k]['attrs'] as $kk => $vv) {
                if (substr($kk, 0, 5) != 'aria-') {
                    continue;
                }
                $retvals[$k]['aria'][$kk] = $vv;
            }
        }

        return $retvals;
    }

    private static function imagesInA($n, $str, $retvals, $context = null)
    {
        preg_match_all('/\<a [^\>]+\>.+?\<\/a\>/is', $str, $as);
        foreach ($as[0] as $v) {
            if (strpos($v, '<img ') === false) {
                continue;
            }

            $attrs       = Element\Get::attributes($v, $context);
            $href        = Arr::get($attrs, 'href');
            $aria_hidden = Arr::get($attrs, 'aria-hidden');
            $tabindex    = Arr::get($attrs, 'tabindex');
            $text_in_a   = Util::s(strip_tags($v));

            preg_match_all('/\<img[^\>]+\>/is', $v, $ass);

            foreach ($ass[0] as $vv) {
                $retvals[$n]['element'] = 'img (a)';
                $retvals[$n]['is_important'] = true;
                $retvals[$n]['href'] = $href;
                $retvals[$n]['aria_hidden'] = $aria_hidden;
                $retvals[$n]['tabindex'] = $tabindex;
                $retvals[$n]['attrs'] = Element\Get::attributes($vv, $context);
                $retvals[$n]['near_text'] = $text_in_a;
                $n++;
            }

            $str = str_replace($v, '', $str);
        }

        return array($retvals, $str, $n);
    }

    private static function getArea($n, $str, $retvals, $context = null)
    {
        preg_match_all('/\<area [^\>]+\>/is', $str, $as);
        foreach ($as[0] as $v) {
            $attrs = Element\Get::attributes($v, $context);
            $retvals[$n]['element'] = 'area';
            $retvals[$n]['is_important'] = true;
            $retvals[$n]['href'] = Arr::get($attrs, 'href');
            $retvals[$n]['attrs'] = $attrs;
            $n++;
            $str = str_replace($v, '', $str);
        }
        return array($retvals, $str, $n);
    }

    private static function getButton($n, $str, $retvals, $context = null)
    {
        preg_match_all('/\<button [^\>]+\>.+?\<\/button\>/is', $str, $as);
        foreach ($as[0] as $v) {
            if (strpos($v, '<img ') === false) {
                continue;
            }

            $attrs = Element\Get::attributes($v, $context);
            $aria_hidden = Arr::get($attrs, 'aria-hidden');
            $tabindex = Arr::get($attrs, 'tabindex');

            preg_match_all('/\<img[^\>]+\>/is', $v, $ass);
            foreach ($ass[0] as $vv) {
                $retvals[$n]['element'] = 'img (button)';
                $retvals[$n]['href'] = null;
                $retvals[$n]['is_important'] = 1;
                $retvals[$n]['aria_hidden'] = $aria_hidden;
                $retvals[$n]['tabindex'] = $tabindex;
                $retvals[$n]['attrs'] = Element\Get::attributes($vv, $context);
                $n++;
            }

            $str = str_replace($v, '', $str);
        }
        return array($retvals, $str, $n);
    }

    private static function getInput($n, $str, $retvals, $context = null)
    {
        $force = true;
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', $force, $context);

        if (! is_array($ms[1])) {
            return $retvals;
        }

        $targets = array('img', 'input');
        foreach ($ms[1] as $k => $v) {
            if (! in_array($v, $targets)) {
                continue;
            }
            $attrs = Element\Get::attributes($ms[0][$k], $context);
            if ($v == 'input' && ( ! isset($attrs['type']) || $attrs['type'] != 'image')) {
                continue;
            }

            $retvals[$n]['element'] = $v;
            $retvals[$n]['is_important'] = $v == 'input' ? true : false;
            $retvals[$n]['href'] = null;
            $retvals[$n]['attrs'] = $attrs;
            $n++;
        }
        return $retvals;
    }

    private static function tidyAlt($k, $v, $retvals)
    {
        if (isset($v['attrs']['alt'])) {
            if (empty($v['attrs']['alt'])) {
                $retvals[$k]['attrs']['alt'] = '';
            } else {
                $alt = str_replace('　', ' ', $v['attrs']['alt']);
                $alt = trim($alt);

                if (empty($alt)) {
                    $retvals[$k]['attrs']['alt'] = '===a11yc_alt_of_blank_chars===';
                } else {
                    $retvals[$k]['attrs']['alt'] = $v['attrs']['alt'];
                }
            }

            $retvals[$k]['attrs']['newline'] = preg_match("/[\n\r]/is", $v['attrs']['alt']);
        }
        return $retvals;
    }
}
