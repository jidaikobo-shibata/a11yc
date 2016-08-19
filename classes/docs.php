<?php
/**
 * A11yc\Docs
 *
 * @package    part of A11yc
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    WTFPL2.0
 * @copyright  Jidaikobo Inc.
 * @link       http:/www.jidaikobo.com
 */
namespace A11yc;
class Docs
{
	/**
	 * Show Techs Index
	 *
	 * @return  string
	 */
	public static function index()
	{
		$yml = Yaml::fetch();
		$test = Yaml::each('test');
		$html = '';

		// show testing index
		$html.= '<h2>'.A11YC_LANG_DOCS_TEST.'</h2>';
		$html.= '<ul>';
		foreach ($test['tests'] as $code => $v)
		{
			$html.= '<li><a'.A11YC_TARGET.' href="'.A11YC_DOC_URL.$code.'">'.$v['name'].'</a></li>';
		}
		$html.= '</ul>';

		// show technique index
		foreach ($yml['principles'] as $k => $v)
		{
			// principles
			$html.= '<div id="section_p_'.$v['code'].'" class="section_guidelines"><h2 id="p_'.$v['code'].'" tabindex="-1">'.$v['code'].' '.$v['name'].'</h2>';

			// guidelines
			foreach ($yml['guidelines'] as $kk => $vv)
			{
				if ($kk{0} != $k) continue;
				$html.= '<div id="g_'.$vv['code'].'" class="section_guideline"><h3>'.Util::key2code($vv['code']).' '.$vv['name'].'</h3>';

				// criterions
				$html.='<div class="section_criterions">';
				foreach ($yml['criterions'] as $kkk => $vvv)
				{
					if (substr($kkk, 0, 3) != $kk) continue;
					$html.= '<div id="c_'.$kkk.'" class="section_criterion l_'.strtolower($vvv['level']['name']).'">';
					$html.= '<div class="a11yc_criterion">';
					$html.= '<h4>'.Util::key2code($vvv['code']).' '.$vvv['name'].' ('.$vvv['level']['name'].')';
					if (isset($vvv['url_as']))
					{
						$html.= '<a'.A11YC_TARGET.' href="'.$vvv['url_as'].'" class="link_as">Accessibility Supported</a>';
					}
					$html.= '<a'.A11YC_TARGET.' href="'.$vvv['url'].'" class="link_understanding">Understanding</a></h4>';
					$html.= '<p>'.$vvv['summary'].'</p></div><!-- /.a11yc_criterion -->';

					// checks
					$html.= '<ul>';
					foreach ($yml['checks'][$kkk] as $code => $val)
					{
						$non_interference = isset($vvvv['non-interference']) ? ' class="non_interference" title="non interference"' : '';
						$html.= '<li'.$non_interference.'>';
						$html.= '<a'.A11YC_TARGET.' href="'.A11YC_DOC_URL.$code.'&amp;criterion='.$kkk.'">';
						$html.= $val['name'];
						$html.= '</a></li>';
					}
					$html.= '</ul>';
					$html.= '</div><!--/#c_'.$kkk.'.l_'.strtolower($vvv['level']['name']).'-->';
				}
				$html.='</div><!--/.section_criterions-->';
				$html.='</div><!--/#g_'.$vv['code'].'-->';
			}
			$html.= '</div><!--/#section_p_'.$v['code'].'.section_guidelines-->';
		}

		return array('', $html);
	}

	/**
	 * Show each
	 *
	 * @return  string
	 */
	public static function each($criterion, $code)
	{
		// fetch content
		$yml = Yaml::fetch();
		$test = Yaml::each('test');
		$doc = '';
		$html = '';

		if (isset($yml['checks'][$criterion][$code]))
		{
			$doc = $yml['checks'][$criterion][$code];
		}
		elseif(isset($test['tests'][$code]))
		{
			$doc = $test['tests'][$code];
		}

		// show content
		if ($doc)
		{
			$lines = isset($doc['tech']) ? explode("\n", stripslashes($doc['tech'])) : false;
			if ($lines)
			{
				$html.= '<ul>';
				foreach ($lines as $line)
				{
					$html.= '<li>'.$line.'</li>';
				}
				$html.= '</ul>';
			}
			else
			{
				$html.= '<p>'.A11YC_LANG_NO_DOC.'</p>';
			}

			// relation
			if (isset($doc['relations']))
			{
				$rels = Util::s($doc['relations']);
				$html.= '<h2>'.A11YC_LANG_RELATED.'</h2>';
				$html.= '<ul>';
				foreach ($rels as $rel_criterion => $rel_codes)
				{
					foreach ($rel_codes as $rel_code)
					{
						$html.= '<li><a'.A11YC_TARGET.' href="'.A11YC_DOC_URL.$rel_code.'&amp;criterion='.Util::s($rel_criterion).'">'.$yml['checks'][$rel_criterion][$rel_code]['name'].'</a></li>';
					}
				}
				$html.= '</ul>';
			}

			// understanding
			if ($criterion)
			{
				$html.= '<h2>'.A11YC_LANG_UNDERSTANDING.'</h2>';
				$html.= '<p><a'.A11YC_TARGET_OUT.' href="'.$yml['criterions'][$criterion]['url'].'">'.$yml['criterions'][$criterion]['name'].'</a></p>';
			}

			// Accessibility Supported
			if (isset($doc['url_as']))
			{
				$html.= '<h2>'.A11YC_LANG_AS.'</h2>';
				$html.= '<ul>';
				foreach ($doc['url_as'] as $v)
				{
					$v = Util::s($v);
					$html.= '<li><a'.A11YC_TARGET_OUT.' href="'.$v['url'].'">'.$v['name'].'</a></li>';
				}
				$html.= '</ul>';
			}
		}
		else
		{
			die('invalid access.');
		}
		return array('', $html);
	}
}
