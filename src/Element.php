<?php

namespace Jidaikobo\A11yc;

class Element
{
    public static $ignores = array(
        "/\<script.+?\<\/script\>/si",
        "/\<style.+?\<\/style\>/si",
        "/\<rdf:RDF.+?\<\/rdf:RDF\>/si",
    );

    public static $ignores_comment_out = array(
        "/\<!--.*?--\>/si",
        "/\<!\[CDATA\[.*?\]\]\>/si",
    );

    protected static $ruled_attrs = array(
            'accept', 'accept-charset', 'accesskey', 'action', 'align', 'alt',
            'async', 'autocomplete', 'autofocus', 'autoplay', 'bgcolor', 'border',
            'buffered', 'challenge', 'charset', 'checked', 'cite', 'class', 'code',
            'codebase', 'color', 'cols', 'colspan', 'content', 'contenteditable',
            'contextmenu', 'controls', 'coords', 'data', 'datetime', 'default',
            'defer', 'dir', 'dirname', 'disabled', 'draggable', 'dropzone', 'enctype',
            'for', 'form', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang',
            'http-equiv', 'icon', 'id', 'ismap', 'itemprop', 'keytype', 'kind',
            'label', 'lang', 'language', 'list', 'loop', 'low', 'manifest', 'max',
            'maxlength', 'media', 'method', 'min', 'multiple', 'name', 'novalidate',
            'open', 'optimum', 'pattern', 'ping', 'placeholder', 'poster', 'preload',
            'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'reversed',
            'rows', 'rowspan', 'sandbox', 'spellcheck', 'scope', 'scoped', 'seamless',
            'selected', 'shape', 'size', 'sizes', 'span', 'src', 'srcdoc', 'srclang',
            'start', 'step', 'style', 'summary', 'tabindex', 'target', 'title',
            'type', 'usemap', 'value', 'width', 'wrap',
            'cellspacing', 'cellpadding',
            'xmlns', 'rev', 'profile', 'property', 'role', 'prefix', 'itemscope', 'xml:lang',
            'onclick', 'ondblclick', 'onkeydown', 'onkeypress', 'onkeyup', 'onmousedown',
            'onmouseup', 'onmouseover', 'onmouseout', 'onmousemove', 'onload', 'onunload',
            'onfocus', 'onblur', 'onsubmit', 'onreset', 'onchange', 'onresize', 'onmove',
            'ondragdrop', 'onabort', 'onerror', 'onselect',
        );

    protected static $double = '"';
    protected static $single = "'";
    protected static $quoted_double = '[---a11yc_quoted_double---]';
    protected static $quoted_single = '[---a11yc_quoted_open---]';
    protected static $open_double   = '[---a11yc_open_double---]';
    protected static $close_double  = '[---a11yc_close_double---]';
    protected static $open_single   = '[---a11yc_open_single---]';
    protected static $close_single  = '[---a11yc_close_single---]';
    protected static $inner_double  = '[---a11yc_inner_double---]';
    protected static $inner_single  = '[---a11yc_inner_single---]';
    protected static $inner_space   = '[---a11yc_inner_space---]';
    protected static $inner_equal   = '[---a11yc_inner_equal---]';
    protected static $inner_newline = '[---a11yc_inner_newline---]';

    public static function isIgnorable($str)
    {
        $attrs = Element\Get::attributes($str);

        if (
            (isset($attrs['tabindex']) && (string) $attrs['tabindex'] === '-1') ||
            (isset($attrs['aria-hidden']) && (string) $attrs['aria-hidden'] === 'true')
        ) {
            return true;
        }

        if (isset($attrs['href']) && strpos($attrs['href'], 'javascript') === 0) {
            return true;
        }

        if (isset($attrs['href']) && $attrs['href'] == '#') {
            return true;
        }

        if (isset($attrs['href']) && substr($attrs['href'], 0, 7) == 'mailto:') {
            return true;
        }

        return false;
    }

    public static function ignoreElementsByStr($str)
    {
        $ignores = array_merge(static::$ignores, static::$ignores_comment_out);
        foreach ($ignores as $ignore) {
            $str = preg_replace($ignore, '', $str);
        }
        return $str;
    }

    public static function prepareStrings($str)
    {
        $str = str_replace(
            array("\\'", '\\"'),
            array(self::$quoted_single, self::$quoted_double),
            $str
        );

        $suspicious_end_quote = false;
        $no_space_between_attributes = false;

        while (true) {
            $d_offset = mb_strpos($str, '"', 0, 'UTF-8');
            $s_offset = mb_strpos($str, "'", 0, 'UTF-8');

            if ($d_offset && $s_offset) {
                $target = $d_offset < $s_offset ? self::$double : self::$single;
            } elseif ($d_offset) {
                $target = self::$double;
            } elseif ($s_offset) {
                $target = self::$single;
            } else {
                break;
            }
            $opp = $target == self::$double ? self::$single : self::$double;

            $open = $target == self::$double ? self::$open_double : self::$open_single;
            $close = $target == self::$double ? self::$close_double : self::$close_single;
            $inner = $target == self::$double ? self::$inner_single : self::$inner_double;

            if ($open_pos = mb_strpos($str, $target, 0, 'UTF-8')) {
                $close_pos = mb_strpos($str, $target, $open_pos + 1, 'UTF-8');

                if (! $close_pos) {
                    $str .= $close;
                    $suspicious_end_quote = true;
                }

                $search = mb_substr($str, $open_pos, $close_pos - $open_pos + 1, 'UTF-8');
                $replace = str_replace(
                    array($target, $opp, ' ', '=', "\n", "\r"),
                    array(
                        '',
                        $inner,
                        self::$inner_space,
                        self::$inner_equal,
                        self::$inner_newline,
                        self::$inner_newline,
                    ),
                    $search
                );
                $replace = $open . $replace . $close;
                $str = str_replace($search, $replace, $str);
            }
        }

        if (preg_match("/\[---a11yc_close_double---\][^\n\r\t\f \>\?]/is", $str)) {
            $str = str_replace("[---a11yc_close_double---\]", "[---a11yc_close_double---\] ", $str);
            $no_space_between_attributes = true;
        }

        $str = preg_replace("/ {2,}/", " ", $str);
        $str = preg_replace("/ *?= */", "=", $str);
        $str = str_replace(array("\n", "\r"), " ", $str);

        return array($str, $suspicious_end_quote, $no_space_between_attributes);
    }

    public static function explodeStrings($str)
    {
        $attrs = array();
        foreach (explode(' ', $str) as $k => $v) {
            $v = trim($v, '>');
            if (empty($v)) {
                continue;
            }
            if ($v == '/') {
                continue;
            }
            if ($v[0] == '<') {
                continue;
            }
            if (strpos($v, '=') !== false) {
                list($key, $val) = explode("=", $v);
                $key = trim(strtolower($key));
            } else {
                $key = $v;
                $val = $v;
            }
            $val = self::recoverStr($val);

            if (
                in_array($key, self::$ruled_attrs) ||
                substr($key, 0, 5) == 'aria-' ||
                substr($key, 0, 5) == 'data-' ||
                substr($key, 0, 4) == 'xml:'
            ) {
                if (array_key_exists($key, $attrs)) {
                    $key = $key . '_' . $k;
                    $attrs['plural'] = true;
                }
                $attrs[$key] = $val;
            }
        }

        return $attrs;
    }

    public static function recoverStr($str)
    {
        return str_replace(
            array(
                self::$quoted_double,
                self::$quoted_single,
                self::$open_double,
                self::$close_double,
                self::$open_single,
                self::$close_single,
                self::$inner_double,
                self::$inner_single,
                self::$inner_space,
                self::$inner_equal,
                self::$inner_newline,
            ),
            array(
                '\\"',
                "\\'",
                '',
                '',
                '',
                '',
                self::$double,
                self::$single,
                ' ',
                '=',
                "\n",
            ),
            $str
        );
    }
}
