<?php

/**
 * Jidaikobo\A11yc\Validate\Check\CheckDoctype
 */

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class CheckDoctype extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        if (Validate::isPartialRun($runtime)) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'check_doctype', null, 5, $runtime);
            return;
        }

        \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
            is_null(Element\Get\Each::doctype($url, $context)),
            $url,
            'check_doctype',
            0,
            '',
            'doctype not found',
            $runtime
        );
        static::addErrorToHtml($url, 'check_doctype');
    }
}
