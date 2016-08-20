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
if (
  ! defined('KONTIKI_CONFIG_PATH') ||
  strpos(KONTIKI_CONFIG_PATH, 'kontiki.php') === false
)
{
	die('Define KONTIKI_CONFIG_PATH for this project.  e.g. "/path/to/kontiki.php"');
}
require_once KONTIKI_CONFIG_PATH;

// require
require_once __DIR__.'/classes/db.php';
require_once __DIR__.'/classes/auth.php';
require_once __DIR__.'/classes/users.php';
require_once __DIR__.'/classes/util.php';
require_once __DIR__.'/classes/view.php';
