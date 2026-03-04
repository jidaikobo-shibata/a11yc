<?php

declare(strict_types=1);

$autoload_path = dirname(__DIR__) . '/vendor/autoload.php';
if (! file_exists($autoload_path)) {
    fwrite(STDERR, "vendor/autoload.php was not found.\n");
    exit(1);
}

require $autoload_path;

$langs = array('ja', 'en');
$compiled_dir = dirname(__DIR__) . '/resources/compiled';

if (! is_dir($compiled_dir) && ! mkdir($compiled_dir, 0777, true) && ! is_dir($compiled_dir)) {
    fwrite(STDERR, "Failed to create resources/compiled.\n");
    exit(1);
}

foreach ($langs as $lang) {
    $data = \Jidaikobo\A11yc\Yaml::buildCompiledData($lang);
    $path = \Jidaikobo\A11yc\Yaml::compiledPath($lang);

    $body = "<?php\n\nreturn " . var_export($data, true) . ";\n";
    if (file_put_contents($path, $body) === false) {
        fwrite(STDERR, "Failed to write {$path}.\n");
        exit(1);
    }

    fwrite(STDOUT, "Compiled {$lang} resources.\n");
}
