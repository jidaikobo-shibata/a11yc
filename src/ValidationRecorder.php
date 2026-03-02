<?php

/**
 * Jidaikobo\A11yc\ValidationRecorder
 */

namespace Jidaikobo\A11yc;

class ValidationRecorder extends Validate
{
    private static function normalizeTargetKey(?string $target): string
    {
        // 配列キーでは null が空文字扱いになって曖昧になるため、
        // 「位置未特定」は専用の sentinel 文字列で表す。
        return $target === null || $target === ''
            ? '__unspecified__'
            : $target;
    }

    public static function error(
        $url,
        $error_name,
        $count,
        $id,
        $str,
        ValidationContext $runtime
    ) {
        $log_id = static::normalizeTargetKey(
            is_string($id) ? $id : null
        );
        $machineChecks = &static::machineChecksForUrl($url, $runtime);
        $errorIds = &static::errorIdsForUrl($url, $runtime);

        $machineChecks[$error_name][$log_id] = -1;
        $errorIds[$error_name][$count]['id'] = $id;
        $errorIds[$error_name][$count]['str'] = Util::s($str);
        static::incrementLevelCount($error_name, $runtime);
    }

    public static function recordErrorOrPass(
        $exp,
        $url,
        $error_name,
        $count,
        $id,
        $str,
        ValidationContext $runtime
    ) {
        $exp = (bool) $exp;
        if ($exp) {
            self::error($url, $error_name, $count, $id, $str, $runtime);
            return;
        }
        self::recordMachineCheck($url, $error_name, $id, 2, $runtime);
    }

    public static function recordMachineCheck(
        $url,
        $error_name,
        $target_str,
        $status,
        ValidationContext $runtime
    ) {
        $targetKey = static::normalizeTargetKey(
            is_string($target_str) ? $target_str : null
        );
        $machineChecks = &static::machineChecksForUrl($url, $runtime);
        if (is_array($error_name)) {
            foreach ($error_name as $each_error_name) {
                $machineChecks[$each_error_name][$targetKey] = $status;
            }
            return;
        }
        $machineChecks[$error_name][$targetKey] = $status;
    }

    private static function incrementLevelCount(
        string $error_name,
        ValidationContext $runtime
    ): void {
        $current_err = Arr::get(Yaml::fetch(), 'errors.' . $error_name, array());
        if (! is_array($current_err) || isset($current_err['notice'])) {
            return;
        }

        $criterion = Arr::get($current_err, 'criterions.0');
        if (! is_string($criterion) || $criterion === '') {
            return;
        }

        $level = strtolower((string) Arr::get(
            Yaml::fetch(),
            'criterions.' . $criterion . '.level.name',
            ''
        ));
        if ($level === '') {
            return;
        }

        if (! array_key_exists($level, $runtime->errCnts)) {
            $runtime->errCnts[$level] = 0;
        }

        $runtime->errCnts[$level]++;
    }
}
