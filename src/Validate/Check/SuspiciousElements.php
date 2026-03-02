<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class SuspiciousElements extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'too_much_opens', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'too_much_ends', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'suspicious_ends', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        preg_match_all("/\<([^\> \n]+)/i", $str, $tags);

        $endless = array(
            'img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area',
            'embed', 'meta', 'link', 'track', 'source', 'col', 'command',
            'frame', 'keygen'
        );
        $ignores = array('!doctype', 'html', '![if', '![endif]', '?xml');
        $omissionables = array(
            'li', 'dt', 'dd', 'p', 'rt', 'rp', 'optgroup', 'option', 'tr',
            'td', 'th', 'thead', 'tfoot', 'tbody', 'colgroup', 'path',
            'rect', 'line', 'polygon', 'circle', 'ellipse', 'text', 'use',
            'image'
        );
        $ignores = array_merge($ignores, $endless, $omissionables);

        list($too_much_opens, $too_much_ends) = self::countTags($tags, $ignores);
        $suspicious_ends = self::suspiciousEnds($endless, $str);

        foreach ($too_much_opens as $k => $v) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'too_much_opens', $k, '', $v, $runtime);
        }
        static::addErrorToHtml($url, 'too_much_opens');

        foreach ($too_much_ends as $k => $v) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'too_much_ends', $k, '', $v, $runtime);
        }
        static::addErrorToHtml($url, 'too_much_ends');

        foreach ($suspicious_ends as $k => $v) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'suspicious_ends', $k, '', $v, $runtime);
        }
        static::addErrorToHtml($url, 'suspicious_ends');
    }

    public static function countTags($tags, $ignores)
    {
        $tags = is_null($tags) ? array() : $tags;

        $opens = array();
        $ends = array();
        foreach ($tags[1] as $tag) {
            $tag = strtolower($tag);
            $tag = rtrim($tag, '/');
            if (in_array($tag, $ignores)) {
                continue;
            }
            if (in_array(substr($tag, 1), $ignores)) {
                continue;
            }

            if ($tag[0] == '/') {
                $ends[] = substr($tag, 1);
                continue;
            }
            $opens[] = $tag;
        }

        $opens_cnt = array_count_values($opens);
        $ends_cnt = array_count_values($ends);

        $too_much_opens = array();
        $too_much_ends = array();
        foreach (array_keys($opens_cnt) as $tag) {
            if (! isset($ends_cnt[$tag]) || $opens_cnt[$tag] > $ends_cnt[$tag]) {
                $too_much_opens[] = $tag;
            } elseif ($opens_cnt[$tag] < $ends_cnt[$tag]) {
                $too_much_ends[] = $tag;
            }
        }
        return array($too_much_opens, $too_much_ends);
    }

    public static function suspiciousEnds($endless, $str)
    {
        $suspicious_ends = array();
        foreach ($endless as $v) {
            if (strpos($str, '</' . $v . '>') !== false) {
                $suspicious_ends[] = '/' . $v;
            }
        }
        return $suspicious_ends;
    }
}
