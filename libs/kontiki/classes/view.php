<?php
/**
 * Kontiki\View
 *
 * @package    part of Kontiki
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace Kontiki;
class View
{
	public $vals = array();

	/**
	 * fetch_tpl
	 * fetch specified / fallback template path
	 *
	 * @return  string
	 */
	public static function fetch_tpl($tpl)
	{
		$path = KONTIKI_VIEW_PATH.'/'.$tpl;
		$fallback = dirname(__DIR__).'/templates/'.$tpl;

		if (file_exists($path))
		{
			return $path;
		}
		elseif(file_exists($fallback))
		{
			return $fallback;
		}
		return false;
	}

	/**
	 * assign
	 *
	 * @return  void
	 */
	public function assign($key, $val, $raw = false)
	{
		$this->vals[$key] = $val;
	}

	/**
	 * display
	 *
	 * @return  void
	 */
	public function display($title, $body)
	{
		// extract
		extract ($this->vals);

		// render
		include(static::fetch_tpl('header.php'));
		echo $body;
		include(static::fetch_tpl('footer.php'));
	}
}
