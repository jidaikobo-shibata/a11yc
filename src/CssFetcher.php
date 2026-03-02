<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Guzzle;
use Jidaikobo\A11yc\Input;
use Jidaikobo\A11yc\Util;

final class CssFetcher
{
    public function fetchCombinedCss(string $url, string $ua = 'using'): string
    {
        $ua = $ua == 'using' ? Input::userAgent() : $ua;
        if (! is_string($ua)) {
            Util::error();
        }

        $html = (new Fetcher())->fetchBody($url, array(
            'user_agent' => $ua,
        ));
        if ($html === '') {
            return '';
        }

        return $this->getCombinedCssFromHtml($html, $ua);
    }

    private function getCombinedCssFromHtml(string $html, string $ua): string
    {
        $css = '';

        if (preg_match_all("/\<style[^\>]*\>(.*?)\<\/style\>/si", $html, $ms)) {
            foreach ($ms[1] as $m) {
                $css .= $m;
            }
        }

        $css .= $this->getCssFileFromLink($html, $ua);

        return $css;
    }

    private function getCssFileFromLink(string $html, string $ua): string
    {
        $css = '';
        $ua = $ua == 'using' ? Input::userAgent() : $ua;
        $fetcher = new Fetcher();

        if (preg_match_all("/\<link [^\>]*\>/si", $html, $ms)) {
            foreach ($ms[0] as $m) {
                if (strpos($m, 'stylesheet') === false) {
                    continue;
                }
                $attrs = Element\Get::attributes($m);
                if (! isset($attrs['href'])) {
                    continue;
                }
                $url = $this->normalizeUri($attrs['href']);
                if (! $fetcher->exists($url)) {
                    continue;
                }
                $current_css = $this->fetchFromInternet($url, $ua);
                $css .= $current_css;

                if (preg_match_all("/@import *?url *?\((.+?)\)/si", $current_css, $mms)) {
                    foreach ($mms[1] as $import_url) {
                        $import_url = trim($import_url, '"');
                        $import_url = trim($import_url, "'");
                        $import_url = $this->normalizeUri($import_url, $url);
                        $css .= $this->fetchFromInternet($import_url, $ua);
                    }
                }
            }
        }

        return $css;
    }

    private function fetchFromInternet(string $url, string $ua = 'using'): string
    {
        $instance = Guzzle::forge($url);
        $instance->setConfig(
            'User-Agent',
            Util::s($ua . ' GuzzleHttp/a11yc (+http://www.jidaikobo.com)')
        );
        return (string) $instance->body;
    }

    private function normalizeUri(string $url, string $css_url = ''): string
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }

        if (strlen($url) >= 2 && $url[0] == '/' && $url[1] != '/') {
            return Util::enuniqueUri($url);
        }

        if ($css_url) {
            $css_urls = explode('/', $css_url);
            array_pop($css_urls);
            $css_url = join('/', $css_urls);

            if (strlen($url) >= 2 && $url[0] == '.' && $url[1] == '/') {
                $url = $css_url . substr($url, 1);
            } elseif (strlen($url) >= 3 && $url[0] == '.' && $url[1] == '.' && $url[2] == '/') {
                $strs = explode('../', $url);
                $upper_num = count($strs) - 1;
                for ($n = 1; $n <= $upper_num; $n++) {
                    array_pop($css_urls);
                }
                $css_url = join('/', $css_urls);
                $url = $css_url . '/' . end($strs);
            } elseif (strpos($url, 'http') !== 0) {
                $url = $css_url . '/' . $url;
            }
        }

        return Util::urldec($url);
    }
}
