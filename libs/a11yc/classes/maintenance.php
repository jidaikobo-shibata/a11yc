<?php
/**
 * A11yc\Arr
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;
class Maintenance extends \Kontiki\Maintenance
{
	public static function version_check ()
	{
		// fetch current version
		$today = strtotime(date('Y-m-d'));
		$version_file = KONTIKI_DATA_PATH.'/version';
		$checked_file = KONTIKI_DATA_PATH.'/checked';
		$uptodate_file = KONTIKI_DATA_PATH.'/uptodate';
		if (file_exists($version_file))
		{
			$version = file_get_contents($version_file);
		}
		else
		{
			$version = $today;
			file_put_contents($version_file, $version);
		}

// https://api.github.com/repos/jidaikobo-shibata/a11yc/tags

		// run once in a day
		if ($version >= $today) return;

		// Github API
		ini_set('user_agent', 'file_get_contents');
		$json = file_get_contents('https://api.github.com/repos/jidaikobo-shibata/a11yc/tags');
		$repos = json_decode($json, true);

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
var_dump($vals);
echo '</textarea>';
die();

		// get a11yc
		$last_pushed_udate = array();
		foreach ($repos as $key => $repo)
		{
			if ($repo['id'] == '74967427')
			{
				$last_pushed_udate = strtotime(date('Y-m-d', strtotime($repo['pushed_at'])));
				break;
			}
		}

		// is lower?
		if ($version < $last_pushed_udate)
		{
			file_put_contents($uptodate_file, '1');
			return;
		}


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">';
var_dump($last_pushed_udate);
echo '</textarea>';
die();
	}
}