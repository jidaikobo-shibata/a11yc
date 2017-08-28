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
define('A11YC_POST_IP_MAX_A_DAY', 150);
define('A11YC_POST_COOKIE_A_10MIN', 10); // min.
define('A11YC_POST_SCRIPT_NAME', '/index.php');
\A11yc\Controller_Post::forge();

at .htaccess
php_value post_max_size "2M"

 */
namespace A11yc;

class Controller_Post
{
	private static $url;

	/**
	 * forge
	 *
	 * @return Void
	 */
	public static function forge()
	{
		// max post a day by ip
		defined('A11YC_POST_IP_MAX_A_DAY') or define('A11YC_POST_IP_MAX_A_DAY', 150);
		defined('A11YC_POST_COOKIE_A_10MIN') or define('A11YC_POST_COOKIE_A_10MIN', 10);

		// script name
		defined('A11YC_POST_SCRIPT_NAME') or define('A11YC_POST_SCRIPT_NAME', '/post.php');

		// Google Analytics
		defined('A11YC_POST_GOOGLE_ANALYTICS_CODE') or define('A11YC_POST_GOOGLE_ANALYTICS_CODE', '');

		// a11yc
		require (dirname(dirname(__DIR__)).'/main.php');

		// ua
		require (A11YC_PATH.'/resources/ua.php');

		// set application url
		static::$url = Util::remove_query_strings(Util::uri());
		static::$url.= strpos(static::$url, A11YC_POST_SCRIPT_NAME) === false ? A11YC_POST_SCRIPT_NAME : '';

		// load language
		static::load_language();

		// session
 	Session::forge('A11YCONLINEVALIDATE');

		// auth users
		if (defined('A11YC_GUEST_USERS'))
		{
			Users::forge(unserialize(A11YC_GUEST_USERS));
		}
		else
		{
			Users::forge(array());
		}

		// auth
		if (Auth::auth())
		{
			// login user
			$login_user = Users::fetch_current_user();
			View::assign('login_user', $login_user);
		}

		// view
		View::add_tpl_path(A11YC_PATH.'/views/post');

		// set base url before call controllers
		View::assign('base_url', static::$url);

		// routing
		static::routing();
		$action = Route::get_action();
		static::$action();

		// render
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
	 * @return Void
	 */
	public static function Action_Login()
	{
		\A11yc\Controller_Auth::Action_Login();
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_AUTH_TITLE);
	}

	/**
	 * Action_Logout
	 *
	 * @return Void
	 */
	public static function Action_Logout()
	{
		\A11yc\Controller_Auth::Action_Logout(static::$url);
	}

	/**
	 * Action_Docs
	 *
	 * @return Void
	 */
	public static function Action_Docs()
	{
		Controller_Docs::index(); // $body set
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_DOCS_TITLE);
	}

	/**
	 * Action_Doc
	 *
	 * @return Void
	 */
	public static function Action_Doc()
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
	 * Action_Readme
	 *
	 * @return Void
	 */
	public static function Action_Readme()
	{
		View::assign('body', View::fetch_tpl('post/readme.php'), false);
		View::assign('title', A11YC_LANG_POST_README);
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_POST_README);
	}

	/**
	 * ip check for guest users
	 *
	 * @param  String $ip
	 * @return Void
	 */
	private static function ip_check_for_guest_users($ip)
	{
		// database
		define('A11YC_POST_DB', 'post_log');
		define('A11YC_POST_DATA_FILE', '/'.A11YC_POST_DB.'.sqlite');
		Db::forge(
			A11YC_POST_DB,
			array(
				'dbtype' => 'sqlite',
				'path' => A11YC_DATA_PATH.A11YC_POST_DATA_FILE,
			));
		static::init_table();

		// ip check
		$past_24h = time() - 86400;
		$sql = 'SELECT COUNT(`ip`) as cnt FROM ip WHERE `ip` = ? AND `datetime` > ?;;';
		$ip_count = Db::fetch($sql, array($ip, $past_24h), A11YC_POST_DB);

		// cookie check
		$cookie_count = Session::show('a11yc_post', 'count') ?: array();
		$cookie_count = array_filter($cookie_count, function ($v){return ($v > time() - 600);});

		// ban
		if (
			$ip_count['cnt'] >= A11YC_POST_IP_MAX_A_DAY ||
			count($cookie_count) >= A11YC_POST_COOKIE_A_10MIN
		)
		{
			Util::error('too much access.');
		}
	}

	/**
	 * Action_Validation
	 *
	 * @return Void
	 */
	public static function Action_Validation()
	{
		// vals
		$ip         = Input::server('REMOTE_ADDR', '');
		$url        = Input::post('url', '', FILTER_VALIDATE_URL);
		$user_agent = Input::post('user_agent', '');
		$default_ua = Util::s(Input::user_agent());
		$page_title = '';
		$real_url   = '';

		// performed IPs
		$is_in_white_list = false;
		if (defined('A11YC_APPROVED_GUEST_IPS'))
		{
			$is_in_white_list = in_array($ip, unserialize(A11YC_APPROVED_GUEST_IPS));
		}

		// ip check for guest users
		if ( ! Auth::auth() && ! $is_in_white_list)
		{
			static::ip_check_for_guest_users($ip);
		}

		// validation
		$raw = '';
		$all_errs = array();
		$errs_cnts = array();
		$target_html = '';

		if (Input::post('source'))
		{
			$target_html = Input::post('source');
		}
		elseif ($url)
		{
			Guzzle::forge($url);

			// User Agent
			switch ($user_agent)
			{
				case 'iphone':
					$ua = A11YC_UA_IPHONE;
					break;
				case 'android':
					$ua = A11YC_UA_ANDROID;
					break;
				case 'ipad':
					$ua = A11YC_UA_IPAD;
					break;
				case 'tablet':
					$ua = A11YC_UA_ANDROID_TABLET;
					break;
				case 'featurephone':
					$ua = A11YC_UA_FEATUREPHONE;
					break;
				default:
					$ua = $default_ua;
					break;
			}
			$default_ua = $ua;
			Guzzle::instance($url)->set_config('User-Agent', $ua);

			// html
			$target_html = Guzzle::instance($url)->is_html ? Guzzle::instance($url)->body : false;

			// real url
			$real_url = $target_html ? Guzzle::instance($url)->real_url : '';

			// basic auth failed
			if (Guzzle::instance($url)->status_code == 401)
			{
				$target_html = '';
				Session::add('messages', 'errors', A11YC_LANG_POST_BASIC_AUTH_EXP);
			}

			// connection problems
			if (Guzzle::instance($url)->errors)
			{
				Session::add('messages', 'errors', A11YC_LANG_ERROR_COULD_NOT_ESTABLISH_CONNECTION);
			}
		}

		// Do Validate
		if ($target_html)
		{
			$all_errs = array();
			Validate::set_html($target_html);
			$codes = Validate::$codes;

			// for same_urls_should_have_same_text
			$do_validate = true;
			if ($url)
			{
				Crawl::set_target_path($url);
				if (Input::post('behaviour') == 'images')
				{
					$do_validate = false;
					View::assign('images', Validate_Alt::get_images());
					Session::add('messages', 'messages', A11YC_LANG_POST_DONE_IMAGE_LIST);
				}
			}
			else
			{
				// just for "same_urls_should_have_same_text".
				Crawl::set_target_path('http://example.com');
				// unset($codes['same_urls_should_have_same_text']);
				// Session::add('messages', 'errors', A11YC_LANG_ERROR_NO_URL_NO_CHECK_SAME);
			}

			// do validate not image list
			if ($do_validate)
			{
				// unset uncheck errors
				unset($codes['link_check']);
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
							$all_errs[] = Controller_Checklist::message($err_code, $err, $key, $err_link);
						}
					}
				}

				// message
				Session::add('messages', 'messages', A11YC_LANG_POST_DONE);
				if (count($all_errs) == 0)
				{
					Session::add('messages', 'messages',
						A11YC_LANG_CHECKLIST_NOT_FOUND_ERR);
				}
				else
				{
					Session::add('messages', 'messages',
						sprintf(A11YC_LANG_POST_DONE_POINTS, count($all_errs)));
				}
			}

			// page_title
			$page_title = Util::fetch_page_title_from_html($target_html);

			// results
			$errs_cnts = array_merge(
				array('total' => count($all_errs)),
				Controller_Checklist::$err_cnts
			);
			$raw = nl2br(Validate::revert_html(Util::s(Validate::get_hl_html())));

			View::assign('errs'              , $all_errs, false);
			View::assign('errs_cnts'         , $errs_cnts);
			View::assign('raw'               , $raw, false);
			View::assign('is_call_from_post' , true);
			View::assign('do_validate'       , $do_validate);
			if ($do_validate)
			{
				View::assign('result' , View::fetch_tpl('checklist/validate.php'), false);
			}
			else
			{
				View::assign('result' , View::fetch_tpl('checklist/images.php'), false);
			}

			// count up for guest users
			if ( ! Auth::auth() && ! $is_in_white_list)
			{
				// ip
				$sql = 'INSERT INTO ip (ip, datetime) VALUES (?, ?);';
				Db::execute($sql, array($ip, date('Y-m-d H:i:s')), A11YC_POST_DB);

				// cookie (session)
				Session::add('a11yc_post', 'count', time());
			}
		}

		// error
		if (Input::is_post_exists() && ! $target_html)
		{
			Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);
		}

		// title
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_POST_SERVICE_NAME);

		// assign
		View::assign('title'              , '');
		View::assign('page_title'         , $page_title);
		View::assign('real_url'           , $real_url ?: $url);
		View::assign('current_user_agent' , $default_ua);
		View::assign('user_agent'         , $user_agent);
		View::assign('target_url'         , static::$url);
		View::assign('url'                , $url);
		View::assign('target_html'        , $target_html);
		View::assign('body'               , View::fetch_tpl('post/index.php'), false);
	}

	/**
	 * load language
	 *
	 * @return Void
	 */
	private static function load_language()
	{
		// load language
		$lang = Lang::get_lang();

		if (empty($lang))
		{
			Util::error('Not found.');
		}
		include A11YC_PATH.'/languages/'.$lang.'/post.php';
	}

	/**
	 * routing
	 *
	 * @return String
	 */
	private static function routing()
	{
		// vals
		$a = Input::get('a', '');
		$controller = '\A11yc\Controller_Post';
		$action = '';
		$is_index = empty(Input::server('QUERY_STRING'));

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

		// auth - already logged in
		if (Auth::auth() && $a == 'login')
		{
			header('location:'.static::$url);
		}

		// auth - post
		if (Input::post('username') || Input::post('password'))
		{
			$action = 'Action_Login';
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

	/**
	 * init table
	 *
	 * @return Void
	 */
	private static function init_table()
	{
		if ( ! DB::is_table_exist('ip', A11YC_POST_DB))
		{
			$sql = 'CREATE TABLE ip (';
			$sql.= '`ip`        text NOT NULL,';
			$sql.= '`datetime`  datetime';
			$sql.= ');';
			DB::execute($sql, array(), A11YC_POST_DB);
		}
	}
}