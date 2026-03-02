<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Fetcher;
use Jidaikobo\A11yc\Util;
use Jidaikobo\A11yc\Validate;

class LinkCheck extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'link_check', null, 5, $runtime);
        if (! static::shouldDoLinkCheck($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'link_check', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        $checks = array(
            'a',
            'img',
            'form',
            'meta',
        );

        preg_match_all("/ (?:id|name) *?= *?[\"']([^\"']+?)[\"']/i", $str, $fragments);
        $fetcher = new Fetcher();

        foreach ($ms[0] as $k => $tag) {
            $ele = $ms[1][$k];

            if (! in_array($ele, $checks)) {
                continue;
            }
            $attrs = Element\Get::attributes($tag, $context);

            $target_url = self::getTargetUrl($attrs);
            if (! $target_url) {
                continue;
            }

            if ($target_url[0] == '#') {
                if (! in_array(substr($target_url, 1), $fragments[1])) {
                    \Jidaikobo\A11yc\ValidationRecorder::error(
                        $url,
                        'link_check',
                        $k,
                        $tag,
                        'Fragment Not Found: ' . $target_url,
                        $runtime
                    );
                }
                continue;
            }

            if (Element::isIgnorable($tag)) {
                continue;
            }

            $target_url = Util::enuniqueUri($target_url);
            $target_url = str_replace('&#038;', '&', $target_url);

            if (! $fetcher->exists($target_url)) {
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'link_check',
                    $k,
                    $tag,
                    'Not Found: ' . $target_url,
                    $runtime
                );
                continue;
            }

            $headers = @get_headers($target_url);
            if ($headers !== false && strpos($headers[0], ' 40') !== false) {
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'link_check',
                    $k,
                    $tag,
                    'header 40x: ' . $target_url,
                    $runtime
                );
            }
        }

        if (static::errorIdsForUrl($url, $runtime)) {
            static::addErrorToHtml($url, 'link_check');
        }
    }

    protected static function getTargetUrl($attrs)
    {
        if (isset($attrs['href'])) {
            return $attrs['href'];
        } elseif (isset($attrs['src'])) {
            return $attrs['src'];
        } elseif (isset($attrs['action'])) {
            return $attrs['action'];
        } elseif (isset($attrs['property'])) {
            if ($attrs['property'] == 'og:url' || $attrs['property'] == 'og:image') {
                return $attrs['content'];
            }
        }
        return '';
    }
}
