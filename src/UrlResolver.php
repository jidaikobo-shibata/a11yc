<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Util;

final class UrlResolver
{
    public function hostFromUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            return null;
        }

        $parts = parse_url($url);
        if (! is_array($parts)) {
            return null;
        }

        $scheme = (string) Arr::get($parts, 'scheme', '');
        $host = (string) Arr::get($parts, 'host', '');
        $port = Arr::get($parts, 'port');
        if ($scheme === '' || $host === '') {
            return null;
        }

        $base = $scheme . '://' . $host;
        if ($port) {
            $base .= ':' . (string) $port;
        }

        return $base;
    }

    public function normalize(string $url, ?string $base_url = null): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return Util::urldec($url);
        }

        if ($base_url) {
            return Util::enuniqueUri($url, $base_url);
        }

        return Util::enuniqueUri($url);
    }
}
