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

// config
if ( ! defined('KONTIKI_CONFIG_PATH'))
{
  define('KONTIKI_CONFIG_PATH', dirname(dirname(__DIR__)).'/config');
}
include KONTIKI_CONFIG_PATH.'/kontiki.php';

// load function
include __DIR__.'/functions.php';

// load classes
include __DIR__.'/classes/db.php';
include __DIR__.'/classes/auth.php';
include __DIR__.'/classes/users.php';
include __DIR__.'/classes/util.php';
include __DIR__.'/classes/view.php';
