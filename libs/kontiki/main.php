<?php
/**
 * Kontiki
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

// KONTIKI_PATH
define('KONTIKI_PATH', __DIR__);

// mb_internal_encoding()
mb_internal_encoding('UTF-8');

// performance
include_once KONTIKI_PATH.'/classes/performance.php';
Performance::set_time();
Performance::set_memory();

// load functions
include_once KONTIKI_PATH.'/functions.php';
include_once KONTIKI_PATH.'/classes/util.php';

// Autoloader
Util::add_autoloader_path(KONTIKI_PATH.'/classes/', 'kontiki');

// constants
defined('KONTIKI_DEFAULT_LANG') or Util::error('set KONTIKI_DEFAULT_LANG');
defined('KONTIKI_DEFAULT_TIMZONE') or Util::error('set KONTIKI_DEFAULT_TIMZONE');

// timezone
date_default_timezone_set(KONTIKI_DEFAULT_TIMZONE);
