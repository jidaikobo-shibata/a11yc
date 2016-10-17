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

// path
defined('A11YC_CLASSES_PATH')  or define('A11YC_CLASSES_PATH',  A11YC_PATH.'/classes');
defined('A11YC_RESOURCE_PATH') or define('A11YC_RESOURCE_PATH', A11YC_PATH.'/resources/'.A11YC_LANG);

// tables
defined('A11YC_TABLE_PAGES')  or define('A11YC_TABLE_PAGES',  'a11y_pages');
defined('A11YC_TABLE_CHECKS') or define('A11YC_TABLE_CHECKS', 'a11y_checks');
defined('A11YC_TABLE_SETUP')  or define('A11YC_TABLE_SETUP',  'a11y_setup');
defined('A11YC_TABLE_BULK')   or define('A11YC_TABLE_BULK',   'a11y_bulk');

// url
define('A11YC_VALIDATE_URL', dirname(A11YC_URL));
defined('A11YC_BULK_URL') or define('A11YC_BULK_URL', A11YC_URL.'?c=bulk&amp;a=index');
defined('A11YC_PAGES_URL') or define('A11YC_PAGES_URL', A11YC_URL.'?c=pages&amp;a=index');
defined('A11YC_CHECKLIST_URL') or define('A11YC_CHECKLIST_URL', A11YC_URL.'?c=checklist&amp;a=index&amp;url=');
defined('A11YC_DOC_URL') or define('A11YC_DOC_URL', A11YC_URL.'?c=docs&amp;a=each&amp;code=');

// languages
include A11YC_PATH.'/languages/'.A11YC_LANG.'.php';

// include
include dirname(dirname(__DIR__)).'/libs/spyc/spyc.php';

// Autoloader
\Kontiki\Util::add_autoloader_path(__DIR__.'/classes/', 'a11yc');
