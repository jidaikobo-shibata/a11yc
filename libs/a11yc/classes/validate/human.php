<?php
/**
 * A11yc\Validate_Human
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Validate_Human extends Validate
{
	public static $humans = array();

	/**
	 * prepare
	 *
	 * @return Array
	 */
	private static function prepare()
	{
		// cache
		if ( ! empty(static::$humans)) return static::$humans;

		// get target url
		$url = Crawl::get_target_path();
		if ( ! $url) return false;

		// get errors
		$sql = 'SELECT `human_src` FROM '.A11YC_TABLE_PAGES.' WHERE `url` = ?'.Controller_Setup::curent_version_sql().';';
		$human_src = Db::fetch($sql, array($url));
		if ( ! isset($human_src['human_src']) || empty($human_src['human_src'])) return false;

		// divide strings
		$strs = explode('##A11YC_SRC_DIV##', $human_src['human_src']);
		$strs = array_map('trim', $strs);
		foreach ($strs as $str)
		{
			if(empty($str)) continue;
			$html = '';

			// get error or notice
			$e_or_n = $str[0] == 'E' || $str[0] == 'e' ? 'error' : 'notice';
			$str = mb_substr($str, mb_strpos($str, "\n") + 1);

			// get criterion
			$criterion = trim(mb_substr($str, 0, mb_strpos($str, "\n")));
			$str = mb_substr($str, mb_strpos($str, "\n") + 1);

			// get code
			$code = trim(mb_substr($str, 0, mb_strpos($str, "\n")));
			$str = mb_substr($str, mb_strpos($str, "\n") + 1);

			// get message and html
			$message = trim(mb_substr($str, 0, mb_strpos($str, "\n")));
			$html = mb_substr($str, mb_strpos($str, "\n") + 1);
			if (empty($html)) continue;

			// is single?
			$validate_type = 'nontag';
			if (preg_match('/\<[^\>]+?\>/', $html))
			{
				$validate_type = 'single';
			}
			elseif(preg_match('/\<[^\>]+?\>[^\<]+?\</', $html))
			{
				$validate_type = 'elements';
			}

			$key = base64_encode($html);

			// result
			static::$humans[$key] = array(
				'validate_type' => $validate_type,
				'criterion' => $criterion,
				'code' => $code,
				'e_or_n' => $e_or_n,
				'message' => $message,
				'html' => $html,
			);
		}
	}

	/**
	 * type
	 *
	 * @param  string $type [elements|single|nontag]
	 * @return Array
	 */
	private static function type($type)
	{
		self::prepare();

		$str = static::ignore_elements(static::$hl_html);

		// add errors
		$retvals = array();
		$n = 0;
		foreach (static::$humans as $human)
		{
			if ($human['validate_type'] != $type) continue;
			$key = base64_encode($human['html']);

			static::$error_ids[$key][$n]['id'] = $human['html'];
			static::$error_ids[$key][$n]['str'] = $human['html'];
			static::add_error_to_html($key, static::$error_ids, 'ignores');
			$n++;
		}
	}

	/**
	 * elements
	 *
	 * @return Void
	 */
	public static function elements()
	{
		self::type('elements');
	}

	/**
	 * single
	 *
	 * @return Void
	 */
	public static function single()
	{
		self::type('single');
	}

	/**
	 * nontag
	 *
	 * @return Void
	 */
	public static function nontag()
	{
		self::type('nontag');
	}
}
