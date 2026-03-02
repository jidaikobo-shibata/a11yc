<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class NoticeNonHtmlExists extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'notice_non_html_exists', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[1]) {
            return;
        }

        $suspicious = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx');
        $exists = array();

        foreach ($ms[1] as $m) {
            foreach ($suspicious as $vv) {
                $m = str_replace("'", '"', $m);
                if (strpos($m, '.' . $vv . '"') !== false) {
                    if (! isset($exists[$vv])) {
                        $exists[$vv] = 0;
                    }
                    $exists[$vv] = $exists[$vv] + 1;
                }
            }
        }

        if (! empty($exists)) {
            $err_strs = array();
            foreach ($exists as $ext => $times) {
                $err_strs[] = $ext . ' (' . sprintf(
                    RuntimeConfig::langConst('A11YC_LANG_COUNT_ITEMS', '%s items'),
                    $times
                ) . ')';
            }
            \Jidaikobo\A11yc\ValidationRecorder::error(
                $url,
                'notice_non_html_exists',
                0,
                '',
                join(', ', $err_strs),
                $runtime
            );
        }
        static::addErrorToHtml($url, 'notice_non_html_exists');
    }
}
