<?php

/**
 * Jidaikobo\A11yc\Validate\Check\NoticeImgExists
 */

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class NoticeImgExists extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'notice_img_exists', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'imgs', false, $context);
        if (! $ms[1]) {
            return;
        }

        $tstr = RuntimeConfig::langConst('A11YC_LANG_IMAGE', 'Image') . ' ' .
            sprintf(
                RuntimeConfig::langConst('A11YC_LANG_COUNT_ITEMS', '%s items'),
                count($ms[1])
            );
        \Jidaikobo\A11yc\ValidationRecorder::error($url, 'notice_img_exists', 0, '', $tstr, $runtime);
        static::addErrorToHtml($url, 'notice_img_exists');
    }
}
