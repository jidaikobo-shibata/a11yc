<?php

namespace Jidaikobo\A11yc;

final class HtmlDocument
{
    public static function pageTitle(string $html): string
    {
        preg_match("/<title.*?>(.+?)<\/title>/si", $html, $m);
        $tmp = isset($m[1]) ? $m[1] : '';
        $title = str_replace(array("\n", "\r"), '', $tmp);

        return $title;
    }
}
