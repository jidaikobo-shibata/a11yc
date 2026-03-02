<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class EmptyLinkElement extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'empty_link_element', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[1]) {
            return;
        }

        foreach ($ms[0] as $k => $m) {
            if (strpos($m, 'href') === false) {
                continue;
            }
            if (substr($m, 0, 5) === '<area') {
                continue;
            }

            $text = Element\Get\Each::textFromElement($ms[2][$k], $context);

            if (empty($text)) {
                $text = self::getAriaLabelledby($ms[0][$k], $str, $text, $context);
            }

            if (empty($text)) {
                $text = self::getAriaLabel($ms[0][$k], $text, $context);
            }

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                empty($text),
                $url,
                'empty_link_element',
                $k,
                $ms[0][$k],
                $ms[0][$k],
                $runtime
            );
        }
        static::addErrorToHtml($url, 'empty_link_element');
    }

    private static function getAriaLabelledby($eles, $str, $text = '', $context = null)
    {
        $text = is_bool($text) ? '' : $text;

        if (strpos($eles, 'aria-labelledby') !== false) {
            $eleses = explode('>', $eles);
            foreach ($eleses as $ele) {
                if (strpos($ele, 'aria-labelledby') === false) {
                    continue;
                }
                $attrs = Element\Get::attributes($ele . ">", $context);
                $ids = Arr::get($attrs, 'aria-labelledby');
                if (empty($ids)) {
                    continue;
                }

                foreach (explode(' ', $ids) as $id) {
                    $eachele = Element\Get::elementById($str, $id);
                    $text .= Element\Get\Each::textFromElement($eachele, $context);
                }
            }
        }
        return $text;
    }

    private static function getAriaLabel($eles, $text = '', $context = null)
    {
        $text = is_bool($text) ? '' : $text;

        if (strpos($eles, 'aria-label') !== false) {
            $eleses = explode('>', $eles);
            foreach ($eleses as $ele) {
                if (strpos($ele, 'aria-label') === false) {
                    continue;
                }
                if (! empty($text)) {
                    continue;
                }
                $attrs = Element\Get::attributes($ele . ">", $context);
                $text .= Arr::get($attrs, 'aria-label', '');
            }
        }

        return $text;
    }
}
