<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Validate;

class CssTotal extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        $error_names = array(
            'css_suspicious_paren_num',
            'css_suspicious_props',
            'css_suspicious_prop_and_vals',
            'css_suspicious_prop_and_vals',
        );

        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 5, $runtime);
        if (! static::shouldDoCssCheck($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 1, $runtime);

        $css_result = static::css($url, $runtime);
        if (empty($css_result->csses)) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 4, $runtime);
            return;
        }

        \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
            $css_result->is_suspicious_paren_num,
            $url,
            'css_suspicious_paren_num',
            0,
            '',
            '',
            $runtime
        );

        self::serErrorOrLog(
            $css_result->suspicious_props,
            $url,
            'css_suspicious_props',
            '',
            $runtime
        );

        self::serErrorOrLog(
            $css_result->suspicious_prop_and_vals,
            $url,
            'css_suspicious_prop_and_vals',
            '',
            $runtime
        );

        foreach ($css_result->suspicious_val_prop as $k => $prop) {
            \Jidaikobo\A11yc\ValidationRecorder::error(
                $url,
                'css_suspicious_prop_and_vals',
                $k,
                '',
                join(':', $prop),
                $runtime
            );
        }
        if (
            static::machineCheckStatus(
                $url,
                'css_suspicious_prop_and_vals',
                null,
                $runtime,
                null
            ) != -1
        ) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'css_suspicious_prop_and_vals',
                null,
                2,
                $runtime
            );
        }
    }

    private static function serErrorOrLog($props, $url, $error_name, $id, $runtime = null)
    {
        foreach ($props as $count => $prop) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, $error_name, $count, $id, $prop, $runtime);
        }
        if (static::machineCheckStatus($url, $error_name, null, $runtime, null) != -1) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_name, null, 2, $runtime);
        }
    }
}
