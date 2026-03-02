<?php

namespace Jidaikobo\A11yc;

final class CssAnalysis
{
    private static $vendors = array(
        '-ms-', '-moz-', '-webkit-', '-o-', '-moz-osx-'
    );

    private static $cssProps = array(
        'color', 'opacity', 'background', 'background-attachment', 'background-clip',
        'background-color', 'background-image', 'background-origin', 'background-position',
        'background-repeat', 'background-size', 'border', 'border-bottom',
        'border-bottom-color', 'border-bottom-left-radius', 'border-bottom-right-radius',
        'border-bottom-style', 'border-bottom-width', 'border-color', 'border-image',
        'border-image-outset', 'border-image-repeat', 'border-image-slice',
        'border-image-source', 'border-image-width', 'border-left', 'border-left-color',
        'border-left-style', 'border-left-width', 'border-radius', 'border-right',
        'border-right-color', 'border-right-style', 'border-right-width', 'border-style',
        'border-top', 'border-top-color', 'border-top-left-radius',
        'border-top-right-radius', 'border-top-style', 'border-top-width', 'border-width',
        'box-decoration-break', 'box-shadow', 'image-resolution', 'object-fit',
        'object-position', 'marquee-direction', 'marquee-play-count', 'marquee-speed',
        'marquee-style', 'break-after', 'break-before', 'break-inside', 'column-count',
        'column-fill', 'column-gap', 'column-rule', 'column-rule-color', 'column-rule-style',
        'column-rule-width', 'column-span', 'column-width', 'columns', 'cue', 'cue-after',
        'cue-before', 'pause', 'pause-after', 'pause-before', 'rest', 'rest-after',
        'rest-before', 'speak', 'speak-as', 'voice-balance', 'voice-duration', 'voice-family',
        'voice-pitch', 'voice-range', 'voice-rate', 'voice-stress', 'voice-volume',
        'backface-visibility', 'perspective', 'perspective-origin', 'transform',
        'transform-origin', 'transform-style', 'transition', 'transition-delay',
        'transition-duration', 'transition-property', 'transition-timing-function',
        'animation', 'animation-delay', 'animation-direction', 'animation-duration',
        'animation-fill-mode', 'animation-iteration-count', 'animation-name',
        'animation-play-state', 'animation-timing-function', 'align-content', 'align-items',
        'align-self', 'flex', 'flex-basis', 'flex-direction', 'flex-flow', 'flex-grow',
        'flex-shrink', 'flex-wrap', 'justify-content', 'order', 'font', 'font-family',
        'font-feature-settings', 'font-kerning', 'font-language-override', 'font-size',
        'font-size-adjust', 'font-stretch', 'font-style', 'font-synthesis', 'font-variant',
        'font-variant-alternates', 'font-variant-caps', 'font-variant-east-asian',
        'font-variant-ligatures', 'font-variant-numeric', 'font-variant-position',
        'font-weight', 'fit', 'fit-position', 'image-orientation', 'orphans', 'page',
        'page-break-after', 'page-break-before', 'page-break-inside', 'size', 'widows',
        'hanging-punctuation', 'hyphens', 'letter-spacing', 'line-break',
        'overflow-wrap', 'tab-size', 'text-align', 'text-align-last', 'text-decoration',
        'text-decoration-color', 'text-decoration-line', 'text-decoration-skip',
        'text-decoration-style', 'text-emphasis', 'text-emphasis-color',
        'text-emphasis-position', 'text-emphasis-style', 'text-indent', 'text-justify',
        'text-shadow', 'text-transform', 'text-underline-position', 'white-space',
        'word-break', 'word-spacing', 'box-sizing', 'cursor', 'icon', 'ime-mode',
        'nav-down', 'nav-index', 'nav-left', 'nav-right', 'nav-up', 'outline',
        'outline-color', 'outline-offset', 'outline-style', 'outline-width', 'resize',
        'text-overflow', 'direction', 'text-combine-horizontal', 'text-combine-mode',
        'text-orientation', 'unicode-bidi', 'writing-mode', 'marks', 'grid-cell',
        'grid-column', 'grid-column-align', 'grid-column-sizing', 'grid-column-span',
        'grid-columns', 'grid-flow', 'grid-row', 'grid-row-align', 'grid-row-sizing',
        'grid-row-span', 'grid-rows', 'grid-template', 'list-style', 'list-style-image',
        'list-style-position', 'list-style-type', 'bottom', 'clip', 'left', 'position',
        'right', 'top', 'z-index', 'border-collapse', 'border-spacing', 'caption-side',
        'empty-cells', 'table-layout', 'clear', 'display', 'float', 'height', 'margin',
        'margin-bottom', 'margin-left', 'margin-right', 'margin-top', 'max-height',
        'max-width', 'min-height', 'min-width', 'overflow', 'overflow-style',
        'overflow-x', 'overflow-y', 'padding', 'padding-bottom', 'padding-left',
        'padding-right', 'padding-top', 'visibility', 'width', 'content',
        'counter-increment', 'counter-reset', 'crop', 'move-to', 'page-policy',
        'quotes', 'alignment-adjust', 'alignment-baseline', 'baseline-shift',
        'dominant-baseline', 'drop-initial-after-adjust', 'drop-initial-after-align',
        'drop-initial-before-adjust', 'drop-initial-before-align', 'drop-initial-size',
        'drop-initial-value', 'inline-box-align', 'line-height', 'line-stacking',
        'line-stacking-ruby', 'line-stacking-shift', 'line-stacking-strategy',
        'text-height', 'vertical-align', 'ruby-align', 'ruby-overhang', 'ruby-position',
        'ruby-span', 'target', 'target-name', 'target-new', 'target-position', 'filter',
        'text-rendering', 'font-smoothing', 'appearance'
    );

    public static function fetchCss($url, $ua = 'using')
    {
        $result = new CssParseResult();
        $ua = $ua == 'using' ? Input::userAgent() : $ua;
        if (! is_string($ua)) {
            Util::error();
        }

        $css = (new CssFetcher())->fetchCombinedCss($url, $ua);
        if ($css === '') {
            return $result;
        }

        $result->csses = static::makeArray($css, $result);

        return $result;
    }

    public static function makeArray($css, CssParseResult $result)
    {
        $css = preg_replace('/\/\*.+?\*\//is', '', $css);
        $css = preg_replace('/^@import.+?$/mis', '', $css);
        $css = preg_replace('/^@charset.+?$/mis', '', $css);
        $css = preg_replace('/^@(?:page|media)[^{]*?{[\n\s\t]*?}/mis', '', $css);

        $start = mb_substr_count($css, '{');
        $end = mb_substr_count($css, '}');
        $result->is_suspicious_paren_num = $start != $end;

        preg_match_all('/@(?:page|media|font-face|keyframes|-webkit-keyframes).+?}.*?}/is', $css, $ms);
        $css = str_replace($ms[0], '', $css);

        $csses = static::divideBlocks($ms[0], $css);
        $rets = static::divideSelectorsAndProperties($csses, $result);

        foreach ($result->suspicious_props as $k => $v) {
            foreach (static::$cssProps as $prop) {
                foreach (static::$vendors as $vendor) {
                    if ($v == $vendor . $prop) {
                        unset($result->suspicious_props[$k]);
                    }
                }
            }
        }

        return $rets;
    }

    private static function divideBlocks($arr, $css)
    {
        $csses = array();
        $csses['base'] = explode('}', $css);

        foreach ($arr as $m) {
            $atmarks = substr($m, 0, strpos($m, '{'));
            $atmarks = trim($atmarks);
            $vals = substr($m, strpos($m, '{'));
            $vals = trim(trim($vals), '}');
            $csses[$atmarks] = explode('}', $vals);
        }

        return $csses;
    }

    private static function divideSelectorsAndProperties($csses, CssParseResult $result)
    {
        $rets = array();

        foreach ($csses as $type => $typeCss) {
            $rets[$type] = array();
            foreach ($typeCss as $each) {
                if (strpos($each, '{') === false) {
                    continue;
                }
                list($selectors, $properties) = explode('{', $each);

                $selectors = trim($selectors);
                $properties = trim($properties);
                if (empty($selectors) || empty($properties)) {
                    continue;
                }

                $eachSelectors = static::divideStrs($selectors, ',');
                $eachProperties = static::divideStrs($properties, ';');
                $props = static::divideEachProperties($eachProperties, $result);

                foreach ($eachSelectors as $eachSelector) {
                    if (! isset($rets[$type][$eachSelector])) {
                        $rets[$type][$eachSelector] = array();
                    }
                    $tmps = array_merge($rets[$type][$eachSelector], $props);
                    ksort($tmps);
                    $rets[$type][$eachSelector] = $tmps;
                }
            }
            ksort($rets[$type]);
        }

        return $rets;
    }

    private static function divideStrs($strs, $delimiter)
    {
        if (strpos($strs, $delimiter) !== false) {
            $eachStrs = explode($delimiter, $strs);
            $eachStrs = array_map('trim', $eachStrs);
        } else {
            $eachStrs = array(trim($strs));
        }

        return $eachStrs;
    }

    private static function divideEachProperties($eachProperties, CssParseResult $result)
    {
        $props = array();
        foreach ($eachProperties as $propAndVal) {
            $propAndVal = trim($propAndVal);

            $propAndVals = array();
            if (strpos($propAndVal, ':') !== false) {
                $propAndVals = explode(':', $propAndVal);
                $propAndVals = array_map('trim', $propAndVals);
            } elseif (! empty($propAndVal)) {
                $result->suspicious_prop_and_vals[] = $propAndVal;
                continue;
            }

            if (empty($propAndVals)) {
                continue;
            }
            if (count($propAndVals) != 2) {
                $result->suspicious_props[] = $propAndVals[0];
                continue;
            }

            $prop = strtolower($propAndVals[0]);
            $val = trim($propAndVals[1]);
            if ($prop === '' || $val === '') {
                $result->suspicious_val_prop[] = array($propAndVals[0], $propAndVals[1]);
                continue;
            }
            $props[$prop] = $val;
        }

        return $props;
    }
}
