<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Designbackground Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelDesignbackground extends FOFModel
{
	/**
	 * Moves an uploaded EPS file to the media://com_reddesing/assets/backgrounds/
	 * under a random name and returns a full file definition array, or false if
	 * the upload failed for any reason.
	 *
	 * @param   array  $file  The file descriptor returned by PHP
	 *
	 * @return array|bool
	 */
	public function uploadFile($file)
	{
		// Can we upload this file type?
		if (!$this->canUpload($file))
		{
			return false;
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
		$filepath = JPath::clean(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $mangledname . '.eps');

		// If we have a name clash, abort the upload
		if (JFile::exists($filepath))
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_DESIGNBACKGROUND_FILENAMEALREADYEXIST'));

			return false;
		}

		// Do the upload
		if (!JFile::upload($file['tmp_name'], $filepath))
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_DESIGNBACKGROUND_CANTJFILEUPLOAD'));

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

		$result_file = array(
			'original_filename' => $file['name'],
			'mangled_filename' => $mangledname . '.eps',
			'mime_type' => $mime,
			'filepath' => $filepath
		);

		// Return the file info
		return $result_file;
	}

	/**
	 * Checks if the EPS file can be uploaded. This is a security check.
	 *
	 * @param   array  $file  File information
	 *
	 * @return  boolean
	 */
	private function canUpload($file)
	{
		$params = JComponentHelper::getParams('com_reddesign');

		if (empty($file['name']))
		{
			$this->setError(JText::_('COM_REDDESIGN_DESIGNBACKGROUND_ERROR_UPLOAD_INPUT'));

			return false;
		}

		jimport('joomla.filesystem.file');

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$this->setError(JText::_('COM_REDDESIGN_DESIGNBACKGROUND_ERROR_FILE_NAME'));

			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		// Allowed file extensions
		$allowable = array('eps');

		if (!in_array($format, $allowable))
		{
			$this->setError(JText::_('COM_REDDESIGN_DESIGNBACKGROUND_ERROR_WRONG_FILE_EXTENSION'));

			return false;
		}

		// Max file size is set by config.xml
		$maxSize = (int) ($params->get('max_eps_file_size', 2) * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$this->setError(JText::_('COM_REDDESIGN_DESIGNBACKGROUND_ERROR_FILE_TOOLARGE'));

			return false;
		}

		// Only allow eps valid mime types
		$okMIMETypes = 'application/postscript, application/eps, application/x-eps, image/eps,image/x-eps';
		$validFileTypes = array_map('trim', explode(",", $okMIMETypes));

		// If the temp file does not have ok MIME, return
		if (!in_array($file['type'], $validFileTypes))
		{
			$this->setError(JText::_('COM_REDDESIGN_DESIGNBACKGROUND_ERROR_INVALID_MIME'));

			return false;
		}

		return true;
	}

	/**
	 * Creates a image based on a eps file to show the look and feel of the background into media://com_reddesing/assets/backgrounds/
	 *
	 * @param   string  $eps_file  the path to a .eps file
	 *
	 * @return  string
	 */
	public function createBackgroundPreview($eps_file)
	{
		$eps_file_location = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $eps_file;

		// Read EPS
		$im = new Imagick;
		$im->setResolution(100, 100);
		$im->readImage($eps_file_location);

		// Convert to jpg
		$im->setCompression(Imagick::COMPRESSION_JPEG);
		$im->setCompressionQuality(60);

		$im->setImageFormat('jpeg');

		// Create the Background thumb .jpg file name
		$thumb_name = substr($eps_file, 0, -3) . 'jpg';

		// Write image to the media://com_reddesing/assets/backgrounds/
		$im->writeImage(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $thumb_name);
		$im->clear();
		$im->destroy();

		return $thumb_name;
	}

	/**
	 * Return a list of designs.
	 *
	 * @return   array  List of designs.
	 */
	public function getDesigns()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select(array('reddesign_design_id', 'title'))
			->from('#__reddesign_designs')
			->where('enabled = 1')
			->order('ordering ASC');

		$db->setQuery($query);

		$designs_list = $db->loadObjectList();

		return $designs_list;
	}

	/**
	 * Return a list of fonts available in the system.
	 *
	 * @return   array  List of object  in the system.
	 */
	public function getFonts()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select(array('reddesign_font_id', 'title'))
			->from('#__reddesign_fonts')
			->where('enabled = 1')
			->order('ordering ASC');

		$db->setQuery($query);

		$designs_list = $db->loadObjectList();

		return $designs_list;
	}
}