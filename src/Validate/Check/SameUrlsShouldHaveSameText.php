<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Util;
use Jidaikobo\A11yc\Validate;

class SameUrlsShouldHaveSameText extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'same_urls_should_have_same_text',
            null,
            1,
            $runtime
        );

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[1]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'same_urls_should_have_same_text',
                null,
                4,
                $runtime
            );
            return;
        }

        $urls = array();
        foreach ($ms[1] as $k => $v) {
            if (Element::isIgnorable($ms[0][$k])) {
                continue;
            }

            $attrs = Element\Get::attributes($v, $context);

            if (! isset($attrs['href'])) {
                continue;
            }
            $each_url = Util::enuniqueUri($attrs['href']);

            $text = $ms[2][$k];
            preg_match_all("/\<\w+ +?[^\>]*?alt *?= *?[\"']([^\"']*?)[\"'][^\>]*?\>/", $text, $mms);
            if ($mms) {
                foreach (array_keys($mms[0]) as $kk) {
                    $text = str_replace($mms[0][$kk], $mms[1][$kk], $text);
                }
            }
            $text = strip_tags($text);
            $text = trim($text);
            $text = preg_replace("/\s{2,}/i", ' ', $text);
            $tstr = $ms[0][$k];

            if (! array_key_exists($each_url, $urls)) {
                $urls[$each_url] = $text;
                \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                    $url,
                    'same_urls_should_have_same_text',
                    $tstr,
                    3,
                    $runtime
                );
            } elseif ($urls[$each_url] != $text) {
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'same_urls_should_have_same_text',
                    $k,
                    $tstr,
                    $each_url . ': (' . mb_strlen($urls[$each_url], "UTF-8") .
                    ') "' . $urls[$each_url] . '" OR (' .
                    mb_strlen($text, "UTF-8") . ') "' . $text . '"',
                    $runtime
                );
            }
        }
        static::addErrorToHtml($url, 'same_urls_should_have_same_text');
    }
}
