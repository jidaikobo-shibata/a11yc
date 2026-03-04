<?php

namespace Jidaikobo\A11yc;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml
{
    private const ALIAS_PLACEHOLDER_PREFIX = '__a11yc_alias__:';

    /** @var array<string, array<mixed>> */
    private static $data = array();

    /** @var array<string, array<int, string>> */
    private static $nonInterferences = array();

    private static function readResource($base_path, $filename)
    {
        if (empty($base_path)) {
            return '';
        }

        $path = rtrim($base_path, '/') . '/' . $filename;
        if (! file_exists($path)) {
            return '';
        }

        $data = file_get_contents($path);
        return is_string($data) ? $data : '';
    }

    private static function readOptionalResource(string $resource_path, string $doc_resource_path, string $filename): string
    {
        $data = self::readResource($doc_resource_path, $filename);
        if ($data !== '') {
            return $data;
        }

        return self::readResource($resource_path, $filename);
    }

    public static function fetch()
    {
        $cache_key = self::cacheKey();

        if (array_key_exists($cache_key, static::$data)) {
            return static::$data[$cache_key];
        }

        $compiled = self::loadCompiledData();
        if (is_array($compiled)) {
            static::$data[$cache_key] = $compiled;

            return static::$data[$cache_key];
        }

        if (! RuntimeConfig::allowYamlFallback()) {
            Util::error(
                'Compiled resources were not found. '
                . 'Run "composer compile-resources" before distribution '
                . 'or enable A11YC_ALLOW_YAML_FALLBACK for development.'
            );

            static::$data[$cache_key] = array();

            return static::$data[$cache_key];
        }

        static::$data[$cache_key] = self::loadYamlData();

        return static::$data[$cache_key];
    }

    public static function buildCompiledData(string $lang): array
    {
        return RuntimeConfig::withOverrides(
            array('lang' => $lang),
            static function (): array {
                return self::loadYamlData();
            }
        );
    }

    public static function compiledPath(?string $lang = null): string
    {
        if ($lang !== null && $lang !== '') {
            return RuntimeConfig::rootPath() . '/resources/compiled/' . $lang . '.php';
        }

        $resource_path = RuntimeConfig::resourcePath();

        return rtrim(dirname($resource_path), '/') . '/compiled/' . basename($resource_path) . '.php';
    }

    private static function loadCompiledData(): ?array
    {
        if (RuntimeConfig::docResourcePath() !== '') {
            return null;
        }

        $compiled_path = self::compiledPath();
        if (! file_exists($compiled_path)) {
            return null;
        }

        $compiled = require $compiled_path;
        if (! is_array($compiled)) {
            return null;
        }

        return $compiled;
    }

    private static function loadYamlData(): array
    {
        $doc_resource_path = RuntimeConfig::docResourcePath();
        $resource_path = RuntimeConfig::resourcePath();

        $resources = array(
            self::readResource($resource_path, 'standards.yml'),
            self::readResource($resource_path, 'levels.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'principles.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'guidelines.yml'),
            self::readResource($resource_path, 'criterions.yml'),
            self::readResource($resource_path, 'errors.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'techs_codes.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'tests.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'processes.yml'),
        );

        $parsed = array();
        $anchors = array();

        foreach ($resources as $resource) {
            $fragment = self::parseResourceData($resource, $anchors);
            if (! is_array($fragment) || $fragment === array()) {
                continue;
            }

            $parsed = array_replace_recursive($parsed, $fragment);
            $anchors = array_replace($anchors, self::extractAnchors($resource, $fragment));
        }

        return $parsed;
    }

    public static function each($name)
    {
        $ret = self::fetch();
        return Arr::get($ret, $name, array());
    }

    public static function nonInterferences()
    {
        $cache_key = self::cacheKey();

        if (array_key_exists($cache_key, static::$nonInterferences)) {
            return static::$nonInterferences[$cache_key];
        }

        $non_interferences = array();
        $yml = self::fetch();
        foreach ($yml['criterions'] as $criterion => $v) {
            if (! isset($v['non-interference'])) {
                continue;
            }
            $non_interferences[] = $criterion;
        }
        static::$nonInterferences[$cache_key] = $non_interferences;

        return static::$nonInterferences[$cache_key];
    }

    private static function cacheKey(): string
    {
        return RuntimeConfig::resourcePath() . '|' . RuntimeConfig::docResourcePath();
    }

    private static function parseYaml(string $yaml)
    {
        if (class_exists(SymfonyYaml::class)) {
            return SymfonyYaml::parse($yaml);
        }

        Util::error('No YAML parser is available');
        return array();
    }

    private static function parseResourceData(string $yaml, array $anchors): array
    {
        if ($yaml === '') {
            return array();
        }

        $yaml = preg_replace_callback(
            '/(:\s*)\*([A-Za-z0-9_-]+)/',
            static function (array $matches): string {
                return $matches[1] . '"' . self::ALIAS_PLACEHOLDER_PREFIX . $matches[2] . '"';
            },
            $yaml
        );
        if (! is_string($yaml)) {
            return array();
        }

        $parsed = self::parseYaml($yaml);
        if (! is_array($parsed)) {
            return array();
        }

        return self::restoreAliases($parsed, $anchors);
    }

    private static function restoreAliases($value, array $anchors)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::restoreAliases($item, $anchors);
            }

            return $value;
        }

        if (! is_string($value)) {
            return $value;
        }

        if (strpos($value, self::ALIAS_PLACEHOLDER_PREFIX) !== 0) {
            return $value;
        }

        $alias = substr($value, strlen(self::ALIAS_PLACEHOLDER_PREFIX));
        if (array_key_exists($alias, $anchors)) {
            return $anchors[$alias];
        }

        return self::fallbackAliasValue($alias);
    }

    private static function fallbackAliasValue(string $alias)
    {
        if (preg_match('/^g(.+)$/', $alias, $matches)) {
            return array('code' => $matches[1]);
        }

        if (preg_match('/^lv([1-3])$/', $alias, $matches)) {
            $names = array(
                '1' => 'A',
                '2' => 'AA',
                '3' => 'AAA',
            );

            return array(
                'name' => $names[$matches[1]],
            );
        }

        return $alias;
    }

    private static function extractAnchors(string $yaml, array $parsed): array
    {
        if ($yaml === '' || $parsed === array()) {
            return array();
        }

        $anchors = array();

        if (isset($parsed['levels']) && is_array($parsed['levels'])) {
            if (
                preg_match_all('/^\s*-\s*&([A-Za-z0-9_-]+)\s*$/m', $yaml, $matches) &&
                isset($matches[1])
            ) {
                foreach (array_values($matches[1]) as $index => $anchor) {
                    if (array_key_exists($index, $parsed['levels'])) {
                        $anchors[$anchor] = $parsed['levels'][$index];
                    }
                }
            }
        }

        if (isset($parsed['principles']) && is_array($parsed['principles'])) {
            $anchors = array_replace(
                $anchors,
                self::extractMapAnchors($yaml, $parsed['principles'])
            );
        }

        if (isset($parsed['guidelines']) && is_array($parsed['guidelines'])) {
            $anchors = array_replace(
                $anchors,
                self::extractMapAnchors($yaml, $parsed['guidelines'])
            );
        }

        if (isset($parsed['criterions']) && is_array($parsed['criterions'])) {
            $anchors = array_replace(
                $anchors,
                self::extractMapAnchors($yaml, $parsed['criterions'])
            );
        }

        return $anchors;
    }

    private static function extractMapAnchors(string $yaml, array $items): array
    {
        $anchors = array();

        if (
            ! preg_match_all('/^\s{2,}[^:\n]+:\s*&([A-Za-z0-9_-]+)\s*$/m', $yaml, $matches) ||
            ! isset($matches[1])
        ) {
            return $anchors;
        }

        $values = array_values($items);
        foreach (array_values($matches[1]) as $index => $anchor) {
            if (array_key_exists($index, $values)) {
                $anchors[$anchor] = $values[$index];
            }
        }

        return $anchors;
    }
}
