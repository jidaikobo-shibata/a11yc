<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class DuplicatedIdsAndAccesskey extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'duplicated_ids', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'duplicated_accesskeys', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        $is_exists_id = false;
        $is_exists_ack = false;
        $ids = array();
        $accesskeys = array();
        foreach ($ms[0] as $k => $m) {
            $attrs = Element\Get::attributes($m, $context);
            $tstr = $ms[0][$k];

            if (isset($attrs['id'])) {
                $is_exists_id = true;
                if (in_array($attrs['id'], $ids)) {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'duplicated_ids',
                        $k,
                        $tstr,
                        $attrs['id'],
                        $runtime
                    );
                }
                $ids[] = $attrs['id'];
            }

            if (isset($attrs['accesskey'])) {
                $is_exists_ack = true;
                if (in_array($attrs['accesskey'], $accesskeys)) {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'duplicated_accesskeys',
                        $k,
                        $tstr,
                        $attrs['accesskey'],
                        $runtime
                    );
                }
                $accesskeys[] = $attrs['accesskey'];
            }
        }

        $duplicated_ids_flag = $is_exists_id ? 3 : 4;
        $duplicated_accesskeys_flag = $is_exists_ack ? 3 : 4;
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'duplicated_ids',
            null,
            $duplicated_ids_flag,
            $runtime
        );
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'duplicated_accesskeys',
            null,
            $duplicated_accesskeys_flag,
            $runtime
        );

        static::addErrorToHtml($url, 'duplicated_ids');
        static::addErrorToHtml($url, 'duplicated_accesskeys');
    }
}
