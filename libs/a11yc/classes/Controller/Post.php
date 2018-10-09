<?php
/**
 * A11yc\Controller\Post
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
namespace A11yc\Controller;

use A11yc\Model;
use A11yc\Validate;

class Post
{
	/**
	 * set consts
	 *
	 * @return Void
	 */
	public static function setConsts()
	{
		// max post a day by ip
		defined('A11YC_POST_IP_MAX_A_DAY') or define('A11YC_POST_IP_MAX_A_DAY', 200);
		defined('A11YC_POST_COOKIE_A_10MIN') or define('A11YC_POST_COOKIE_A_10MIN', 30);

		// Google Analytics
		defined('A11YC_POST_GOOGLE_ANALYTICS_CODE') or define('A11YC_POST_GOOGLE_ANALYTICS_CODE', '');

		// script name
		defined('A11YC_POST_SCRIPT_NAME') or define('A11YC_POST_SCRIPT_NAME', '/post.php');

		// SCRIPT URL
		$url = Util::removeQueryStrings(Util::uri());
		$url.= strpos($url, A11YC_POST_SCRIPT_NAME) === false ? A11YC_POST_SCRIPT_NAME : '';
		defined('A11YC_POST_SCRIPT_URL') or define('A11YC_POST_SCRIPT_URL', $url);
	}

	/**
	 * forge
	 *
	 * @return Void
	 */
	public static function forge()
	{
		// is guest validation
		defined('A11YC_IS_GUEST_VALIDATION') or define('A11YC_IS_GUEST_VALIDATION', true);

		// a11yc
		require (dirname(dirname(__DIR__)).'/main.php');

		// set const
		self::setConsts();

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
		if (A11yc\Auth::auth())
		{
			// login user
			$login_user = Users::fetchCurrentUser();
			View::assign('login_user', $login_user);
		}

		// view
		View::addTplPath(A11YC_PATH.'/views/post');

		// set base url before call controllers
		View::assign('base_url', A11YC_POST_SCRIPT_URL);

		// routing
		self::routing();
		$action = Route::getAction();
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
	public static function actionLogin()
	{
		Auth::actionLogin();
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_AUTH_TITLE);
	}

	/**
	 * Action_Logout
	 *
	 * @return Void
	 */
	public static function actionLogout()
	{
		Auth::actionLogout(A11YC_POST_SCRIPT_URL);
	}

	/**
	 * Action_Docs
	 *
	 * @return Void
	 */
	public static function actionDocs()
	{
		View::assign('a11yc_doc_url', A11YC_POST_SCRIPT_URL.'?a=doc&amp;criterion=');
		Docs::index(); // $body set
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_DOCS_TITLE);
	}

	/**
	 * Action_Doc
	 *
	 * @return Void
	 */
	public static function actionDoc()
	{
		$criterion = Input::get('criterion');
		if ($criterion)
		{
			View::assign('a11yc_doc_url', A11YC_POST_SCRIPT_URL.'?a=doc&amp;criterion=');
			Docs::each($criterion); // $body set
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
	public static function actionReadme()
	{
		View::assign('body', View::fetchTpl('post/readme.php'), false);
		View::assign('title', A11YC_LANG_POST_README);
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_POST_README);
	}

	/**
	 * Action_Validation
	 *
	 * @return Void
	 */
	public static function actionValidation()
	{
		// vals
		$url              = Input::post('url', Input::get('url', ''));
		$user_agent       = Input::post('user_agent', '');
		$default_ua       = Input::userAgent();
		$doc_root         = Input::post('doc_root');
		$target_html      = Input::post('source', '');
		$do_css_check     = Input::post('do_css_check', false);

		// host check
		if (empty($url))
		{
			$url = 'not specified';
			$doc_root = 'not specified';
		}
		else
		{
			$doc_root = strpos($url, $doc_root) !== false ? $doc_root : Crawl::getHostFromUrl($url);
		}

		// User Agent
		$uas = Values::uas();
		$ua = Arr::get($uas, $user_agent) ? $user_agent : $default_ua;
		$current_ua = Arr::get($uas, "{$user_agent}.str");
		$current_ua = $current_ua ?: $default_ua;

		// fallback
		View::assign('errs', array());

		// auth - if limit die here
		Post\Auth::auth();

		// validation
		list($target_html, $do_validate) = self::getHtmlAndCheckDoValidate($url, $doc_root, $ua);

		// Do Validate - if image list, not validate
		if ($target_html && $do_validate)
		{
			self::validate($url, $target_html, $ua, $do_css_check);
		}

		// when post/get exists set message and template
		if (Input::isPostExists() || Input::get('url'))
		{
			self::setMessage($target_html, $url);

			// choose template validate or image list
			$tpl = $do_validate ? 'checklist/validate.php' : 'checklist/images.php' ;
			View::assign('result', View::fetchTpl($tpl), false);
		}

		// title
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_POST_SERVICE_NAME);

		// assign
		View::assign('do_css_check'       , $do_css_check);
		View::assign('title'              , ''); // need for header
		View::assign('current_user_agent' , $current_ua);
		View::assign('doc_root'           , $doc_root == 'not specified' ? '' : $doc_root);
		View::assign('user_agent'         , $user_agent);
		View::assign('script_url'         , A11YC_POST_SCRIPT_URL);
		View::assign('url'                , $url == 'not specified' ? '' : $url);
		View::assign('target_html'        , $target_html);
		View::assign('body'               , View::fetchTpl('post/index.php'), false);
	}

	/**
	 * getHtmlAndCheckDoValidate
	 *
	 * @param String $url
	 * @param String $doc_root
	 * @param String $ua
	 * @return Array
	 */
	private static function getHtmlAndCheckDoValidate($url, $doc_root, $ua)
	{
		$target_html = '';
		$do_validate = true;
		if (Input::post('source'))
		{
			$target_html = Input::post('source');
		}
		elseif ($url)
		{
			$target_html = Model\Html::fetchHtml($url, $ua); // not use Database
			$do_validate = self::failedOrDoOtherAction($url, $doc_root);
		}
		return array($target_html, $do_validate);
	}

	/**
	 * setMessage
	 *
	 * @param String $target_html
	 * @param String $url
	 * @return Bool
	 */
	private static function setMessage($target_html, $url)
	{
		if ( ! $target_html && $url)
		{
			Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR);

			if (strpos($url, 'http') === false)
			{
				Session::add('messages', 'errors', A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR_NO_SCHEME);
			}
		}

		if ($target_html)
		{
			View::assign('page_title', Model\Html::fetchPageTitleFromHtml($target_html));
		}
	}

	/**
	 * failedOrDoOtherAction
	 *
	 * @param String $url
	 * @param String $doc_root
	 * @return Bool|Void
	 */
	private static function failedOrDoOtherAction($url, $doc_root)
	{
		$do_validate = true;

		// basic auth failed
		if (Guzzle::instance($url)->status_code == 401)
		{
			$do_validate = false;
			Session::add('messages', 'errors', A11YC_LANG_POST_BASIC_AUTH_EXP);
		}

		// connection problems
		if (Guzzle::instance($url)->errors)
		{
			$do_validate = false;
			Session::add('messages', 'errors', A11YC_LANG_ERROR_COULD_NOT_ESTABLISH_CONNECTION);
		}

		// images
		if (
			Input::post('behaviour') == 'images' ||
			Input::get('mode') == 'images'
		)
		{
			$do_validate = false;
			View::assign('images', A11yc\Images::getImages($url, $doc_root));
			Session::add('messages', 'messages', A11YC_LANG_POST_DONE_IMAGE_LIST);
		}

		// export CSV
		if (Input::post('behaviour') == 'csv')
		{
			Export::csv($url); // exit()
		}

		return $do_validate;
	}

	/**
	 * validate
	 *
	 * @param Strings $url
	 * @param Strings $target_html
	 * @param Strings $ua
	 * @param Bool $do_css_check
	 * @return Array
	 */
	private static function validate($url, $target_html, $ua, $do_css_check = false)
	{
		// check
		$codes = Validate::$codes;
		Validate::$do_css_check = $do_css_check;
		Validate::html($url, $target_html, $codes, $ua);
		$all_errs = Validate\Get::errors($url, $codes, $ua);

		// message
		Session::add('messages', 'messages', A11YC_LANG_POST_DONE);
		if (count($all_errs['errors']) == 0)
		{
			Session::add('messages', 'messages',
				A11YC_LANG_CHECKLIST_NOT_FOUND_ERR);
		}
		else
		{
			Session::add('messages', 'messages',
				sprintf(A11YC_LANG_POST_DONE_POINTS, count($all_errs['errors'])));
		}
		if (count($all_errs['notices']) != 0)
		{
			Session::add('messages', 'messages',
				sprintf(A11YC_LANG_POST_DONE_NOTICE_POINTS, count($all_errs['notices'])));
		}

		// results
		$errs_cnts = array_merge(
			array('total' => count($all_errs['errors'])),
			Validate\Get::errorCnts($url, $codes, $ua)
		);
		$render = Validate\Get::highLightedHtml($url, $codes, $ua);
		$raw = nl2br($render);

		View::assign('errs'                , $all_errs, false);
		View::assign('errs_cnts'           , $errs_cnts);
		View::assign('raw'                 , $raw, false);
		View::assign('is_call_from_post'   , true);
		View::assign('machine_check_status', Values::machineCheckStatus());
		View::assign('yml'                 , Yaml::fetch());
		View::assign('logs'                , Validate\Get::logs($url) ?: array());

		// count up for guest users
		Post\Auth::countUpForGuestUsers();
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
		$controller = '\A11yc\Controller\Post';
		$action = '';
		$is_index = empty(Input::server('QUERY_STRING')) || Input::get('url');

		// top page
		if ($is_index)
		{
			$action = 'actionValidation';
		}

		// safe access?
		if ( ! $is_index && ctype_alnum($a))
		{
			$action = 'action'.ucfirst($a);
		}

		// auth - already logged in
		if (A11yc\Auth::auth() && $a == 'login')
		{
			header('location:'.A11YC_POST_SCRIPT_URL);
		}

		// auth - post
		if (Input::post('username') || Input::post('password'))
		{
			$action = 'actionLogin';
		}

		// class and methods exists
		if (
			method_exists($controller, $action) &&
			is_callable($controller.'::'.$action)
		)
		{
			Route::setController($controller);
			Route::setAction($action);
			return;
		}

		// error
		Util::error('service not available.');
	}
}
