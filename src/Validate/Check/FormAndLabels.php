<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class FormAndLabels extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        $error_names = array(
            'labelless',
            'submitless',
            'duplicated_names',
            'unique_label',
            'contain_plural_form_elements',
            'lackness_of_form_ends',
        );

        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[1]) {
            return;
        }

        if (! in_array('form', $ms[1])) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, $error_names, null, 4, $runtime);
            return;
        }

        if (self::lacknessOfFormEnds($url, $str, $runtime)) {
            return;
        }

        $forms = self::collectFormItems($ms, $context);

        $n = 0;
        $tmp_html = $str;
        foreach ($forms as $k => $v) {
            $uniqued_types = array_unique($v['types']);
            $uniqued_eles = array_unique($v['eles']);

            if (self::ignoreForm($uniqued_types, $uniqued_eles)) {
                continue;
            }

            $attrs = Element\Get::attributes($v['form'], $context);
            $action = isset($attrs['action']) ? $attrs['action'] : $v['form'];

            self::labelless($n, $url, $v, $action, $runtime);
            self::submitless($n, $url, $v, $action, $uniqued_types, $uniqued_eles, $runtime);

            $replace = preg_quote($v['form'], '/') . '.+?\<\/form\>*';
            preg_match('/' . $replace . '/is', $tmp_html, $whole_form);
            $whole_form = Arr::get($whole_form, 0, '');

            $start = mb_strpos($tmp_html, $v['form']) + mb_strlen($whole_form);
            $tmp_html = mb_substr($tmp_html, $start, null, "UTF-8");

            self::uniqueLabel($k, $url, $whole_form, $v, $context, $runtime);
            self::duplicatedNames($k, $url, $whole_form, $v, $action, $context, $runtime);
            self::missMatchForAndId($n, $url, $ms, $context, $runtime);
            $n++;
        }

        foreach ($error_names as $error_name) {
            static::addErrorToHtml($url, $error_name);
        }
    }

    private static function lacknessOfFormEnds($url, $str, $runtime = null)
    {
        if (substr_count($str, '<form') != substr_count($str, '</form')) {
            \Jidaikobo\A11yc\ValidationRecorder::error($url, 'lackness_of_form_ends', 0, '', '', $runtime);
            return true;
        }

        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'lackness_of_form_ends', null, 2, $runtime);
        return false;
    }

    private static function collectFormItems($ms, $context = null)
    {
        $form_items = array('<form ', '</form' ,'<label', '<input', '<select', '<texta', '<butto');
        $forms = array();
        $n = 0;
        foreach ($ms[0] as $k => $m) {
            $tag = substr($m, 0, 6);
            if (! in_array($tag, $form_items)) {
                continue;
            }

            if ($tag == '</form') {
                $n++;
                continue;
            }

            if ($tag == '<form ') {
                $n++;
                $forms[$n]['form']   = $m;
                $forms[$n]['labels'] = array();
                $forms[$n]['eles']   = array();
                $forms[$n]['types']  = array();
                $forms[$n]['names']  = array();
                continue;
            }

            if ($tag == '<label') {
                $forms[$n]['labels'][] = $m;
            }

            $forms[$n]['eles'][] = $ms[1][$k];
            $attrs = Element\Get::attributes($m, $context);

            if (isset($attrs['type'])) {
                $forms[$n]['types'][] = $attrs['type'];
            }
        }

        foreach ($forms as $k => $v) {
            if (! isset($v['form'])) {
                unset($forms[$k]);
            }
        }
        return $forms;
    }

    private static function ignoreForm($uniqued_types, $uniqued_eles)
    {
        if (
            $uniqued_eles == array('button') ||
            array_diff($uniqued_types, array('submit', 'hidden')) == array() ||
            (
                $uniqued_eles == array('button') &&
                array_diff($uniqued_types, array('submit', 'hidden')) == array()
            )
        ) {
            return true;
        }
        return false;
    }

    private static function labelless($n, $url, $v, $action, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
            ! $v['labels'],
            $url,
            'labelless',
            $n,
            $v['form'],
            $action,
            $runtime
        );
    }

    private static function submitless(
        $n,
        $url,
        $v,
        $action,
        $uniqued_types,
        $uniqued_eles,
        $runtime = null
    ) {
        $eleless = ! in_array('input', $uniqued_eles) &&
                         ! in_array('button', $uniqued_eles);
        $typeless = ! in_array('button', $uniqued_eles) &&
                            ! in_array('submit', $uniqued_types) &&
                            ! in_array('image', $uniqued_types);

        \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
            $eleless || $typeless,
            $url,
            'submitless',
            $n,
            $v['form'],
            $action,
            $runtime
        );
    }

    private static function uniqueLabel($k, $url, $whole_form, $v, $context = null, $runtime = null)
    {
        preg_match_all("/\<label[^\>]*?\>(.+?)\<\/label\>/is", $whole_form, $ms);

        if (isset($ms[1])) {
            foreach ($ms[1] as $kk => $each_label) {
                $alt = '';
                if (strpos($each_label, '<img') !== false) {
                    $mms = Element\Get::elementsByRe($each_label, 'ignores', 'imgs', true, $context);
                    foreach ($mms[0] as $in_img) {
                        $attrs = Element\Get::attributes($in_img, $context);
                        foreach ($attrs as $kkk => $vvv) {
                            if (strpos($kkk, 'alt') !== false) {
                                $alt .= $vvv;
                            }
                        }
                    }
                    $alt = trim($alt);
                }
                $ms[1][$kk] = trim(strip_tags($each_label)) . $alt;
            }

            $suspicion_labels = array_diff_assoc($ms[1], array_unique($ms[1]));
            $suspicion_labels = join(', ', array_unique($suspicion_labels));

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                count($ms[1]) != count(array_unique($ms[1])),
                $url,
                'unique_label',
                $k,
                $v['form'],
                $suspicion_labels,
                $runtime
            );
        }
    }

    private static function duplicatedNames(
        $k,
        $url,
        $whole_form,
        $v,
        $action,
        $context = null,
        $runtime = null
    ) {
        preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $whole_form, $names);
        if (isset($names[0])) {
            $name_arrs = array();
            foreach ($names[0] as $tag) {
                $attrs = Element\Get::attributes($tag, $context);
                if (! isset($attrs['name'])) {
                    continue;
                }
                if (strpos($tag, 'checkbox') !== false || strpos($tag, 'radio') !== false) {
                    continue;
                }

                \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                    in_array($attrs['name'], $name_arrs),
                    $url,
                    'duplicated_names',
                    $k,
                    $v['form'],
                    $action,
                    $runtime
                );
                $name_arrs[] = $attrs['name'];
            }
        }
    }

    private static function missMatchForAndId($n, $url, $ms, $context = null, $runtime = null)
    {
        if (isset($ms[1])) {
            foreach ($ms[0] as $m) {
                preg_match_all("/\<(?:input|select|textarea) .+?\>/si", $m, $mmms);
                if ($mmms[0]) {
                    $ele_types = array();
                    foreach ($mmms[0] as $ele) {
                        $ele_attrs = Element\Get::attributes($ele, $context);
                        if (! isset($ele_attrs['type'])) {
                            continue;
                        }
                        if (strtolower($ele_attrs['type']) == 'hidden') {
                            continue;
                        }
                        $ele_types[] = $ele_attrs['type'];
                    }

                    preg_match('/\<label[^\>]*?\>/is', $m, $label_m);

                    if (count($ele_types) >= 2) {
                        $tstr = $label_m[0];
                        \Jidaikobo\A11yc\ValidationRecorder::error(
                            $url,
                            'contain_plural_form_elements',
                            $n,
                            $tstr,
                            $tstr,
                            $runtime
                        );
                    }
                }
            }
        }
    }
}
