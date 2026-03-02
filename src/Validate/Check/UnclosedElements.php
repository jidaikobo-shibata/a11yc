<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class UnclosedElements extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'unclosed_elements', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        preg_match_all("/\<([^\>\n]+?)\</i", $str, $tags);

        if (! $tags[0]) {
            return;
        }
        foreach ($tags[0] as $k => $m) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'unclosed_elements', $k, $m, $m, $runtime);
        }
        static::addErrorToHtml($url, 'unclosed_elements');
    }
}
