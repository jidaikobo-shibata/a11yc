<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class MeanlessElement extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'meanless_element', null, 1, $runtime);
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'meanless_element_timing', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $banneds = array(
            'big', 'tt', 'center', 'font', 'blink', 'marquee',
            'i', 'u', 's', 'strike', 'basefont',
        );

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        $n = 0;
        foreach ($ms[0] as $m) {
            foreach ($banneds as $banned) {
                preg_match_all('/\<' . $banned . ' [^\>]*?\>|\<' . $banned . '\>/', $m, $mms);
                if (! $mms[0]) {
                    continue;
                }
                foreach ($mms[0] as $tag) {
                    if (strpos($tag, '<blink') !== false || strpos($tag, '<marquee') !== false) {
                        \Jidaikobo\A11yc\ValidationRecorder::error(
                            $url,
                            'meanless_element_timing',
                            $n,
                            $tag,
                            $tag,
                            $runtime
                        );
                    } else {
                        \Jidaikobo\A11yc\ValidationRecorder::error(
                            $url,
                            'meanless_element',
                            $n,
                            $tag,
                            $tag,
                            $runtime
                        );
                    }
                    $n++;
                }
            }
        }
        static::addErrorToHtml($url, 'meanless_element');
    }
}
