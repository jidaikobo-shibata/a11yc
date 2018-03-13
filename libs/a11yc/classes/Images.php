<?php
/**
 * A11yc\Images
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Images
{
	/**
	 * get images
	 * @return  Array
	 */
	public static function getImages($url)
	{
		$retvals = array();
		$str = Validate::ignoreElements(Validate::getHighLightedHtml($url));

		// at first, get images in a
		preg_match_all('/\<a [^\>]+\>.+?\<\/a\>/is', $str, $as);
		$n = 0;
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs = Validate::getAttributes($v);
			$href = Arr::get($attrs, 'href');
			$aria_hidden = Arr::get($attrs, 'aria-hidden');
			$tabindex = Arr::get($attrs, 'tabindex');

			// plural images can be exist.
			preg_match_all('/\<img[^\>]+\>/is', $v, $ass);
			foreach ($ass[0] as $vv)
			{
				$retvals[$n]['element'] = 'img (a)';
				$retvals[$n]['is_important'] = true;
				$retvals[$n]['href'] = $href;
				$retvals[$n]['aria_hidden'] = $aria_hidden;
				$retvals[$n]['tabindex'] = $tabindex;
				$retvals[$n]['attrs'] = Validate::getAttributes($vv);
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// secondary, get areas
		preg_match_all('/\<area [^\>]+\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			// link
			$attrs = static::getAttributes($v);
			$retvals[$n]['element'] = 'area';
			$retvals[$n]['is_important'] = true;
			$retvals[$n]['href'] = Arr::get($attrs, 'href');
			$retvals[$n]['attrs'] = $attrs;
			$n++;

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// get buttons
		preg_match_all('/\<button [^\>]+\>.+?\<\/button\>/is', $str, $as);
		foreach ($as[0] as $v)
		{
			if (strpos($v, '<img ') === false) continue;

			// link
			$attrs = static::getAttributes($v);
			$aria_hidden = Arr::get($attrs, 'aria-hidden');
			$tabindex = Arr::get($attrs, 'tabindex');

			// plural images can be exist.
			preg_match_all('/\<img[^\>]+\>/is', $v, $ass);
			foreach ($ass[0] as $vv)
			{
				$retvals[$n]['element'] = 'img (button)';
				$retvals[$n]['href'] = null;
				$retvals[$n]['is_important'] = 1;
				$retvals[$n]['aria_hidden'] = $aria_hidden;
				$retvals[$n]['tabindex'] = $tabindex;
				$retvals[$n]['attrs'] = static::getAttributes($vv);
				$n++;
			}

			// remove a within images
			$str = str_replace($v, '', $str);
		}

		// input and img
		$ms = Validate::getElementsByRe($str, 'ignores', 'tags', $force = true);

		if ( ! is_array($ms[1])) return $retvals;

		$targets = array('img', 'input');
		foreach ($ms[1] as $k => $v)
		{
			if ( ! in_array($v, $targets)) continue;
			$attrs = Validate::getAttributes($ms[0][$k]);
			if ($v == 'input' && ( ! isset($attrs['type']) || $attrs['type'] != 'image')) continue;

			$retvals[$n]['element'] = $v;
			$retvals[$n]['is_important'] = $v == 'input' ? true : false ;
			$retvals[$n]['href'] = NULL;
			$retvals[$n]['attrs'] = $attrs;
			$n++;
		}

		// tidy
		foreach ($retvals as $k => $v)
		{
			// src exists
			if (isset($v['attrs']['src']))
			{
				$retvals[$k]['attrs']['src'] = Util::enuniqueUri($v['attrs']['src']);
			}

			// alt exists
			if (isset($v['attrs']['alt']))
			{
				// empty alt
				if (empty($v['attrs']['alt']))
				{
					$retvals[$k]['attrs']['alt'] = '';
				}
				else
				{
					// alt of blank chars
					$alt = str_replace('　', ' ', $v['attrs']['alt']);
					$alt = trim($alt);

					if (empty($alt))
					{
						$retvals[$k]['attrs']['alt'] = '===a11yc_alt_of_blank_chars===';
					}
					// alt text
					else
					{
						$retvals[$k]['attrs']['alt'] = $v['attrs']['alt'];
					}
				}

				// newline in attr
				$retvals[$k]['attrs']['newline'] = preg_match("/[\n\r]/is", $v['attrs']['alt']);
			}

			// aria-*
			$retvals[$k]['aria'] = array();
			foreach ($retvals[$k]['attrs'] as $kk => $vv)
			{
				if (substr($kk, 0, 5) != 'aria-') continue;
				$retvals[$k]['aria'][$kk] = $vv;
			}
		}

		return $retvals;
	}
}