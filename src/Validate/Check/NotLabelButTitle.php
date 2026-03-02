<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class NotLabelButTitle extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'not_label_but_title', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        list($eles, $fors) = self::setLabelAndElement($ms, $context);

        if (empty($eles)) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'not_label_but_title', null, 4, $runtime);
            return;
        }

        $del_eles = self::setDeleteElement($eles, $fors);
        $del_fors = self::setDeleteFor($eles, $del_eles);

        foreach ($del_eles as $k) {
            unset($eles[$k]);
        }
        foreach ($del_fors as $k) {
            unset($eles[$k]);
        }

        $del_eles = self::setSecondaryDeleteElement($str, $eles);

        foreach ($del_eles as $k) {
            unset($eles[$k]);
        }

        foreach ($eles as $k => $ele) {
            if ($ele['tag_name'] == 'label') {
                continue;
            }

            $title = trim(mb_convert_kana($ele['title'], 's'));
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                empty($title),
                $url,
                'not_label_but_title',
                $k,
                $ele['tag'],
                $ele['tag'],
                $runtime
            );
        }
        static::addErrorToHtml($url, 'not_label_but_title');
    }

    private static function setLabelAndElement($ms, $context = null)
    {
        $eles = array();
        $fors = array();

        foreach ($ms[1] as $k => $m) {
            if (! in_array($m, array('label', 'input', 'textarea', 'select'))) {
                continue;
            }

            $attrs = Element\Get::attributes($ms[0][$k], $context);
            if ($m == 'label') {
                $eles[$k]['tag'] = $ms[0][$k];
                $eles[$k]['tag_name'] = $ms[1][$k];
                $for = isset($attrs['for']) ? $attrs['for'] : '';
                $eles[$k]['for'] = $for;
                if ($for) {
                    $fors[] = $for;
                }
            } elseif (in_array($m, array('textarea', 'select'))) {
                $eles[$k]['tag'] = $ms[0][$k];
                $eles[$k]['tag_name'] = $ms[1][$k];
                $eles[$k]['title'] = isset($attrs['title']) ? $attrs['title'] : '';
                $eles[$k]['id'] = isset($attrs['id']) ? $attrs['id'] : '';
            } elseif ($m == 'input') {
                $attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'text';

                if (in_array($attrs['type'], array('text', 'checkbox', 'radio', 'file', 'password'))) {
                    $eles[$k]['tag'] = $ms[0][$k];
                    $eles[$k]['tag_name'] = $ms[1][$k];
                    $eles[$k]['title'] = isset($attrs['title']) ? $attrs['title'] : '';
                    $eles[$k]['id'] = isset($attrs['id']) ? $attrs['id'] : '';
                }
            }
        }
        return array($eles, $fors);
    }

    private static function setDeleteElement($eles, $fors)
    {
        $del_eles = array();
        foreach ($fors as $for) {
            foreach ($eles as $k => $ele) {
                if (isset($ele['id']) && $ele['id'] == $for) {
                    if (array_key_exists($for, $del_eles)) {
                        continue;
                    }
                    $del_eles[$for] = $k;
                }
            }
        }
        return $del_eles;
    }

    private static function setDeleteFor($eles, $del_eles)
    {
        $del_fors = array();
        foreach (array_keys($del_eles) as $id) {
            foreach ($eles as $k => $ele) {
                if (isset($ele['for']) && $ele['for'] == $id) {
                    $del_fors[] = $k;
                }
            }
        }
        return $del_fors;
    }

    private static function setSecondaryDeleteElement($str, $eles)
    {
        $del_eles = array();
        $pattern = '/\<label[^\>]*?\>.*?\<\/label\>/is';
        preg_match_all($pattern, $str, $mms);

        if ($mms[0]) {
            foreach ($mms[0] as $m) {
                $prev = '';
                foreach ($eles as $k => $ele) {
                    if ($ele['tag_name'] == 'label') {
                        $prev = $k;
                    } elseif (strpos($m, $ele['tag']) !== false) {
                        $del_eles[] = $prev;
                        $del_eles[] = $k;
                    }
                }
            }
        }
        return $del_eles;
    }
}
