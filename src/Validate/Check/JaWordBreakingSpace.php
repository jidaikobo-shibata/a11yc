<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class JaWordBreakingSpace extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'ja_word_breaking_space', null, 5, $runtime);
        if (strpos(RuntimeConfig::defaultLang(), 'ja') === false) {
            return false;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'ja_word_breaking_space', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);

        $search = '[^\x01-\x7E][ 　]{2,}[^\x01-\x7E]';
        $search .= '|[^\x01-\x7E][ 　]+[^\x01-\x7E][ 　]';
        $search .= '|(?<![^\x01-\x7E])[^\x01-\x7E][ 　]+[^\x01-\x7E](?![^\x01-\x7E])';

        preg_match_all("/(" . $search . ")/iu", $str, $ms);
        foreach ($ms[1] as $k => $m) {
            $tstr = $ms[0][$k];
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'ja_word_breaking_space', $k, $tstr, $m, $runtime);
        }

        static::addErrorToHtml($url, 'ja_word_breaking_space');
    }
}
