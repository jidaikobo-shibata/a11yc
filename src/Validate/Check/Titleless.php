<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class Titleless extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'titleless', null, 5, $runtime);
        if (Validate::isPartialRun($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'titleless', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);

        if (
            strpos(strtolower($str), '<title') === false ||
            preg_match("/\<title[^\>]*?\>[ 　]*?\<\/title/si", $str)
        ) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'titleless', 0, '', Arr::get($ms[0], 0, ''), $runtime);
        } else {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'titleless', null, 2, $runtime);
        }

        static::addErrorToHtml($url, 'titleless');
    }
}
