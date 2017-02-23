<?php
/**
 * A11yc\Post
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com

ussage:
put this code to front controller.

require (__DIR__.'/libs/a11yc/classes/controller/post.php');
\A11yc\Controller_Post::online_validation();

 */
namespace A11yc;
class Controller_Post
{
	private static $url;
	private static $is_logged_in = false;

	/**
	 * forge
	 *
	 * @return void
	 */
	public static function forge()
	{
		require (dirname(dirname(__DIR__)).'/main.php');

		// set application url
		static::$url = Util::remove_query_strings(Util::uri());

		// load language
		static::load_language();

		// auth users
		Users::forge(unserialize(A11YC_GUEST_USERS));

		// auth
		Auth::forge('A11YCONLINEVALIDATE');
		if (Auth::auth())
		{
			// login user
			$login_user = Users::fetch_current_user();
			View::assign('login_user', $login_user);
		}

		// routing
		static::routing();
		$action = Route::get_action();
		static::$action();

		// render
		View::assign('base_url', static::$url);
		View::assign('mode', 'post');
		View::display(array(
				'post/header.php',
				'messages.php',
				'body.php',
				'post/footer.php',
			));
	}

	/**
	 * Action_Login
	 *
	 * @return void
	 */
	private static function Action_Login()
	{
		\A11yc\Controller_Auth::Action_Login();
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_AUTH_TITLE);
	}

	/**
	 * Action_Logout
	 *
	 * @return void
	 */
	private static function Action_Logout()
	{
		\A11yc\Controller_Auth::Action_Logout(static::$url);
	}

	/**
	 * Action_Docs
	 *
	 * @return void
	 */
	private static function Action_Docs()
	{
		Controller_Docs::index(); // $body set
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_DOCS_TITLE);
	}

	/**
	 * Action_Doc
	 *
	 * @return void
	 */
	private static function Action_Doc()
	{
		$code = Input::get('code');
		$criterion = Input::get('criterion');
		if ($code && $criterion)
		{
			Controller_Docs::each($criterion, $code); // $body set
			$doc = View::fetch('doc');
			define('A11YC_LANG_POST_TITLE', $doc['name']);
		}
		else
		{
			Util::error('service not available.');
		}
	}

	/**
	 * Action_Validation
	 *
	 * @return void
	 */
	private static function Action_Validation()
	{
		/*
			banのルールを作る
			ipを保存するDBを作る？
			ガベコレも
		*/

		// validation
		$raw = '';
		$all_errs = array();
		$errs_cnts = array();
		$target_html = Input::post('source', '');
		if ($target_html)
		{
			$all_errs = array();
			Validate::set_html($target_html);
			// Crawl::set_target_path($url); // for same_urls_should_have_same_text

			$codes = Validate::$codes;
			unset($codes['link_check']);
			unset($codes['same_urls_should_have_same_text']);
			unset($codes['same_page_title_in_same_site']);

			// validate
			foreach ($codes as $method => $class)
			{
				$class::$method();
			}

			if (Validate::get_error_ids())
			{
				$err_link = static::$url.'?a=doc&code=';
				foreach (Validate::get_error_ids() as $err_code => $errs)
				{
					foreach ($errs as $key => $err)
					{
						$all_errs[]=Controller_Checklist::message($err_code, $err, $key, $err_link);
					}
				}
			}

			// results
			$errs_cnts = array_merge(
				array('total' => count($all_errs)),
				Controller_Checklist::$err_cnts
			);
			$raw = nl2br(Validate::revert_html(Util::s(Validate::get_hl_html())));

			View::assign('errs', $all_errs, false);
			View::assign('errs_cnts', $errs_cnts);
			View::assign('raw', $raw, false);
			View::assign('result', View::fetch_tpl('checklist/validate.php'), false);
		}

		// title
		define('A11YC_LANG_POST_TITLE', 'Online Validation');

		// assign
		View::assign('is_call_from_post', true);
		View::assign('title', A11YC_LANG_POST_TITLE);
		View::assign('target_html', $target_html);
		View::assign('body', View::fetch_tpl('post/index.php'), false);
	}

	/**
	 * load language
	 *
	 * @return  string
	 */
	private static function load_language()
	{
		// load language
		$lang = Lang::get_lang();
		if (
			empty($lang) ||
			substr(static::$url, 0 - strlen('/post.php')) != '/post.php'
		)
		{
			Util::error('Not found.');
		}
		include A11YC_PATH.'/languages/'.$lang.'/post.php';
	}

	/**
	 * routing
	 *
	 * @return  string
	 */
	private static function routing()
	{
		// vals
		$a = Input::get('a', '');
		$controller = '\A11yc\Controller_Post';
		$is_index = empty(join($_GET));

		// top page
		if ($is_index)
		{
			$action = 'Action_Validation';
		}

		// safe access?
		if ( ! $is_index && ctype_alnum($a))
		{
			$action = 'Action_'.ucfirst($a);
		}

		// auth
		if (\Kontiki\Auth::auth() && $a == 'login')
		{
			header('location:'.static::$url);
		}

		// performed IPs
		if (defined('A11YC_APPROVED_IPS'))
		{
			if ( ! in_array(Arr::get($_SERVER, 'REMOTE_ADDR'), unserialize(A11YC_APPROVED_IPS)))
			{
				$action = '';
			}
		}

		// class and methods exists
		if (
			method_exists($controller, $action) &&
			is_callable($controller.'::'.$action)
		)
		{
			Route::set_controller($controller);
			Route::set_action($action);
			return;
		}

		// error
		Util::error('service not available.');
	}
}