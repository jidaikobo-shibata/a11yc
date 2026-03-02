<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Image;
use Jidaikobo\A11yc\Yaml;

final class Analyzer
{
    public function analyzeUrl(string $url, array $options = array()): array
    {
        $options = $this->normalizeOptions($options);

        return RuntimeConfig::withOverrides(
            $this->runtimeConfigOverrides($options),
            function () use ($url, $options) {
                $fetcher = new Fetcher();
                $fetched = $fetcher->fetchHtml($url, $options);
                $html = Arr::get($fetched, 'html', '');
                $resolved_url = (string) Arr::get($fetched, 'real_url', $url);

                $analysis = $this->analyzeHtml($html, array_merge($options, array(
                    'url' => $resolved_url,
                )));
                $analysis['meta']['requested_url'] = $url;
                $analysis['meta']['url'] = $resolved_url;
                $analysis['meta']['exists'] = (bool) Arr::get($fetched, 'exists', false);

                return $analysis;
            }
        );
    }

    public function analyzeHtml(string $html, array $options = array()): array
    {
        $options = $this->normalizeOptions($options);

        return RuntimeConfig::withOverrides(
            $this->runtimeConfigOverrides($options),
            function () use ($html, $options) {
                $url = $options['url'];
                $checks = $options['checks'];
                $user_agent = $options['user_agent'];

                $result_set = Validate::html($url, $html, $checks, $user_agent, true, array(
                    'is_partial' => (bool) Arr::get($options, 'is_partial', false),
                    'do_link_check' => (bool) Arr::get($options, 'do_link_check', false),
                    'do_css_check' => (bool) Arr::get($options, 'do_css_check', false),
                    'lang' => (string) Arr::get($options, 'lang', ''),
                    'resource_path' => (string) Arr::get($options, 'resource_path', ''),
                    'doc_resource_path' => (string) Arr::get($options, 'doc_resource_path', ''),
                ));
                $result_set = is_array($result_set) ? $result_set : array();

                $options['source_html'] = $html;

                return $this->analyzeResultSet($url, $result_set, $options);
            }
        );
    }

    public function analyzeResultSet(string $url, array $result_set, array $options = array()): array
    {
        $options = $this->normalizeOptions($options);

        return RuntimeConfig::withOverrides(
            $this->runtimeConfigOverrides($options),
            function () use ($url, $result_set, $options) {
                $errors = $this->normalizeIssues(
                    $url,
                    Arr::get($result_set, 'errors.errors', array()),
                    'error'
                );
                $notices = $this->normalizeIssues(
                    $url,
                    Arr::get($result_set, 'errors.notices', array()),
                    'notice'
                );

                return array(
                    'meta' => array(
                        'url' => $url,
                        'user_agent' => $options['user_agent'],
                        'version' => defined('A11YC_VERSION') ? A11YC_VERSION : null,
                        'check_count' => count($options['checks'] ?: CheckRegistry::availableChecks()),
                        'analyzed_at' => date(DATE_ATOM),
                    ),
                    'summary' => $this->buildSummary($result_set),
                    'issues' => array_merge($errors, $notices),
                    'images' => $options['include_images'] ? $this->extractImages($options['source_html'], $options) : array(),
                );
            }
        );
    }

    public function extractImages(string $html, array $options = array()): array
    {
        $options = $this->normalizeOptions($options);
        $images = Image::getImages($options['url'], $options['url'], $html);
        $ret = array();

        foreach ($images as $image) {
            $ret[] = array(
                'element' => Arr::get($image, 'element'),
                'src' => Arr::get($image, 'attrs.src'),
                'alt' => Arr::get($image, 'attrs.alt'),
                'href' => Arr::get($image, 'href'),
                'is_important' => (bool) Arr::get($image, 'is_important'),
                'aria' => Arr::get($image, 'aria', array()),
            );
        }

        return $ret;
    }

    public function getMetadata(): array
    {
        return array(
            'version' => defined('A11YC_VERSION') ? A11YC_VERSION : null,
            'available_checks' => CheckRegistry::availableChecks(),
            'resource_path' => defined('A11YC_RESOURCE_PATH') ? A11YC_RESOURCE_PATH : null,
        );
    }

    public function machineChecksFromResultSet(array $result_set): array
    {
        return (array) Arr::get($result_set, 'machine_checks', array());
    }

    private function normalizeOptions(array $options): array
    {
        $checks = Arr::get($options, 'checks', array());
        $checks = is_array($checks) ? array_values(array_filter($checks, 'is_string')) : array();

        return array(
            'url' => (string) Arr::get($options, 'url', 'about:blank'),
            'user_agent' => (string) Arr::get($options, 'user_agent', 'using'),
            'checks' => $checks,
            'lang' => (string) Arr::get($options, 'lang', ''),
            'resource_path' => (string) Arr::get($options, 'resource_path', ''),
            'doc_resource_path' => (string) Arr::get($options, 'doc_resource_path', ''),
            'is_partial' => (bool) Arr::get($options, 'is_partial', false),
            'do_link_check' => (bool) Arr::get($options, 'do_link_check', false),
            'do_css_check' => (bool) Arr::get($options, 'do_css_check', false),
            'include_images' => (bool) Arr::get($options, 'include_images', false),
            'source_html' => (string) Arr::get($options, 'source_html', ''),
        );
    }

    private function normalizeIssues(string $url, array $items, string $type): array
    {
        $ret = array();

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $error_id = Arr::get($item, 'code_str');
            if (! $error_id) {
                continue;
            }

            $current_err = Validate::setCurrentErr($url, $error_id);
            if (! is_array($current_err)) {
                continue;
            }

            $criterion_keys = array_values(Arr::get($current_err, 'criterions', array()));

            $level = null;
            if ($criterion_keys) {
                $level = Arr::get(Yaml::fetch(), 'criterions.' . $criterion_keys[0] . '.level.name');
            }

            $ret[] = array(
                'id' => $error_id,
                'type' => $type,
                'message' => trim((string) Arr::get($current_err, 'message', '')),
                'level' => $level,
                'criterion_keys' => $criterion_keys,
                'place_id' => $this->extractPlaceId($item),
                'snippet' => $this->extractSnippet($item),
            );
        }

        return $ret;
    }

    private function buildSummary(array $result_set): array
    {
        $error_count = count(Arr::get($result_set, 'errors.errors', array()));
        $notice_count = count(Arr::get($result_set, 'errors.notices', array()));

        return array(
            'error_count' => $error_count,
            'notice_count' => $notice_count,
            'counts_by_level' => Arr::get(
                $result_set,
                'errs_cnts',
                array('a' => 0, 'aa' => 0, 'aaa' => 0)
            ),
        );
    }

    private function extractPlaceId(array $item): ?string
    {
        $place_id = Arr::get($item, 'place_id');
        if (is_string($place_id) && $place_id !== '') {
            return $place_id;
        }
        return null;
    }

    private function extractSnippet(array $item): ?string
    {
        $snippet = Arr::get($item, 'snippet');
        if (is_string($snippet) && $snippet !== '') {
            return $snippet;
        }
        return null;
    }

    private function runtimeConfigOverrides(array $options): array
    {
        return array(
            'lang' => (string) Arr::get($options, 'lang', ''),
            'resource_path' => (string) Arr::get($options, 'resource_path', ''),
            'doc_resource_path' => (string) Arr::get($options, 'doc_resource_path', ''),
        );
    }
}
