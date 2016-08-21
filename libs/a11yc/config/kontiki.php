<?php
/**
 * config
 *
 * @package    part of Kontiki
 */

// database - sqlite or mysql
define('KONTIKI_DBTYPE', 'sqlite');

// sqlite
define('KONTIKI_SQLITE_PATH', dirname(__DIR__).'/db/db.sqlite');

// mysql
define('KONTIKI_MYSQL_NAME', '');
define('KONTIKI_MYSQL_USER', '');
define('KONTIKI_MYSQL_HOST', '');
define('KONTIKI_MYSQL_PASSWORD', '');

// view
define('KONTIKI_VIEWS_PATH', dirname(__DIR__).'/views');
