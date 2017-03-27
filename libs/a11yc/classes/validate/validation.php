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
	 * @return  bool
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
			if (is_numeric($sec[1]))
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
	 * @return  bool
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
	 * @return  bool
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
			}
			else
			{
				$opens[] = $tag;
			}
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
	 * @return  void
	 */
	public static function ja_word_breaking_space()
	{
		if (A11YC_LANG != 'ja') return false;
		$str = str_replace(array("\n", "\r"), '', static::$hl_html);
		$str = static::ignore_elements(static::$hl_html);

		preg_match_all("/([^\x01-\x7E][ 　]{2,}[^\x01-\x7E])/iu", $str, $ms);
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
	 * @return  void
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
	 * @return  void
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
	 * suspicious attributes
	 *
	 * @return  void
	 */
	public static function suspicious_attributes()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);

			// suspicious attributes
			if (isset($attrs['suspicious']))
			{
				static::$error_ids['suspicious_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['suspicious_attributes'][$k]['str'] = join(', ', $attrs['suspicious']);
			}

			// duplicated_attributes
			if (isset($attrs['plural']))
			{
				static::$error_ids['duplicated_attributes'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_attributes'][$k]['str'] = $m;
			}
		}
		static::add_error_to_html('suspicious_attributes', static::$error_ids, 'ignores');
		static::add_error_to_html('duplicated_attributes', static::$error_ids, 'ignores');
	}

	/**
	 * duplicated ids and accesskey
	 *
	 * @return  void
	 */
	public static function duplicated_ids_and_accesskey()
	{
		$str = static::ignore_elements(static::$hl_html);

		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		// duplicated_ids
		$ids = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['id'])) continue;

			if (in_array($attrs['id'], $ids))
			{
				static::$error_ids['duplicated_ids'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_ids'][$k]['str'] = $attrs['id'];
			}
			$ids[] = $attrs['id'];
		}
		static::add_error_to_html('duplicated_ids', static::$error_ids, 'ignores');

		// duplicated_accesskeys
		$accesskeys = array();
		foreach ($ms[0] as $k => $m)
		{
			$attrs = static::get_attributes($m);
			if ( ! isset($attrs['accesskey'])) continue;

			if (in_array($attrs['accesskey'], $accesskeys))
			{
				static::$error_ids['duplicated_accesskeys'][$k]['id'] = $ms[0][$k];
				static::$error_ids['duplicated_accesskeys'][$k]['str'] = $attrs['accesskey'];
			}
			$accesskeys[] = $attrs['accesskey'];
		}
		static::add_error_to_html('duplicated_accesskeys', static::$error_ids, 'ignores');
	}

	/**
	 * invalid tag
	 *
	 * @return  void
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

			// TODO: in Englsih, single quote is frequent on grammar
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
	 * titleless_frame
	 *
	 * @return  void
	 */
	public static function titleless_frame()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		foreach ($ms[0] as $k => $v)
		{
			if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') continue;
			$attrs = static::get_attributes($v);

			if ( ! trim(Arr::get($attrs, 'title')))
			{
				static::$error_ids['titleless_frame'][$k]['id'] = $ms[0][$k];
				static::$error_ids['titleless_frame'][$k]['str'] = $ms[0][$k];
			}
		}
		static::add_error_to_html('titleless_frame', static::$error_ids, 'ignores');
	}

	/**
	 * numeric attr
	 *
	 * @return  bool
	 */
	public static function must_be_numeric_attr()
	{
		$str = static::ignore_elements(static::$hl_html);
		$ms = static::get_elements_by_re($str, 'ignores', 'tags');
		if ( ! $ms[0]) return;

		$targets = array(
			'width',
			'height',
			'border',
		);

		foreach ($ms[0] as $k => $v)
		{
			$attrs = static::get_attributes($v);

			foreach ($attrs as $attr => $val)
			{
				if ( ! in_array($attr, $targets)) continue;
				if ( ! is_numeric($val))
				{
					static::$error_ids['must_be_numeric_attr'][$k]['id'] = $v;
					static::$error_ids['must_be_numeric_attr'][$k]['str'] = $attr;
				}
			}
		}
		static::add_error_to_html('must_be_numeric_attr', static::$error_ids, 'ignores');
	}

	/**
	 * invalid single tag close
	 *
	 * @return  bool
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
}
