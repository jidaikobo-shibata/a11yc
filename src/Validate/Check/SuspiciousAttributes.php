<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class SuspiciousAttributes extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'suspicious_attributes', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'duplicated_attributes', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $m) {
            $tstr = $ms[0][$k];
            $attrs = Element\Get::attributes($m, $context);

            $exp = isset($attrs['suspicious']);
            if ($exp) {
                \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                    $exp,
                    $url,
                    'suspicious_attributes',
                    $k,
                    $tstr,
                    join(', ', $attrs['suspicious']),
                    $runtime
                );
            }

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                isset($attrs['no_space_between_attributes']) && $attrs['no_space_between_attributes'],
                $url,
                'suspicious_attributes',
                $k,
                $tstr,
                $tstr,
                $runtime
            );

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                isset($attrs['plural']),
                $url,
                'duplicated_attributes',
                $k,
                $tstr,
                $m,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'suspicious_attributes');
        static::addErrorToHtml($url, 'duplicated_attributes');
    }
}
