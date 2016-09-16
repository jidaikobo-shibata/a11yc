<?php
/**
 * config
 *
 * @package    part of Kontiki and a11yc
 */

return array(
  // database
	'db' => array(
		'default' => array(
			'dbtype' => 'sqlite',
			'path' => dirname(__DIR__).'/path/to/db.sqlite',
		),
		/*
			'default' => array(
				'dbtype' => 'mysql',
				'db' => '',
				'user' => '',
				'host' => '',
				'password' => '',
			),
		*/
	),

  // template
	'template_path' => dirname(__DIR__).'/path/to/views',
);
