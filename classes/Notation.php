<?php
/**
 * \JwpA11y\Notation
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Notation
{
	/**
	 * dashboard
	 *
	 * @return  void
	 */
	public static function dashboard()
	{
		if ( ! is_admin()) return;
		if ( ! \A11yc\Guzzle::envCheck()) return;

		$settings = \A11yc\Model\Settings::fetchAll();
		if ( ! isset($settings['target_level'])) return;

		if ($settings['target_level'])
		{
			// ダッシュボードにランダムチェックを表示する
			add_action('wp_dashboard_setup', function ()
			{
				wp_add_dashboard_widget (
					'jwp_a11y_ramdom_check',
					__('Ramdom Accessibility Check', 'jwp_a11y'),
					array('\JwpA11y\Notation', 'ramdom_check')
				);
			});
		}
		else
		{
			global $pagenow;
			if ($pagenow == 'index.php')
			{
				add_action('admin_notices', function ()
				{
					echo '<div id="message" class="error dashi_error"><p><strong><a href="'.admin_url().'admin.php?page=jwp-a11y%2Fjwp_a11y_settings">jwp-a11y '.A11YC_LANG_ERROR_NON_TARGET_LEVEL.'</a></strong></p></div>';
				});
			}
		}
	}

	/**
	 * ramdom_check
	 *
	 * @return  void
	 */
	public static function ramdom_check()
	{
		$urls = array();

		$post_types = array();

		// post type archive (home_url() included)
		foreach (get_post_types() as $post_type)
		{
			$archive = get_post_type_archive_link($post_type);
			if ( ! $archive) continue;
			$urls[] = $archive;
			$post_types[] = $post_type;
		}

		// Ramdom pick up
		$get_ramdom = function ()
		{
			$args = array(
				'post_type' => 'any',
				'post_status' => 'publish',
				'orderby' => 'rand',
				'numberposts' => 1,
			);
			$post = get_posts($args);
			return $post;
		};

		// search until nice item found
		$not_found = true;
		while($not_found)
		{
			$post = $get_ramdom();
			if ( ! isset($post[0])) $not_found = false; // there is no content
			if ( ! in_array($post[0]->post_type, $post_types)) continue;
			$not_found = false;
		}

		if (isset($post[0]))
		{
			$urls[] = get_permalink($post[0]->ID);
		}

		// home_url() must exist
		if ( ! in_array(home_url(), $urls))
		{
			$urls[] = home_url();
		}

		// Ramdomize
		shuffle($urls);
		$url = $urls[0];

		// url exist?
		if (\A11yc\Crawl::isPageExist($url))
		{
			$settings = \A11yc\Model\Settings::fetchAll();
			$target_level = str_repeat('a', $settings['target_level']);
			$additional_criterions = join('","', \A11yc\Values::additionalCriterions());
			$user = wp_get_current_user();

			$errors = \A11yc\Validate::url($url);
			echo '<p>'.A11YC_LANG_CHECKLIST_TARGETPAGE.': <a href="'.esc_html($url).'">'.\A11yc\Model\Html::fetchPageTitle($url).'</a>';
			echo ' [<a href="'.A11YC_CHECKLIST_URL.\A11yc\Util::urlenc($url).'">'.A11YC_LANG_PAGES_CHECK.'</a>]';

			\A11yc\View::assign('link_check', false);
			\A11yc\View::assign('url', $url);
			echo '<div id="a11yc_checks" data-a11yc-target_level="'.$target_level.'" data-a11yc-additional_criterions=\'["'.$additional_criterions.'"]\' data-a11yc-current-user="'.$user->ID.'" data-a11yc-lang="{\'expand\':'.A11YC_LANG_CTRL_EXPAND.'\', \'compress\': \''.A11YC_LANG_CTRL_COMPRESS.'\', \'conformance\': '.A11YC_LANG_CHECKLIST_CONFORMANCE.','.A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL.'\'}">';
			echo '<div id="a11yc_validator_results" class="a11yc">'.\A11yc\View::fetchTpl('checklist/ajax.php').'</div></div>';
		}
		else
		{
			echo 'failed.';
		}
	}
}
