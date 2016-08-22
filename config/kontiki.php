<?php
/**
 * config
 *
 * @package    part of Kontiki and a11yc
 */

return array(
	'db' => array(
		'default' => array(
			'dbtype' => 'sqlite',
			'path' => dirname(__DIR__).'/db/db.sqlite',
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
	'template_path' => dirname(__DIR__).'/libs/a11yc/views',
);
