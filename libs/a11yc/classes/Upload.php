<?php
/**
 * A11yc\Upload
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class Upload
{
	/**
	 * img
	 *
	 * @param String $target_path
	 * @param Integer $id
	 * @param String $old_path
	 * @return Bool|String
	 */
	public static function img($target_path, $id, $old_path = '')
	{
		$file = Input::file('file');
		if (empty($file['name'])) return $old_path;

		// mkdir
		$upload_path = A11YC_UPLOAD_PATH.'/'.$target_path.'/'.$id;
		if ( ! file_exists($upload_path)) mkdir($upload_path);

		// unlink
		if ( ! empty($old_path) && file_exists($upload_path.'/'.$old_path)) unlink($upload_path.'/'.$old_path);

		// prepare
		require(A11YC_LIB_PATH.'/Upload/Autoloader.php');
		\Upload\Autoloader::register();
		$storage = new \Upload\Storage\FileSystem($upload_path);
		$file = new \Upload\File('file', $storage);

		// set unique name
		$new_filename = uniqid();
		$file->setName($new_filename);

		// validate
		$file->addValidations(array(
				new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg')),
				new \Upload\Validation\Size('5M')
			));

		// upload
		try
		{
			$file->upload();
		}
		catch (\Exception $e)
		{
			$errors = $file->getErrors();
		}

		return $file->getNameWithExtension();

		// $mess_type = $r ? 'messages' : 'errors';
		// $mess_str  = $r ?
		// 					 sprintf(A11YC_LANG_PAGES_PURGE_DONE, 'id: '.$id) :
		// 					 sprintf(A11YC_LANG_PAGES_PURGE_FAILED, 'id: '.$id);
		// Session::add('messages', $mess_type, $mess_str);
	}
}
