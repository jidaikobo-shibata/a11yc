<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Validate;

class AppropriateHeadingDescending extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'appropriate_heading_descending',
            null,
            1,
            $runtime
        );
        $str = \Jidaikobo\A11yc\Element\Get::ignoredHtml($url, false, $context);

        $secs = preg_split("/\<(h[^\>?]+?)\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (! $secs[0]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'appropriate_heading_descending',
                null,
                4,
                $runtime
            );
            return;
        }

        $prev = 1;
        foreach ($secs as $sec) {
            if (isset($sec[1]) && is_numeric($sec[1])) {
                $prev = $sec[1];
                break;
            }
        }

        foreach ($secs as $k => $v) {
            if ($v[0] != 'h' || ! is_numeric($v[1])) {
                continue;
            }
            $current_level = $v[1];
            $tstr = '<' . $v . '>';

            if ($current_level - $prev >= 2) {
                $str = isset($secs[$k + 1]) ? $secs[$k + 1] : $v[1];
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'appropriate_heading_descending',
                    $k,
                    $tstr,
                    $str,
                    $runtime
                );
            } else {
                \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                    $url,
                    'appropriate_heading_descending',
                    $tstr,
                    2,
                    $runtime
                );
            }
            $prev = $current_level;
        }

        static::addErrorToHtml($url, 'appropriate_heading_descending');
    }
}
