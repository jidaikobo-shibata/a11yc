<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class MetaRefresh extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'meta_refresh', null, 5, $runtime);
        if (Validate::isPartialRun($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'meta_refresh', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $v) {
            if ($ms[1][$k] != 'meta') {
                continue;
            }
            $attrs = Element\Get::attributes($v, $context);

            if (! array_key_exists('http-equiv', $attrs)) {
                continue;
            }
            if (! array_key_exists('content', $attrs)) {
                continue;
            }
            if ($attrs['http-equiv'] !== 'refresh') {
                continue;
            }

            $tstr = $ms[0][$k];
            $content = $attrs['content'];
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                trim(substr($content, 0, strpos($content, ';'))) != '0' ||
                (strpos($content, ';') === false && trim($content) != '0'),
                $url,
                'meta_refresh',
                0,
                $tstr,
                $tstr,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'meta_refresh');
    }
}
