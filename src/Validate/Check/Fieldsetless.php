<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class Fieldsetless extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'fieldsetless', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'legendless', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[1]) {
            return;
        }

        $radio_check_names = self::getRadioCheckNames($ms[0], $context);
        if (isset($radio_check_names[0]) && is_null($radio_check_names[0])) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'fieldsetless', null, 4, $runtime);
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'legendless', null, 4, $runtime);
            return;
        }

        preg_match_all('/\<fieldset\>(.+?)\<\/fieldset\>/is', $str, $mms);

        self::legendless($url, $mms[0], $runtime);
        $radio_check_names = self::eliminateRadioCheckNames($mms[0], $radio_check_names, $context);
        self::fieldsetless($url, $ms[0], $radio_check_names, $context, $runtime);

        static::addErrorToHtml($url, 'legendless');
        static::addErrorToHtml($url, 'fieldsetless');
    }

    private static function getRadioCheckNames($ms, $context = null)
    {
        $radio_check_names = array();
        foreach ($ms as $m) {
            $attrs = Element\Get::attributes($m, $context);
            if (
                Arr::get($attrs, 'type') == 'radio' ||
                Arr::get($attrs, 'type') == 'checkbox'
            ) {
                $radio_check_names[] = Arr::get($attrs, 'name');
            }
        }

        return array_unique($radio_check_names);
    }

    private static function eliminateRadioCheckNames($mms, $radio_check_names, $context = null)
    {
        foreach ($mms as $mm) {
            $mm_mod = Element::ignoreElementsByStr($mm);
            preg_match_all('/\<[^\/].+?\>/', $mm_mod, $eles);

            foreach ($radio_check_names as $erase_key => $radio_check_name) {
                foreach ($eles[0] as $ele) {
                    $attrs = Element\Get::attributes($ele, $context);
                    if (Arr::get($attrs, 'name') == $radio_check_name) {
                        unset($radio_check_names[$erase_key]);
                        break;
                    }
                }
            }
        }
        return $radio_check_names;
    }

    private static function legendless($url, $mms, $runtime = null)
    {
        foreach ($mms as $k => $mm) {
            if (strpos($mm, '</legend>') === false) {
                $tstr = mb_substr($mm, 0, mb_strpos($mm, '>') + 1);
                \Jidaikobo\A11yc\ValidationRecorder::error($url, 'legendless', $k, $tstr, $tstr, $runtime);
            }
        }
    }

    private static function fieldsetless(
        $url,
        $ms,
        $radio_check_names,
        $context = null,
        $runtime = null
    ) {
        foreach ($radio_check_names as $radio_check_name) {
            foreach ($ms as $k => $tstr) {
                $attrs = Element\Get::attributes($tstr, $context);
                if (Arr::get($attrs, 'name') == $radio_check_name) {
                    \Jidaikobo\A11yc\ValidationRecorder::error($url, 'fieldsetless', $k, $tstr, $tstr, $runtime);
                    break;
                }
            }
        }
    }
}
