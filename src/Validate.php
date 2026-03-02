<?php

namespace Jidaikobo\A11yc;

use Jidaikobo\A11yc\Yaml;

class Validate
{
    public static function codes2name($codes = array())
    {
        return md5(join($codes));
    }

    public static function html2id($html)
    {
        return str_replace(array('+', '/', '*', '='), '', base64_encode($html));
    }

    protected static function css($url, ValidationContext $runtime = null)
    {
        $ua = $runtime instanceof ValidationContext ? $runtime->userAgent : 'using';
        return CssAnalysis::fetchCss($url, $ua);
    }

    public static function html(
        $url,
        $html,
        $codes = array(),
        $ua = 'using',
        $force = false,
        array $options = array()
    ) {
        return RuntimeConfig::withOverrides(
            self::runtimeConfigOverrides($options),
            static function () use ($url, $html, $codes, $ua, $options) {
                $html = ! is_string($html) ? '' : $html;
                $context = new ElementAnalysisContext();
                $runtime = new ValidationContext();
                $runtime->userAgent = is_string($ua) ? $ua : 'using';
                $runtime->isPartial = (bool) Arr::get($options, 'is_partial', false);
                $runtime->doLinkCheck = (bool) Arr::get($options, 'do_link_check', false);
                $runtime->doCssCheck = (bool) Arr::get($options, 'do_css_check', false);

                $codes = $codes ?: CheckRegistry::availableChecks();
                Element\Get::setSourceHtml($url, $html, $context);
                $runtime->errorIds[$url] = array();
                $runtime->machineChecks[$url] = array();

                foreach ($codes as $class_name) {
                    list($class, $method) = CheckRegistry::resolve($class_name);
                    $class::$method($url, $context, $runtime);
                }

                $all_errs = self::setMessage($url, $runtime);

                $result = array();
                $result['errors'] = $all_errs;
                $result['html'] = $html;
                $result['errs_cnts'] = static::errorCounts($runtime);
                $result['machine_checks'] =
                    isset($runtime->machineChecks[$url]) ?
                    $runtime->machineChecks[$url] :
                    array();
                return $result;
            }
        );
    }

    public static function &errorCounts(ValidationContext $runtime): array
    {
        return $runtime->errCnts;
    }

    public static function &errorIdsForUrl($url, ValidationContext $runtime): array
    {
        if (! isset($runtime->errorIds[$url])) {
            $runtime->errorIds[$url] = array();
        }

        return $runtime->errorIds[$url];
    }

    public static function &machineChecksForUrl($url, ValidationContext $runtime): array
    {
        if (! isset($runtime->machineChecks[$url])) {
            $runtime->machineChecks[$url] = array();
        }

        return $runtime->machineChecks[$url];
    }

    public static function machineCheckStatus(
        $url,
        $errorName,
        $target,
        ValidationContext $runtime,
        $default = null
    ) {
        $machineChecks = static::machineChecksForUrl($url, $runtime);

        if (! isset($machineChecks[$errorName])) {
            return $default;
        }

        if (! array_key_exists($target, $machineChecks[$errorName])) {
            return $default;
        }

        return $machineChecks[$errorName][$target];
    }

    public static function isPartialRun(ValidationContext $runtime): bool
    {
        return (bool) $runtime->isPartial;
    }

    public static function shouldDoLinkCheck(ValidationContext $runtime): bool
    {
        return (bool) $runtime->doLinkCheck;
    }

    public static function shouldDoCssCheck(ValidationContext $runtime): bool
    {
        return (bool) $runtime->doCssCheck;
    }

    private static function setMessage($url, ValidationContext $runtime)
    {
        $yml = Yaml::fetch();
        $all_errs = array(
            'notices' => array(),
            'errors' => array()
        );
        $errorIds = static::errorIdsForUrl($url, $runtime);
        if ($errorIds) {
            foreach ($errorIds as $code => $errs) {
                $num_of_err = count($errs);
                foreach ($errs as $key => $err) {
                    $err_type = isset($yml['errors'][$code]) && isset($yml['errors'][$code]['notice']) ?
                        'notices' :
                        'errors';
                    $place = (string) Arr::get($err, 'id', '');
                    $snippet = (string) Arr::get($err, 'str', '');
                    $all_errs[$err_type][] = array(
                        'code_str' => $code,
                        'place_id' => $place,
                        'snippet' => $snippet,
                        'num_of_err' => $num_of_err,
                        'index' => $key,
                    );
                }
            }
        }
        return $all_errs;
    }

    public static function addErrorToHtml(
        $url,
        $error_id,
        $s_errors = array(),
        $ignore_vals = '',
        $issue_html = ''
    ) {
        // Highlight rendering was moved to the standalone application layer.
        return;
    }

    public static function setCurrentErr($url, $error_id, $issue_html = '')
    {
        $yml = Yaml::fetch();
        $current_err = array();

        if (! isset($yml['errors'][$error_id])) {
            return false;
        } else {
            $current_err = $yml['errors'][$error_id];
        }
        return $current_err;
    }

    private static function runtimeConfigOverrides(array $options): array
    {
        return array(
            'lang' => Arr::get($options, 'lang', ''),
            'resource_path' => Arr::get($options, 'resource_path', ''),
            'doc_resource_path' => Arr::get($options, 'doc_resource_path', ''),
        );
    }
}
