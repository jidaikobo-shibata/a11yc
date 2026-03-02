<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class HeaderlessSection extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'headerless_section', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section\>/is", $str, $secs);

        if (! $secs[0]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'headerless_section', null, 4, $runtime);
            return;
        }

        foreach ($secs[0] as $k => $v) {
            $tstr = Element\Get\Each::firstTag($v);

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                ! preg_match("/\<h\d/", $v),
                $url,
                'headerless_section',
                $k,
                $tstr,
                $tstr,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'headerless_section');
    }
}
