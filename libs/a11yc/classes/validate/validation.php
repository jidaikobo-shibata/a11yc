<?php
/**
 * A11yc\Validation
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Validate_Validation extends Validate
{
	/**
	 * appropriate heading descending
	 *
	 * @return Void
	 */
	public static function appropriate_heading_descending()
	{
		$str = static::ignore_elements(static::$hl_html);

		$secs = preg_split("/\<(h[^\>?]+?)\>(.+?)\<\/h\d/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		if ( ! $secs[0]) return;

		// get first appeared heading
		$prev = 1;
		foreach ($secs as $sec)
		{
			if (isset($sec[1]) && is_numeric($sec[1]))
			{
				$prev = $sec[1];
				break;
			}
		}

		foreach ($secs as $k => $v)
		{
			if ($v[0] != 'h' || ! is_numeric($v[1])) continue; // skip non heading
			$current_level = $v[1];

			if ($current_level - $prev >= 2)
			{
				$str = isset($secs[$k + 1]) ? $secs[$k + 1] : $v[1];

				static::$error_ids['appropriate_heading_descending'][$k]['id'] = '<'.$v.'>'.$str;
				static::$error_ids['appropriate_heading_descending'][$k]['str'] = $str;
			}
			$prev = $current_level;
		}
		static::add_error_to_html('appropriate_heading_descending', static::$error_ids, 'ignores');
	}

	/**
	 * unclosed_elements
	 *
	 * @return Void
	 */
	public static function unclosed_elements()
	{
		$str = static::ignore_elements(static::$hl_html);

		// tags
		preg_match_all("/\<([^\>\n]+?)\</i", $str, $tags);

		if ( ! $tags[0]) return;
		foreach ($tags[0] as $k => $m)
		{
			static::$error_ids['unclosed_elements'][$k]['id'] = $m;
			static::$error_ids['unclosed_elements'][$k]['str'] = $m;
		}
		static::add_error_to_html('unclosed_elements', static::$error_ids, 'ignores');
	}

	/**
	 * suspicious_elements
	 *
	 * @return Void
	 */
	public static function suspicious_elements()
	{
		$str = static::ignore_elements(static::$hl_html);

		// tags
		preg_match_all("/\<([^\> \n]+)/i", $str, $tags);

		// elements
		$endless = array('img', 'wbr', 'br', 'hr', 'base', 'input', 'param', 'area', 'embed', 'meta', 'link', 'track', 'source', 'col', 'command', 'frame', 'keygen', 'rect', 'circle', 'line');
		$ignores = array('!doctype', 'html', '![if', '![endif]', '?xml');
		$omissionables = array('li', 'dt', 'dd', 'p', 'rt', 'rp', 'optgroup', 'option', 'tr', 'td', 'th', 'thead', 'tfoot', 'tbody', 'colgroup');
		$ignores = array_merge($ignores, $endless, $omissionables);

		// tags
		$opens = array();
		$ends = array();
		foreach ($tags[1] as $tag)
		{
			$tag = strtolower($tag);
			if (in_array($tag, $ignores)) continue; // ignore
			if (in_array(substr($tag, 1), $ignores)) continue; // ignore

			// collect tags
			if ($tag[0] =='/')
			{
				$ends[] = substr($tag, 1);
				continue;
			}
			$opens[] = $tag;
		}

		// count tags
		$opens_cnt = array_count_values($opens);
		$ends_cnt = array_count_values($ends);

		// check nums of opens
		$too_much_opens = array();
		$too_much_ends = array();
		foreach (array_keys($opens_cnt) as $tag)
		{
			if ( ! isset($ends_cnt[$tag]) || $opens_cnt[$tag] > $ends_cnt[$tag])
			{
				$too_much_opens[] = $tag;
			}
			elseif ($opens_cnt[$tag] < $ends_cnt[$tag])
			{
				$too_much_ends[] = $tag;
			}
		}

		// endless
		$suspicious_ends = array();
		foreach ($endless as $v)
		{
			if (strpos($str, '</'.$v) !== false)
			{
				$suspicious_ends[] = '/'.$v;
			}
		}

		// add errors
		foreach ($too_much_opens as $k => $v)
		{
			static::$error_ids['too_much_opens'][$k]['id'] = false;
			static::$error_ids['too_much_opens'][$k]['str'] = $v;
		}
		static::add_error_to_html('too_much_opens', static::$error_ids, 'ignores');

		foreach ($too_much_ends as $k => $v)
		{
			static::$error_ids['too_much_ends'][$k]['id'] = false;
			static::$error_ids['too_much_ends'][$k]['str'] = $v;
		}
		static::add_error_to_html('too_much_ends', static::$error_ids, 'ignores');

		foreach ($suspicious_ends as $k => $v)
		{
			static::$error_ids['suspicious_ends'][$k]['id'] = false;
			static::$error_ids['suspicious_ends'][$k]['str'] = $v;
		}
		static::add_error_to_html('suspicious_ends', static::$error_ids, 'ignores');
	}

	/**
	 * ja word breaking space
	 *
	 * @return Void
	 */
	public static function ja_word_breaking_space()
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', static::$hl_html);
		$str = static::ignore_elements(static::$hl_html);

		$search = '[^\x01-\x7E][ 　]{2,}[^\x01-\x7E]'; // MB+spaces+MB
		$search.= '|[^\x01-\x7E][ 　]+[^\x01-\x7E][ 　]'; // MB+space(s)+MB+space
		$search.= '|(?<![^\x01-\x7E])[^\x01-\x7E][ 　]+[^\x01-\x7E](?![^\x01-\x7E])'; // single MB+space(s)+single MB

		preg_match_all("/(".$search.")/iu", $str, $ms);
		foreach ($ms[1] as $k => $m)
		{
			static::$error_ids['ja_word_breaking_space'][$k]['id'] = $ms[0][$k];
			static::$error_ids['ja_word_breaking_space'][$k]['str'] = $m;
		}
		static::add_error_to_html('ja_word_breaking_space', static::$error_ids, 'ignores');
	}

	/**
	 * meanless element
	 *
	 * @return Void
	 */
	public static function meanless_element()
	{
		$str = static::ignore_elements(static::$hl_html);

		$banneds = array(
			'big',
			'tt',
			'center',
			'font',
			'blink',
			'marquee',
			'b',
			'i',
			'u',
			's',
			'strike',
			'basefont',
		);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$n = 0;
		foreach ($ms[0] as $m)
		{
			foreach ($banneds as $banned)
			{
				preg_match_all('/\<'.$banned.' [^\>]*?\>|\<'.$banned.'\>/', $m, $mms);
				if ( ! $mms[0]) continue;
				foreach ($mms[0] as $tag)
				{
					if (strpos($tag, '<blink') !== false || strpos($tag, '<marquee') !== false )
					{
						static::$error_ids['meanless_element_timing'][$n]['id'] = $tag;
						static::$error_ids['meanless_element_timing'][$n]['str'] = $tag;
					}
					else
					{
						static::$error_ids['meanless_element'][$n]['id'] = $tag;
						static::$error_ids['meanless_element'][$n]['str'] = $tag;
					}
					$n++;
				}
			}
		}
		static::add_error_to_html('meanless_element', static::$error_ids, 'ignores');
	}

	/**
	 * style for structure
	 *
	 * @return Void
	 */
	public static function style_for_structure()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! array_key_exists('style', $attrs)) continue;
			if (
				strpos($attrs['style'], 'size') !== false ||
				strpos($attrs['style'], 'color') !== false // includes background-color
			)
			{
				static::$error_ids['style_for_structure'][$k]['id'] = $ms[0][$k];
				static::$error_ids['style_for_structure'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('style_for_structure', static::$error_ids, 'ignores');
	}

	/**
	 * invalid tag
	 *
	 * @return Void
	 */
	public static function invalid_tag()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			// newline character must not exists in attr
			$attrs = static::get_attributes($m);

			foreach ($attrs as $val)
			{
				if (strpos($val, "\n") !== false)
				{
					static::$error_ids['cannot_contain_newline'][$k]['id'] = $ms[0][$k];
					static::$error_ids['cannot_contain_newline'][$k]['str'] = $m;
					break;
				}
			}

			// unbalanced_quotation
			// delete qouted quotation
			$tag = str_replace(array("\\'", '\\"'), '', $m);

			// in Englsih, single quote is frequent on grammar
			// if ((substr_count($tag, '"') + substr_count($tag, "'")) % 2 !== 0)
			if (substr_count($tag, '"') % 2 !== 0)
			{
				static::$error_ids['unbalanced_quotation'][$k]['id'] = $ms[0][$k];
				static::$error_ids['unbalanced_quotation'][$k]['str'] = $m;
			}

			if (A11YC_LANG != 'ja') continue;

			// multi-byte space
			// ignore values of attributes
			$tag = preg_replace("/(\".+?\"|'.+?')/is", '', $tag);

			if (strpos($tag, '　') !== false)
			{
				static::$error_ids['cannot_contain_multibyte_space'][$k]['id'] = $ms[0][$k];
				static::$error_ids['cannot_contain_multibyte_space'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('unbalanced_quotation', static::$error_ids, 'ignores');
		static::add_error_to_html('cannot_contain_multibyte_space', static::$error_ids, 'ignores');
		static::add_error_to_html('cannot_contain_newline', static::$error_ids, 'ignores');
	}

	/**
	 * invalid single tag close
	 *
	 * @return Void
	 */
	public static function invalid_single_tag_close()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if (preg_match("/[^ ]+\/\>/", $v))
			{
				static::$error_ids['invalid_single_tag_close'][$k]['id'] = $v;
				static::$error_ids['invalid_single_tag_close'][$k]['str'] = $ms[1][$k];
			}
		}
		static::add_error_to_html('invalid_single_tag_close', static::$error_ids, 'ignores');
	}

	/**
	 * headerless section
	 *
	 * @return Void
	 */
	public static function headerless_section()
	{
		$str = static::ignore_elements(static::$hl_html);

		preg_match_all("/\<section[^\>]*?\>(.+?)\<\/section/is", $str, $secs);

		if ( ! $secs[0]) return;

		foreach ($secs[0] as $k => $v)
		{
			if ( ! preg_match("/\<h\d/", $v))
			{
				static::$error_ids['headerless_section'][$k]['id'] = $v;
				static::$error_ids['headerless_section'][$k]['str'] = $ms[1][$k];
			}
		}
		static::add_error_to_html('headerless_section', static::$error_ids, 'ignores');
	}
}
