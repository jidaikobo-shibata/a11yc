<?php

namespace Jidaikobo\A11yc;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml
{
    private const ALIAS_PLACEHOLDER_PREFIX = '__a11yc_alias__:';

    /** @var array<mixed>|null */
    private static $data = null;

    /** @var array<int, string>|null */
    private static $nonInterferences = null;

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
        if (is_array(static::$data)) {
            return static::$data;
        }

        $doc_resource_path = defined('A11YC_DOC_RESOURCE_PATH') ? A11YC_DOC_RESOURCE_PATH : '';
        $resource_path = RuntimeConfig::resourcePath();

        $resources = array(
            self::readResource($resource_path, 'standards.yml'),
            self::readResource($resource_path, 'levels.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'principles.yml'),
            self::readOptionalResource($resource_path, $doc_resource_path, 'guidelines.yml'),
            self::readResource($resource_path, 'criterions.yml'),
            self::readResource($resource_path, 'errors.yml'),
            self::readResource($resource_path, 'techs.yml'),
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

        static::$data = $parsed;

        return static::$data;
    }

    public static function each($name)
    {
        $ret = self::fetch();
        return Arr::get($ret, $name, array());
    }

    public static function nonInterferences()
    {
        if (is_array(static::$nonInterferences)) {
            return static::$nonInterferences;
        }

        $non_interferences = array();
        $yml = self::fetch();
        foreach ($yml['criterions'] as $criterion => $v) {
            if (! isset($v['non-interference'])) {
                continue;
            }
            $non_interferences[] = $criterion;
        }
        static::$nonInterferences = $non_interferences;

        return static::$nonInterferences;
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
