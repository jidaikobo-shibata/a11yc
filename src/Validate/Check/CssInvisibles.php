<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class CssInvisibles extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_invisible', null, 5, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_background_image_only', null, 5, $runtime);
        if (! static::shouldDoCssCheck($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_invisible', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_background_image_only', null, 1, $runtime);

        $css_result = static::css($url, $runtime);
        if (empty($css_result->csses)) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_invisible', null, 4, $runtime);
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'css_background_image_only',
                null,
                4,
                $runtime
            );
            return;
        }

        $is_exists_visible = false;
        $is_exists_bg = false;

        $k = 0;
        foreach ($css_result->csses as $each_csses) {
            foreach ($each_csses as $selector => $props) {
                $is_exists_visible = self::checkDisplayVisibility($url, $selector, $k, $props, $runtime);
                $is_exists_bg = self::checkBgImageWithoutBgColor($url, $selector, $k, $props, $runtime);
                $k++;
            }
        }

        if (! $is_exists_visible) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'css_invisible', null, 4, $runtime);
        }

        if (! $is_exists_bg) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'css_background_image_only',
                null,
                4,
                $runtime
            );
        }
    }

    private static function checkDisplayVisibility($url, $selector, $k, $props, $runtime = null)
    {
        $is_exists_visible = false;
        if (
            (isset($props['display']) && $props['display'] == 'none') ||
            (isset($props['visibility']) && $props['visibility'] == 'hidden')
        ) {
            $is_exists_visible = true;
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'css_invisible', $k, '', $selector, $runtime);
        }
        return $is_exists_visible;
    }

    private static function checkBgImageWithoutBgColor(
        $url,
        $selector,
        $k,
        $props,
        $runtime = null
    ) {
        $is_exists_bg = false;
        if (
            isset($props['background']) ||
            isset($props['background-image'])
        ) {
            $background = Arr::get($props, 'background', '');
            $background_image = Arr::get($props, 'background-image', '');

            if (
                strpos($background, 'url') !== false ||
                strpos($background_image, 'url') !== false
            ) {
                $is_exists_bg = true;
                if (
                    strpos($background, '#') === false &&
                    ! isset($props['background-color'])
                ) {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'css_background_image_only',
                        $k,
                        '',
                        $selector,
                        $runtime
                    );
                }
            }
        }
        return $is_exists_bg;
    }
}
