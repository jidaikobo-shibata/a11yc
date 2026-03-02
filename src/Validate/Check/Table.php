<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class Table extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        $error_names = array(
            'table_use_th',
            'table_use_scope',
            'table_use_valid_scope',
            'table_use_summary',
            'table_use_caption',
        );

        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        preg_match_all('/\<table[^\>]*?\>.+?\<\/table\>/ims', $str, $ms);

        if (! $ms[0]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 4, $runtime);
            return;
        }

        foreach ($ms[0] as $n => $m) {
            $attrs = Element\Get::attributes($m, $context);
            if (Arr::get($attrs, 'role') == 'presentation') {
                continue;
            }

            preg_match('/\<table[^\>]*?\>/i', $m, $table_tag);

            $tstr = $table_tag[0];
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                strpos($m, '<th') === false,
                $url,
                'table_use_th',
                $n,
                $tstr,
                $tstr,
                $runtime
            );

            self::scopeless($n, $m, $url, $tstr, $runtime);
            self::summaryless($n, $m, $url, $tstr, $table_tag[0], $context, $runtime);

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                strpos($m, '</caption>') === false,
                $url,
                'table_use_caption',
                $n,
                $tstr,
                $tstr,
                $runtime
            );
        }

        static::addErrorToHtml($url, 'table_use_th');
        static::addErrorToHtml($url, 'table_use_scope');
        static::addErrorToHtml($url, 'table_use_valid_scope');
        static::addErrorToHtml($url, 'table_use_summary');
        static::addErrorToHtml($url, 'table_use_caption');
    }

    private static function scopeless($n, $m, $url, $tstr, $runtime = null)
    {
        if (substr_count($m, '<th') == substr_count($m, '<td')) {
            return;
        }

        if (strpos($m, ' scope') === false) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'table_use_scope', $n, $tstr, $tstr, $runtime);
        } elseif (preg_match_all('/scope *?= *?[\'"]([^\'"]+?)[\'"]/i', $m, $mms)) {
            foreach ($mms[1] as $nn => $mm) {
                if (! in_array($mm, array('col', 'row', 'rowgroup', 'colgroup'))) {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'table_use_valid_scope',
                        $n,
                        $tstr,
                        $mms[0][$nn],
                        $runtime
                    );
                }
            }
        } else {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'table_use_scope', $m, 2, $runtime);
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'table_use_valid_scope', $m, 2, $runtime);
        }
    }

    private static function summaryless(
        $n,
        $m,
        $url,
        $tstr,
        $table_tag,
        $context = null,
        $runtime = null
    ) {
        if (in_array(Element\Get\Each::doctype($url, $context), array('html4', 'xhtml'))) {
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                ! array_key_exists('summary', Element\Get::attributes($table_tag, $context)),
                $url,
                'table_use_summary',
                $n,
                $tstr,
                $tstr,
                $runtime
            );
        } else {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'table_use_summary', $m, 5, $runtime);
        }
    }
}
