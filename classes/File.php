<?php
/**
 * A11yc\File
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */
namespace A11yc;

class File
{
	/**
	 * upload img
	 *
	 * @param String $target_path
	 * @param Integer|String $id
	 * @param String $old_path
	 * @return Bool|String
	 */
	public static function uploadImg($target_path, $id = '', $old_path = '')
	{
		$file = Input::file('file');
		if (empty($file['name'])) return $old_path;

		// mkdir
		$upload_path = A11YC_UPLOAD_PATH.'/'.Model\Data::groupId().'/'.$target_path.'/'.$id;
		$upload_path = empty($id) ? rtrim($upload_path, '/') : $upload_path;

		// at least 3 times
		if ( ! file_exists(dirname(dirname($upload_path)))) mkdir(dirname(dirname($upload_path)));
		if ( ! file_exists(dirname($upload_path))) mkdir(dirname($upload_path));
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
			return empty($errors);
		}

		return $file->getNameWithExtension();

		// $mess_type = $r ? 'messages' : 'errors';
		// $mess_str  = $r ?
		// 					 sprintf(A11YC_LANG_CTRL_PURGE_DONE, 'id: '.$id) :
		// 					 sprintf(A11YC_LANG_CTRL_PURGE_FAILED, 'id: '.$id);
		// Session::add('messages', $mess_type, $mess_str);
	}

	/**
	 * download
	 *
	 * @param String $filename
	 * @param String $text
	 * @return Void
	 */
	public static function download($filename, $text)
	{
		// export
		$filepath = sys_get_temp_dir().$filename;
		file_put_contents($filepath, $text);

		header("HTTP/1.1 200 OK");
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.filesize($filepath));
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename='.$filename);

		ob_start();
		readfile($filepath);

		$levels = ob_get_level();

		$final = '';
		for ($i = 0; $i < $levels; $i++)
		{
			$final .= ob_get_clean();
		}
		echo $final;

		exit();
	}
}
