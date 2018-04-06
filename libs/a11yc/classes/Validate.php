<?php
/**
 * \JwpA11y\Validate
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Validate extends \A11yc\Validate
{
	static $errors = array();

	/**
	 * Check accessibility $post->content.
	 *
	 * @param   int     $post_id
	 * @return  void
	 */
	public static function non_post_validate()
	{
		if (substr(\A11yc\Input::server('SCRIPT_NAME'), -18) != '/wp-admin/post.php') return;
		if ( ! \A11yc\Input::get('jwp-a11y_check_here')) return;
		global $post;
		static::set_errors($post);
	}

	/**
	 * Check accessibility $post->content.
	 *
	 * @param   int     $post_id
	 * @return  void
	 */
	public static function validate($post_id)
	{
		if (substr(\A11yc\Input::server('SCRIPT_NAME'), -18) != '/wp-admin/post.php') return;
		global $wpdb;

		$do_nothing = false;

		// do nothing with menus
		if (\A11yc\Input::post('menu-item')) $do_nothing = true;
		if (\A11yc\Input::post('save_menu')) $do_nothing = true;

		// autosave, acl
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) $do_nothing = true;
		if ( ! current_user_can('edit_post', $post_id)) $do_nothing = true;

		// check status
		$obj = get_post($post_id);

		if ( ! in_array($obj->post_status, array('publish', 'private', 'draft', 'future', 'pending')))
		{
			$do_nothing = true;
		}

		// do nothing
		if ($do_nothing)
		{
			remove_action('save_post', array('\JwpA11y\Validate', 'check'));
			return $post_id;
		}

		static::set_errors($obj);
	}

	/**
	 * set_errors
	 *
	 * @return  void
	 */
	public static function set_errors($obj)
	{
		// accessibility check
		$e = new \WP_Error();
		$yml = \A11yc\Yaml::fetch();

		// code and function
		$codes = \A11yc\Validate::$codes;
		\A11yc\Validate::$is_partial    = true;
		\A11yc\Validate::$do_link_check = \A11yc\Input::post('jwp_a11y_link_check', false);
		\A11yc\Validate::$do_css_check  = \A11yc\Input::post('jwp_a11y_css_check', false);

		// get all custom_fields
		$meta_values = '';
		foreach (get_post_meta($obj->ID) as $meta_key => $meta_value)
		{
			if ($meta_key[0] == '_') continue;
			if ($meta_key == 'dashi_search') continue; // secret hard coding. but low influential...
			$meta_values.= wp_specialchars_decode($meta_value[0]);
		}
		$url = get_permalink($obj->ID);
		static::html($url, apply_filters('the_content', $obj->post_content).$meta_values);

		// check same title
		self::same_page_title_in_same_site($url);

		// add errors
		if (static::getErrorIds($url))
		{
			foreach (static::getErrorIds($url) as $code => $errs)
			{
				foreach ($errs as $key => $err)
				{
					$err_type = isset($yml['errors'][$code]['notice']) ? 'notices' : 'errors';
					$e->add(
						$err_type,
						array($code => \A11yc\Message::getText($url, $code, $err, $key))
					);
				}
			}
		}

		// set transient
		set_transient('jwp_a11y_notices', $e->get_error_messages('notices'), 10);
		if ($e->get_error_messages('errors'))
		{
			set_transient('jwp_a11y_errors', $e->get_error_messages('errors'), 10);
		}
		else
		{
			$messages = array('no_errors' => true);
			if (\A11yc\Input::post('jwp_a11y_link_check'))
			{
				$messages['no_dead_link'] = true;
			}
			set_transient('jwp_a11y_no_errors', $messages, 10);
		}
	}

	/**
	 * same page title in same site
	 *
	 * @param  String $url
	 * @return Void
	 */
	private static function same_page_title_in_same_site($url)
	{
		global $wpdb;
		$title = \A11yc\Input::post('post_title');
		if ( ! $title) return;

		$sql = $wpdb->prepare('SELECT ID FROM '.$wpdb->posts.' WHERE `post_title` = %s and `post_status` = "publish"', \A11yc\Input::post('post_title'));
		$results = $wpdb->get_results($sql);

		if (count($results) >= 2)
		{
			$strs = array();
			foreach ($results as $v)
			{
				$strs[] = $v->ID;
			}

			static::$error_ids[$url]['same_page_title_in_same_site'][0]['id'] = \A11yc\Util::s($title);
			static::$error_ids[$url]['same_page_title_in_same_site'][0]['str'] = 'IDs: '.join(', ', $strs);
		}
		static::addErrorToHtml($url, 'same_page_title_in_same_site', static::$error_ids[$url]);
	}

	/**
	 * Show message to editor.
	 *
	 * @return  void
	 */
	public static function show_messages()
	{
		$html = '';
		if ($messages = get_transient('jwp_a11y_errors'))
		{
			$html.= '<div class="notice error is-dismissible" id="jwp_a11y_error"><section>';
			$html.= '<a href="#end_line_of_a11y_checklist" class="screen-reader-shortcut">'.__("Skip accessibility checklist messages.", "jwp_a11y").'</a>';
			$html.= '<h1>'.__("Accessibility Checklist", "jwp_a11y").'</h1>';
			$html.= '<p>'.__("This checklist is according to WCAG2.0 (Web Content Accessibility Guidelines).", "jwp_a11y").'</p>';

			// count errors
			$yml = \A11yc\Yaml::fetch();
			$errs_cnts = array('a' => 0, 'aa' => 0, 'aaa' => 0);
			foreach ($messages as $message)
			{
				$message_keys = array_keys($message);
				$code = reset($message_keys);
				$lv = strtolower($yml['criterions'][$yml['errors'][$code]['criterions'][0]]['level']['name']);
				$errs_cnts[$lv]++;
			}

			$errs_cnts = array_merge(array('total' => count($messages)), $errs_cnts);
			foreach ($errs_cnts as $lv => $errs_cnt)
			{
				$html.= '<span class="a11yc_errs_lv">'.strtoupper($lv).'</span> <span class="a11yc_errs_cnt">'.intval($errs_cnt).'</span> ';
			}

			$html.= '<dl id="a11yc_validation_errors" class="a11yc_hide_if_fixedheader">';
			$html = self::remove_view_src($html, $messages);
			$html.= '</dl>';
			$html.= '</section><a id="end_line_of_a11y_checklist" class="screen-reader-text" tabindex="-1">'.__("End line of accessibility checklist.", "jwp_a11y").'</a></div>';
		}

		// no error
		elseif($messages = get_transient('jwp_a11y_no_errors'))
		{
			$html.= '<div class="notice notice-success is-dismissible" id="jwp_a11y_no_error">';
			$html.= '<p>'.__("In the automatic check, accessibility problems were not found.", "jwp_a11y").'</p>';
			if (isset($messages['no_dead_link']))
			{
				$html.= '<p>'.__("No dead links were found.", "jwp_a11y").'</p>';
			}
			$html.= '</div>';
		}

		// notice
		if ($messages = get_transient('jwp_a11y_notices'))
		{
			$html.= '<div class="notice notice-warning is-dismissible" id="jwp_a11y_notice">';
			$html.= '<h2>'.__("There may be no accessibility problems, but just in case, please check.", "jwp_a11y").'</h2>';
			$html.= '<dl id="a11yc_validation_notices" class="a11yc_hide_if_fixedheader">';
			$html = self::remove_view_src($html, $messages);
			$html.= '</dl>';
			$html.= '</div>';
		}

		echo $html;
		delete_transient('jwp_a11y_errors');
	}

	/**
	 * Remove "view source"
	 *
	 * @param string $html
	 * @param array $messages
	 * @return  string
	 */
	private static function remove_view_src($html, $messages)
	{
		foreach($messages as $message)
		{
			$message_keys = array_keys($message);
			$code = reset($message_keys);
			$html.= preg_replace(
				'/\<a href="#.+?\<\/a\>/i',
				'',
				$message[$code]);
		}
		return $html;
	}
}
