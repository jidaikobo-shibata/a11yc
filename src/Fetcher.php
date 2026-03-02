<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Guzzle;
use Jidaikobo\A11yc\Input;
use Jidaikobo\A11yc\Util;

final class Fetcher
{
    public function fetchBody(string $url, array $options = array()): string
    {
        $user_agent = (string) Arr::get($options, 'user_agent', 'using');
        return (string) $this->fetchBodyFromInternet($url, $user_agent);
    }

    public function fetchHtml(string $url, array $options = array()): array
    {
        $html = $this->fetchBody($url, $options);

        return array(
            'url' => $url,
            'real_url' => $this->realUrl($url),
            'exists' => $html !== '',
            'is_html' => $html !== '',
            'html' => $html,
        );
    }

    public function exists(string $url): bool
    {
        if (Guzzle::envCheck() === false) {
            return false;
        }
        $instance = Guzzle::forge($url);
        return (bool) $instance->is_exists;
    }

    public function realUrl(string $url): string
    {
        $instance = Guzzle::forge($url);
        $real_url = (string) $instance->real_url;
        return $real_url ?: $url;
    }

    public function fetchMeta(string $url, array $options = array()): array
    {
        $instance = $this->makeClient($url, $options);

        return array(
            'status_code' => $instance->status_code,
            'errors' => $instance->errors ?: array(),
            'exists' => (bool) $instance->is_exists,
            'real_url' => (string) $instance->real_url,
            'is_html' => (bool) $instance->is_html,
            'html' => $instance->is_html ? (string) $instance->body : '',
        );
    }

    private function fetchBodyFromInternet(string $url, string $ua = 'using')
    {
        $instance = $this->makeClient($url, array(
            'user_agent' => $ua,
        ));
        $bool_or_html = $instance->is_html ? $instance->body : false;

        if (! $bool_or_html) {
            return '';
        }

        return $bool_or_html;
    }

    private function makeClient(string $url, array $options = array()): Guzzle
    {
        $user_agent = (string) Arr::get($options, 'user_agent', 'using');
        $user_agent = $user_agent == 'using' ? Input::userAgent() : $user_agent;
        if (! is_string($user_agent)) {
            Util::error();
        }

        $instance = Guzzle::forge($url);
        $instance->setConfig(
            'User-Agent',
            Util::s($user_agent . ' GuzzleHttp/a11yc (+http://www.jidaikobo.com)')
        );

        return $instance;
    }
}
