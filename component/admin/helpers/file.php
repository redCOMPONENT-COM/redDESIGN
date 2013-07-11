<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;


/**
 * File helper.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignHelperFile
{
	/**
	 * Uploads file to the given assets folder.
	 *
	 * @param   array   $file              The file descriptor returned by PHP
	 * @param   string  $assetsFolder      Name of a folder in media://com_reddesign/assets/.
	 * @param   int     $maxFileSize       Maximum allowed file size.
	 * @param   string  $okFileExtensions  Comma separated string list of allowed file extensions.
	 * @param   string  $okMIMETypes       Comma separated string list of allowed MIME types.
	 *
	 * @return array|bool
	 */
	public function uploadFile($file, $assetsFolder, $maxFileSize = 2, $okFileExtensions = null, $okMIMETypes = null)
	{
		$app = JFactory::getApplication();
		$fileExtension = JFile::getExt($file['name']);

		// Can we upload this file type?
		if (!$this->canUpload($file, $maxFileSize, $okFileExtensions, $okMIMETypes))
		{
			return false;
		}

		// Check folders
		if (!JFolder::exists(FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/')))
		{
			JFolder::create(FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/'));
		}

		// Get a (very!) randomised name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverkey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverkey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $file['name'] . microtime() . $serverkey;

		if (function_exists('sha256'))
		{
			$mangledname = sha256($sig);
		}
		elseif (function_exists('sha1'))
		{
			$mangledname = sha1($sig);
		}
		else
		{
			$mangledname = md5($sig);
		}

		// ...and its full path
		$filepath = JPath::clean(JPATH_ROOT . '/media/com_reddesign/assets/' . $assetsFolder . '/' . $mangledname . '.' . $fileExtension);

		// If we have a name clash, abort the upload
		if (JFile::exists($filepath))
		{
			$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_FILE_HELPER_FILENAMEALREADYEXIST', $filepath), 'error');

			return false;
		}

		// Do the upload
		if (!JFile::upload($file['tmp_name'], $filepath))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FILE_HELPER_CANTJFILEUPLOAD'), 'error');

			return false;
		}

		// Get the MIME type
		if (function_exists('mime_content_type'))
		{
			$mime = mime_content_type($filepath);
		}
		elseif (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $filepath);
		}
		else
		{
			$mime = 'application/postscript';
		}

		$resultFile = array(
			'original_filename' => $file['name'],
			'mangled_filename' => $mangledname . '.' . $fileExtension,
			'mime_type' => $mime,
			'filepath' => $filepath
		);

		// Return the file info
		return $resultFile;
	}

	/**
	 * Checks if the file can be uploaded.
	 *
	 * @param   array   $file              File information.
	 * @param   int     $maxFileSize       Maximum allowed file size.
	 * @param   string  $okFileExtensions  Comma separated string list of allowed file extensions.
	 * @param   string  $okMIMETypes       Comma separated string list of allowed MIME types.
	 *
	 * @return  boolean
	 */
	private function canUpload($file, $maxFileSize = 2, $okFileExtensions = null, $okMIMETypes = null)
	{
		$app = JFactory::getApplication();

		if (empty($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FILE_HELPER_FILE_NAME_EMPTY'), 'error');

			return false;
		}

		jimport('joomla.filesystem.file');

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_FILE_HELPER_ERROR_FILE_NAME', $file['name']), 'error');

			return false;
		}

		// Allowed file extensions
		if (!empty($okFileExtensions))
		{
			$format = strtolower(JFile::getExt($file['name']));
			$allowable = array_map('trim', explode(",", $okFileExtensions));

			if (!in_array($format, $allowable))
			{
				$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_FILE_HELPER_ERROR_WRONG_FILE_EXTENSION', $format, $okFileExtensions), 'error');

				return false;
			}
		}

		// Max file size is set by config.xml
		$maxSize = (int) ($maxFileSize * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_FILE_HELPER_ERROR_FILE_TOOLARGE', $maxFileSize), 'error');

			return false;
		}

		// Allowed file extensions
		if (!empty($okMIMETypes))
		{
			$validFileTypes = array_map('trim', explode(",", $okMIMETypes));

			if (!in_array($file['type'], $validFileTypes))
			{
				$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_FILE_HELPER_ERROR_INVALID_MIME', $file['type'], $okMIMETypes));

				return false;
			}
		}

		return true;
	}
}