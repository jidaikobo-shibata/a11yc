<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

// performance
include __DIR__.'/classes/performance.php';
\Kontiki\Performance::set_time();
\Kontiki\Performance::set_memory();

// mb_internal_encoding()
mb_internal_encoding('UTF-8');

// constants
defined('KONTIKI_DEFAULT_LANG') or die('set KONTIKI_DEFAULT_LANG');
define('KONTIKI_PATH', __DIR__);

// load function
include __DIR__.'/functions.php';

// Autoloader
include __DIR__.'/classes/util.php';
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'kontiki');
