<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class InvalidTag extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'cannot_contain_newline', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'unbalanced_quotation', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'cannot_contain_multibyte_space',
            null,
            1,
            $runtime
        );
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $m) {
            $attrs = Element\Get::attributes($m, $context);
            $tstr = $ms[0][$k];

            foreach ($attrs as $val) {
                if (strpos($val, "\n") !== false) {
                    \Jidaikobo\A11yc\ValidationRecorder::error($url, 'cannot_contain_newline', $k, $tstr, $m, $runtime);
                    break;
                } else {
                    \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                        $url,
                        'cannot_contain_newline',
                        $tstr,
                        2,
                        $runtime
                    );
                }
            }

            $tag = str_replace(array("\\'", '\\"'), '', $m);

            if (substr_count($tag, '"') % 2 !== 0) {
                \Jidaikobo\A11yc\ValidationRecorder::error($url, 'unbalanced_quotation', $k, $tstr, $m, $runtime);
            } else {
                \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                    $url,
                    'unbalanced_quotation',
                    $tstr,
                    2,
                    $runtime
                );
            }

            if (RuntimeConfig::defaultLang() != 'ja') {
                continue;
            }

            $tag = preg_replace("/(\".+?\"|'.+?')/is", '', $tag);

            if (strpos($tag, '　') !== false) {
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'cannot_contain_multibyte_space',
                    $k,
                    $tstr,
                    $m,
                    $runtime
                );
            } else {
                \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                    $url,
                    'cannot_contain_multibyte_space',
                    $tstr,
                    2,
                    $runtime
                );
            }
        }
        static::addErrorToHtml($url, 'unbalanced_quotation');
        static::addErrorToHtml($url, 'cannot_contain_multibyte_space');
        static::addErrorToHtml($url, 'cannot_contain_newline');
    }
}
