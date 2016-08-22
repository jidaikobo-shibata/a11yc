<?php
/**
 * config
 *
 * @package    part of Kontiki and a11yc
 */

return array(
	'db' => array(
		'default' => array(
			'dbtype'   => 'mysql',
			'db'       => DB_NAME,
			'user'     => DB_USER,
			'host'     => DB_HOST,
			'password' => DB_PASSWORD,
		),
	),
	'template_path' => dirname(__DIR__).'/libs/a11yc/views',
);
