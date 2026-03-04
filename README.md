# a11yc

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

`a11yc` is the core accessibility checking library extracted from A11YC.

This package is a PHP library, not the old standalone web application. It provides HTML and URL analysis APIs that can be embedded in other applications such as the standalone UI layer or the `jwp-a11y` WordPress plugin.

## Requirements

- PHP 7.4 or later
- Composer

## Installation

```bash
composer require jidaikobo/a11yc
```

If you are working on this repository directly:

```bash
composer install
```

## Compiled Resources

Runtime resource loading now expects precompiled PHP arrays in `resources/compiled/`.

The distributed package should include:

- `resources/compiled/ja.php`
- `resources/compiled/en.php`

When you change resource source files under `resources/`, rebuild the compiled files before committing or packaging:

```bash
composer compile-resources
```

This command regenerates the compiled PHP arrays from the YAML source files.

At runtime, `a11yc` uses the compiled files first. If they are missing, the library raises an error unless YAML fallback is explicitly enabled for development.

## Development-Only YAML Fallback

For local development only, you can allow direct YAML loading with a Git-ignored `config.development.php` file in the package root:

```php
<?php

return array(
    'allow_yaml_fallback' => true,
);
```

This file is not intended for distribution. In normal packaged environments, keep compiled resources available and do not rely on YAML fallback.

## What This Package Does

- Fetch and analyze a page by URL
- Analyze already-available HTML
- Return normalized issue lists and summary counts
- Extract image metadata used by the analyzer
- Load WCAG-related resources from bundled YAML files

## Basic Usage

### Analyze a URL

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Jidaikobo\A11yc\Analyzer;

$analyzer = new Analyzer();

$result = $analyzer->analyzeUrl('https://example.com/', array(
    'do_link_check' => false,
    'do_css_check' => false,
    'include_images' => true,
));
```

### Analyze HTML

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Jidaikobo\A11yc\Analyzer;

$html = '<!doctype html><html lang="ja"><head><title>Example</title></head><body><img src="/logo.png" alt=""></body></html>';

$analyzer = new Analyzer();

$result = $analyzer->analyzeHtml($html, array(
    'url' => 'https://example.com/',
    'is_partial' => false,
    'do_link_check' => false,
    'do_css_check' => false,
    'include_images' => true,
));
```

## Result Format

`Analyzer::analyzeUrl()` and `Analyzer::analyzeHtml()` return an array with these top-level keys:

- `meta`
- `summary`
- `issues`
- `images`

### `meta`

- `url`: analyzed URL
- `requested_url`: original requested URL (only for `analyzeUrl()`)
- `exists`: whether fetching succeeded (only for `analyzeUrl()`)
- `user_agent`: effective user agent string
- `version`: `A11YC_VERSION` if defined
- `check_count`: number of executed checks
- `analyzed_at`: ISO 8601 timestamp

### `summary`

- `error_count`
- `notice_count`
- `counts_by_level`

`counts_by_level` contains:

- `a`
- `aa`
- `aaa`

### `issues`

Each issue is normalized into a flat array:

- `id`
- `type` (`error` or `notice`)
- `message`
- `level`
- `criterion_keys`
- `place_id`
- `snippet`

### `images`

When `include_images` is enabled, image data includes:

- `element`
- `src`
- `alt`
- `href`
- `is_important`
- `aria`

## Available Options

Both `Analyzer::analyzeUrl()` and `Analyzer::analyzeHtml()` accept an options array.

- `url`: base URL for analysis (`analyzeHtml()` only; default `about:blank`)
- `user_agent`: user agent used for fetching HTML/CSS
- `checks`: array of check class names to run; omitted means all available checks
- `is_partial`: skip full-document assumptions for partial HTML analysis
- `do_link_check`: enable slower link validation checks
- `do_css_check`: enable CSS fetching and CSS-related checks
- `include_images`: include extracted image data in the result

## Lower-Level API

If you need the raw validation result set before normalization:

```php
<?php

use Jidaikobo\A11yc\Validate;

$resultSet = Validate::html(
    'https://example.com/',
    $html,
    array(),
    'using',
    true,
    array(
        'is_partial' => false,
        'do_link_check' => false,
        'do_css_check' => false,
    )
);
```

The normalized API via `Analyzer` is recommended for new integrations.

## License

MIT
