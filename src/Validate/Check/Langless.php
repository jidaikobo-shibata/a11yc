<?php

/**
 * Jidaikobo\A11yc\Validate\Check\Langless
 */

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class Langless extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'langless', null, 5, $runtime);
        if (Validate::isPartialRun($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'langless', null, 1, $runtime);

        preg_match_all(
            "/\<([a-zA-Z1-6]+?) +?([^\>]*?)[\/]*?\>|\<([a-zA-Z1-6]+?)[ \/]*?\>/i",
            Element\Get::rawHtml($url, $context),
            $ms
        );

        $has_langs = array();
        foreach ($ms[0] as $k => $v) {
            $attrs = Element\Get::attributes($v, $context);
            if (! isset($attrs['lang']) && ! isset($attrs['xml:lang'])) {
                continue;
            }
            $has_langs[0][$k] = $ms[0][$k];
            $has_langs[1][$k] = $ms[1][$k];
            $has_langs[2][$k] = $ms[2][$k];
            $has_langs[3][$k] = $attrs;
        }

        if (! isset($has_langs[1]) || ! in_array('html', $has_langs[1])) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'langless', 0, '', Arr::get($ms, '0.0'), $runtime);
            static::addErrorToHtml($url, 'langless');
            return;
        } else {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'langless', null, 2, $runtime);
        }

        $ietf_subtags = require(RuntimeConfig::rootPath() . '/resources/ietf_langs.php');

        foreach ($has_langs[3] as $k => $v) {
            $tstr = $ms[0][$k];
            $lang = isset($v['lang']) ? $v['lang'] : $v['xml:lang'];
            $ls = explode('-', $lang);
            if (! array_key_exists(strtolower($ls[0]), $ietf_subtags)) {
                if ($has_langs[1][$k] == 'html') {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'invalid_page_lang',
                        $k,
                        $tstr,
                        $tstr,
                        $runtime
                    );
                } else {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'invalid_partial_lang',
                        $k,
                        $tstr,
                        $tstr,
                        $runtime
                    );
                }
            }
        }
        static::addErrorToHtml($url, 'invalid_page_lang');
        static::addErrorToHtml($url, 'invalid_partial_lang');
    }
}
