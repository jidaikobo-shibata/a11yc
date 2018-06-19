<?php
/**
 * \JwpA11y\Pages
 *
 * @package    WordPress
 * @version    1.0
 * @author     Jidaikobo Inc.
 * @license    GPL
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace JwpA11y;

class Pages extends \A11yc\Controller\Pages
{
	/**
	 * Manage Target Pages.
	 *
	 * @return  void
	 */
	public static function index()
	{
		// nonce check
		if ($_POST)
		{
			if (
				! isset($_POST['jwp_a11y_nonce']) ||
				(
					! wp_verify_nonce($_POST['jwp_a11y_nonce'], 'jwp_a11y_pages_add') &&
					! wp_verify_nonce($_POST['jwp_a11y_nonce'], 'jwp_a11y_pages_get')
				)
			)
			{
				print 'nonce check failed.';
				exit;
			}
		}

		// nonce
		\A11yc\View::assign(
			'add_nonce',
			wp_nonce_field('jwp_a11y_pages_add', 'jwp_a11y_nonce', true, false),
			false
		);

		\A11yc\View::assign(
			'get_nonce',
			wp_nonce_field('jwp_a11y_pages_get', 'jwp_a11y_nonce', true, false),
			false
		);

		$ramdom_num = \A11yc\Input::post('jwp_a11y_pages_ramdom_add_num', 0);
		if ($ramdom_num)
		{
			$args = array(
				'posts_per_page' => $ramdom_num,
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'orderby'        => 'rand'
			);
			$pages = array();
			foreach (get_posts($args) as $v)
			{
				$pages[] = get_permalink($v->ID);
			}
			parent::addPages($is_force = false, $pages);
		}
		// parent edit
		elseif (\A11yc\Input::get('a') == 'edit')
		{
			parent::edit();
		}
		// parent add
		elseif (\A11yc\Input::get('a') == 'add')
		{
			parent::add();
		}
		else
		{
			parent::index();
		}

		// html
		$html = '';
		$html.= '<div class="wrap">';
		$html.= '<div id="icon-themes" class="icon32"><br /></div>';
		$html.= '<h1>'.__("Pages", "jwp_a11y").'</h1>';
		$html.= '<div class="postbox" style="margin-top: 15px;">';
		$html.= '<div class="inside">';
		$html.= \A11yc\View::fetchTpl('messages.php');
		$html.= \A11yc\View::fetch('body');

		if (\A11yc\Input::get('a') == 'add')
		{
			$html.= '<form action="'.A11YC_PAGES_ADD_URL.'" method="POST">';
			$html.= '<h2>'.__('Get Urls Ramdom (WordPress Pages Only)', 'jwp_a11y').'</h2>';
			$html.= '<label for="jwp_a11y_pages_ramdom_add_num">'.__('Num', 'jwp_a11y').'</label> ';
			$html.= '<select id="jwp_a11y_pages_ramdom_add_num" name="jwp_a11y_pages_ramdom_add_num">';
			for ($n = 1; $n <= 30; $n++)
			{
				$html.= '<option value="'.$n.'" />'.$n.'</option>';
			}
			$html.= '</select>';
			$html.= \A11yc\View::fetch('add_nonce');
			$html.= '<input type="submit" value="'.A11YC_LANG_PAGES_URLS_ADD.'" />';
			$html.= '</form>';
		}

		$html.= '</div><!--/.inside-->';
		$html.= '</div><!--/.postbox-->';
		$html.= '</div><!--/.wrap-->';
		echo $html;
	}
}
