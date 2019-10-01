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
	use PostAuth;

	private static $behaviours = array(
		'images',
		'csv',
		'check',
	);

	/**
	 * set url consts
	 *
	 * @return Void
	 */
	public static function setUrlConsts()
	{
		defined('A11YC_POST_SCRIPT_NAME') or define('A11YC_POST_SCRIPT_NAME', '/post.php');
		defined('A11YC_IMAGELIST_URL') or define('A11YC_IMAGELIST_URL', '?behaviour=images&amp;url=');
	}

	/**
	 * set consts
	 *
	 * @return Void
	 */
	public static function setConsts()
	{
		// max post a day by ip
		defined('A11YC_POST_IP_MAX_A_DAY') or define('A11YC_POST_IP_MAX_A_DAY', 800);
		defined('A11YC_POST_COOKIE_A_10MIN') or define('A11YC_POST_COOKIE_A_10MIN', 400);

		// Google Analytics
		defined('A11YC_POST_GOOGLE_ANALYTICS_CODE') or define('A11YC_POST_GOOGLE_ANALYTICS_CODE', '');

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

		// set const
		self::setUrlConsts();

		// a11yc
		require (dirname(dirname(__DIR__)).'/main.php');

		// set const
		self::setConsts();

		// session
 	Session::forge('A11YCONLINEVALIDATE');

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
	 * Action_Docs
	 *
	 * @return Void
	 */
	public static function actionDocs()
	{
		View::assign('a11yc_doc_url', A11YC_POST_SCRIPT_URL.'?a=doc&amp;criterion=');
		Doc::index(); // $body set
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_DOC_TITLE);
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
			Doc::each($criterion); // $body set
			$doc = View::fetch('doc');
			define('A11YC_LANG_POST_TITLE', $doc['name']);
			return;
		}
		Util::error('service not available.');
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
		$url          = Input::post('url', Input::get('url', ''));
		$user_agent   = Input::post('user_agent', '');
		$default_ua   = Input::userAgent();
		$doc_root     = Input::post('doc_root', 'http://localhost');
		$do_css_check = Input::post('do_css_check', false);

		// host check
		$doc_root = strpos($url, $doc_root) !== false ? $doc_root : Crawl::getHostFromUrl($url);

		// User Agent
		$current_ua = self::setCurrentUa($user_agent, $default_ua);

		// fallback
		View::assign('errs', array());

		// auth - if limit die here: use PostAuth
		static::auth();

		// validation
		list($target_html, $do_validate) = self::getHtmlAndCheckDoValidate($url, $doc_root, $current_ua);

		// Do Validate - if image list, not validate
		if ($target_html && $do_validate)
		{
			self::validate($url, $target_html, $current_ua, $do_css_check);
		}

		// when post/get exists set message and template
		if (Input::isPostExists() || Input::get('url'))
		{
			self::setMessage($target_html, $url);

			// choose template validate or image list
			$tpl = Input::param('behaviour') == 'images' ? 'checklist/images.php' : 'checklist/validate.php';
			View::assign('result', View::fetchTpl($tpl), false);
		}

		// title
		define('A11YC_LANG_POST_TITLE', A11YC_LANG_POST_SERVICE_NAME);

		// assign
		View::assign('do_css_check'       , $do_css_check);
		View::assign('title'              , ''); // need for header
		View::assign('current_user_agent' , $current_ua);
		View::assign('doc_root'           , $doc_root);
		View::assign('user_agent'         , $user_agent);
		View::assign('script_url'         , A11YC_POST_SCRIPT_URL);
		View::assign('url'                , $url);
		View::assign('target_html'        , $target_html);
		View::assign('body'               , View::fetchTpl('post/index.php'), false);
	}

	/**
	 * setCurrentUa
	 *
	 * @param $user_agent
	 * @param $default_ua
	 * @return String
	 */
	private static function setCurrentUa($user_agent, $default_ua)
	{
		$uas = Values::uas();
//		$ua = Arr::get($uas, $user_agent) ? $user_agent : $default_ua;
		$current_ua = Arr::get($uas, "{$user_agent}.str");
		$current_ua = $current_ua ?: $default_ua;
		return $current_ua;
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
		elseif ( ! empty($url))
		{
			$target_html = Model\Html::fetchHtmlFromInternet($url, $ua); // not use Database
			$target_html = is_string($target_html) ? $target_html : '';
			$do_validate = self::failedOrDoOtherAction($url, $doc_root, $target_html);
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
			View::assign('page_title', Model\Html::pageTitleFromHtml($target_html));
		}
	}

	/**
	 * failedOrDoOtherAction
	 *
	 * @param String $url
	 * @param String $doc_root
	 * @param String $target_html
	 * @return Bool|Void
	 */
	private static function failedOrDoOtherAction($url, $doc_root, $target_html)
	{
		$do_validate = true;

		// basic auth failed
		if (Guzzle::instance($url)->status_code == 401)
		{
			$do_validate = false;
			Session::add('messages', 'errors', A11YC_LANG_POST_BASIC_AUTH_EXP);
			return $do_validate;
		}

		// connection problems
		if (Guzzle::instance($url)->errors)
		{
			$do_validate = false;
			Session::add('messages', 'errors', A11YC_LANG_ERROR_COULD_NOT_ESTABLISH_CONNECTION);
			return $do_validate;
		}

		// images
		if (
			Input::param('behaviour') == 'images' ||
			Input::get('mode') == 'images'
		)
		{
			$do_validate = false;
			View::assign('images', A11yc\Image::getImages($url, $doc_root, $target_html));
			Session::add('messages', 'messages', A11YC_LANG_POST_DONE_IMAGE_LIST);
			return $do_validate;
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
	 * @param String $url
	 * @param String $target_html
	 * @param String $ua
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

		// count up for guest users : use PostAuth
		static::countUpForGuestUsers();
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
		$behaviour = Input::post('behaviour');

		if ( ! empty($behaviour) && ! in_array($behaviour, self::$behaviours))
		{
			Util::error('service not available.');
		}

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
