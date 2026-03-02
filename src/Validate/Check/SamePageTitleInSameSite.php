<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Validate;

class SamePageTitleInSameSite extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'same_page_title_in_same_site',
            null,
            5,
            $runtime
        );
        return;
    }
}
