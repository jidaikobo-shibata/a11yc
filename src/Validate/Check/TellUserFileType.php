<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Guzzle;
use Jidaikobo\A11yc\Util;
use Jidaikobo\A11yc\Validate;

class TellUserFileType extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'tell_user_file_type', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[1]) {
            return;
        }

        $suspicious = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'tar');

        foreach ($ms[0] as $k => $m) {
            foreach ($suspicious as $vv) {
                $tmp = str_replace("'", '"', $m);
                if (strpos($tmp, '.' . $vv . '"') !== false) {
                    $attrs = Element\Get::attributes($m, $context);

                    if (! isset($attrs['href'])) {
                        continue;
                    }
                    $href = strtolower($attrs['href']);
                    $inner = Element\Get\Each::textFromElement($m, $context);
                    $f_inner = self::addCheckStrings($inner, $vv);

                    list($len, $is_exists) = self::existCheck($href);
                    $tstr = $ms[0][$k];

                    $is_better_text =
                        (is_null($is_exists) || $is_exists === true) &&
                        (
                            strpos(strtolower($f_inner), $vv) === false ||
                            preg_match("/\d/", $f_inner) === false
                        );

                    \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                        $is_better_text,
                        $url,
                        'tell_user_file_type',
                        $k,
                        $tstr,
                        $href . ': ' . $inner . $len,
                        $runtime
                    );

                    if (is_null($is_exists)) {
                        continue;
                    }
                    if (
                        $is_exists === false
                        && static::shouldDoLinkCheck($runtime)
                    ) {
                        \Jidaikobo\A11yc\ValidationRecorder::error(
                            $url,
                            'link_check',
                            $k,
                            $tstr,
                            $href,
                            $runtime
                        );
                    }
                }
            }
        }
        static::addErrorToHtml($url, 'tell_user_file_type');
        static::addErrorToHtml($url, 'link_check');
    }

    private static function addCheckStrings($inner, $vv)
    {
        if (is_bool($inner)) {
            return '';
        }

        $exts = array(
            array('ext' => array('doc', 'docx'), 'str' => 'word'),
            array('ext' => array('xls', 'xlsx'), 'str' => 'excel'),
            array('ext' => array('ppt', 'pptx'), 'str' => 'power'),
        );
        foreach ($exts as $ext) {
            if (in_array($vv, $ext['ext']) && strpos($inner, $ext['str']) !== false) {
                $inner .= join(',', $ext['ext']);
            }
        }

        return $inner;
    }

    private static function existCheck($href)
    {
        $len = '';
        $is_exists = null;
        if (Guzzle::envCheck()) {
            $instance = Guzzle::forge($href);
            $is_exists = $instance->is_exists;
            if ($is_exists) {
                $tmps = $instance->headers;
                if (isset($tmps['Content-Length'][0])) {
                    $ext = strtoupper(substr($href, strrpos($href, '.') + 1));
                    $len = ' (' . $ext . ', ' . Util::byte2Str(intval($tmps['Content-Length'][0])) . ')';
                }
            }
        }
        return array($len, $is_exists);
    }
}
