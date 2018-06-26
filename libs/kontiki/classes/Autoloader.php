<?php
/**
 * Kontiki\Autoloader
 *
 * @package    part of Kontiki
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @forked     FuelPHP core/classes/autoloader.php
 * @link       http://www.jidaikobo.com
 */
namespace Kontiki;

class Autoloader
{
	public static $classes = array();
	public static $core_namespaces = array();

	/**
	 * add path
	 *
	 * @param  String $path
	 * @param  String $namespace
	 * @return Void
	 */
	public static function addPath($path, $namespace = '')
	{
		spl_autoload_register(
			function ($class_name) use ($path, $namespace)
			{
				// check namespace
				$class = $class_name;
				$strlen = strlen($namespace);

				if (substr($class, 0, $strlen) !== $namespace) return;
				$class = substr($class, $strlen + 1);

				// backslashes mean directories
				$file_path = $path.\Kontiki\Autoloader::prepPath($class);

				// require
				if (file_exists($file_path))
				{
					include $file_path;
				}
				// search core namespace
				else
				{
					$loaded = false;
					$classes = explode('\\', $class_name);
					$naked_class = end($classes);

					// search loaded class
					foreach (array_keys(\Kontiki\Autoloader::$core_namespaces) as $core_namespace)
					{
						$ns_class = $core_namespace.'\\'.$naked_class;
						if (array_key_exists($ns_class, \Kontiki\Autoloader::$classes))
						{
							$loaded = true;
							break;
						}
					}

					// search unloaded class
					if ( ! $loaded)
					{
						foreach (\Kontiki\Autoloader::$core_namespaces as $core_namespace => $core_path)
						{
							$ns_class = $core_namespace.'\\'.$naked_class;
							$file_path = $core_path.\Kontiki\Autoloader::prepPath($naked_class);

							if (file_exists($file_path))
							{
								include_once $file_path;
								$loaded = true;
								break;
							}
						}
					}

					// loaded or not
					if ($loaded)
					{
						class_alias($ns_class, $class_name);
					}
					else
					{
				 	return false;
					}
				}

				// loaded classes
				\Kontiki\Autoloader::$classes[$class_name] = $file_path;

				// init
				if (method_exists($class_name, '_init') and is_callable($class_name.'::_init'))
				{
					call_user_func($class_name.'::_init');
				}
			}
		);
	}

	/**
	 * add core namespace
	 *
	 * @param  string $path
	 * @param  string $namespace
	 * @param  bool   $prefix
	 * @return void
	 */
	public static function addCoreNamespace($path, $namespace, $prefix = true)
	{
		if ($prefix)
		{
			static::$core_namespaces = array_merge(array($namespace => $path), static::$core_namespaces);
		}
		else
		{
			static::$core_namespaces[$namespace] = $path;
		}
	}

	/**
	 * Prepares a given path by making sure the directory separators are correct.
	 *
	 * @param   string  $path  Path to prepare
	 * @return  string  Prepped path
	 */
	public static function prepPath($path)
	{
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path).'.php';
	}
}
