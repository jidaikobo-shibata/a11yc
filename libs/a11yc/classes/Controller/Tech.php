<?php
/**
 * A11yc\Controller\Tech
 *
 * @usage
 * This controller is for engineering.
 * Genarate Yaml
 * Do not forget other languages
 * @source https://waic.jp/docs/WCAG-TECHS/complete.html
 * @source https://www.w3.org/TR/WCAG20-TECHS/complete.html
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc\Controller;

class Tech
{
	/**
	 * Import
	 *
	 * @return Void
	 */
	public static function actionImport()
	{
		// physical path of Techniques for WCAG 2.0 complete.html
		$path = '/Users/FOOBAR/Desktop/complete.html';
		if ( ! file_exists($path)) Util::error();
		$text = file_get_contents($path);
		$text = preg_replace('/\<p[^\>]*?\>.+?\<\/p\>/mis', '', $text);
		$techs = explode('<h3>', $text);

		$results = array();
		foreach ($techs as $tech)
		{
			if (substr($tech, 0, 7) !== '<a name') continue;
			$each = explode('</h3>', $tech);
			if (count($each) !== 2) continue;

			// code
			if ( ! preg_match('/".+?"/', $each[0], $m)) continue;
			$code = str_replace('"', '', $m[0]);

			// allowed types
			$types = Values::techsTypes();
			$type = preg_replace('/\d/', '', $code);
			if ( ! in_array($type, $types)) continue;

			// title
			$title = trim(strip_tags($each[0]));

			// applicability
			$app = mb_substr($each[1], 0, mb_strpos($each[1], '-description">'));
			preg_match_all('/\d\.\d\.\d{1,2}/', strip_tags($app), $ms);
			$apps = array_unique($ms[0]);
			sort($apps);
			$apps = array_map(function($s){return str_replace('.', '-', $s);}, $apps);

			// results
			$results[$code] = array(
				'title' => $title,
				'type' => $type,
				'apps' => $apps,
			);
		}

		// reverse value
		$results2 = array();
		foreach ($results as $k => $v)
		{
			foreach ($v['apps'] as $code)
			{
				if ($v['type'] == 'F')
				{
					$results2[$code]['f'][] = $k;
				}
				else
				{
					$results2[$code]['t'][] = $k;
				}
			}
		}
		ksort($results2);

		if ( ! class_exists('Spyc'))
		{
			include A11YC_LIB_PATH.'/spyc/Spyc.php';
		}
		$yml = \Spyc::YAMLDump($results);
		$yml2 = \Spyc::YAMLDump($results2);

		echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
		echo $yml;
		echo '</textarea>';
		echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
		echo $yml2;
		echo '</textarea>';
		die();
	}
}
