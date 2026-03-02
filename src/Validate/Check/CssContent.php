<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Util;
use Jidaikobo\A11yc\Validate;

class CssContent extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_is_meanfull_content', null, 5, $runtime);
        if (! static::shouldDoCssCheck($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_is_meanfull_content', null, 1, $runtime);

        $css_result = static::css($url, $runtime);
        if (empty($css_result->csses)) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_is_meanfull_content', null, 4, $runtime);
            return;
        }

        $is_exists = false;
        $k = 0;
        foreach ($css_result->csses as $each_csses) {
            foreach ($each_csses as $selector => $props) {
                if (! isset($props['content'])) {
                    continue;
                }
                $is_exists = true;
                if (in_array($props['content'], array("''", '""', '.', 'none', '"."', "'.'", '" "', "' '"))) {
                    continue;
                }
                if (in_array(substr($props['content'], 0, 2), array("'\\", '"\\'))) {
                    continue;
                }
                $tstr = $selector . ': ' . Util::s($props['content']);
                \Jidaikobo\A11yc\ValidationRecorder::error($url, 'css_is_meanfull_content', $k, '', $tstr, $runtime);
                $k++;
            }
        }

        if (! $is_exists) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_is_meanfull_content', null, 4, $runtime);
        }
    }
}
