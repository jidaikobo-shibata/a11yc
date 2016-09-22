<?php
/**
 * A11yc
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */

// languages
include A11YC_PATH.'/languages/'.A11YC_LANG.'.php';

// include
include dirname(dirname(__DIR__)).'/libs/spyc/spyc.php';

// Autoloader
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');
