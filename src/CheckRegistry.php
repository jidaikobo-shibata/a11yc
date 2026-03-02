<?php

namespace Jidaikobo\A11yc;

final class CheckRegistry
{
    /** @var array<string, array{0: string, 1: string}> */
    private static $extensions = array();

    public static function availableChecks(): array
    {
        $pattern = __DIR__ . '/Validate/Check/*.php';
        $files = glob($pattern);

        if ($files === false) {
            return array();
        }

        $checks = array();
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name === '') {
                continue;
            }
            $checks[] = $name;
        }

        if (! empty(static::$extensions)) {
            $checks = array_merge($checks, array_keys(static::$extensions));
        }

        $checks = array_values(array_unique($checks));
        sort($checks);

        return $checks;
    }

    public static function register(string $name, string $class, string $method = 'check'): void
    {
        static::$extensions[$name] = array($class, $method);
    }

    /** @param array<string, array{0: string, 1?: string}> $map */
    public static function extend(array $map): void
    {
        foreach ($map as $name => $definition) {
            if (! is_array($definition) || empty($definition[0])) {
                continue;
            }

            static::register(
                (string) $name,
                (string) $definition[0],
                isset($definition[1]) ? (string) $definition[1] : 'check'
            );
        }
    }

    /** @return array{0: string, 1: string} */
    public static function resolve(string $name): array
    {
        if (isset(static::$extensions[$name])) {
            return static::$extensions[$name];
        }

        return array(
            'Jidaikobo\\A11yc\\Validate\\Check\\' . $name,
            'check',
        );
    }

    public static function clearExtensions(): void
    {
        static::$extensions = array();
    }
}
