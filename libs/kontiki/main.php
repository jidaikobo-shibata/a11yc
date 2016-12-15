<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// performance
include_once __DIR__.'/classes/performance.php';
\Kontiki\Performance::set_time();
\Kontiki\Performance::set_memory();

// mb_internal_encoding()
mb_internal_encoding('UTF-8');

// load functions
include_once __DIR__.'/functions.php';
include_once __DIR__.'/classes/util.php';

// constants
defined('KONTIKI_DEFAULT_LANG') or \Kontiki\Util::error('set KONTIKI_DEFAULT_LANG');
define('KONTIKI_PATH', __DIR__);

// Autoloader
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'kontiki');
