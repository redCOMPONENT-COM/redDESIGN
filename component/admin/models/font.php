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
 * Font Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelFont extends FOFModel
{
	/**
	 * Moves an uploaded font file to the media://com_reddesing/assets/font
	 * under a random name and returns a full file definition array, or false if
	 * the upload failed for any reason.
	 *
	 * @param   array  $file  The file descriptor returned by PHP
	 *
	 * @return array|bool
	 */
	public function uploadFile($file)
	{
		if (isset($file['name']))
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
			elseif(function_exists('sha1'))
			{
				$mangledname = sha1($sig);
			}
			else
			{
				$mangledname = md5($sig);
			}

			// ...and its full path
			$filepath = JPath::clean(JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $mangledname . '.ttf');

			// If we have a name clash, abort the upload
			if (JFile::exists($filepath))
			{
				$this->setError(JText::_('COM_REDDESIGN_ERROR_FONT_FILENAMEALREADYEXIST'));

				return false;
			}

			// Do the upload
			if (!JFile::upload($file['tmp_name'], $filepath))
			{
				$this->setError(JText::_('COM_REDDESIGN_ERROR_FONT_CANTJFILEUPLOAD'));

				return false;
			}

			// Get the MIME type
			if (function_exists('mime_content_type'))
			{
				$mime = mime_content_type($filepath);
			}
			elseif(function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $filepath);
			}
			else
			{
				$mime = 'application/octet-stream';
			}

			// Return the file info
			return array(
				'original_filename'	=> $file['name'],
				'mangled_filename'	=> $mangledname,
				'mime_type'			=> $mime,
				'filepath'			=> $filepath
			);
		}
		else
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_FONT_NOFILE'));

			return false;
		}
	}

	/**
	 * Checks if the font file can be uploaded
	 *
	 * @param   array  $file  File information
	 *
	 * @return  boolean
	 */
	private function canUpload($file)
	{
		if (empty($file['name']))
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_UPLOAD_INPUT'));

			return false;
		}

		jimport('joomla.filesystem.file');

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_WARNFILENAME'));

			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		// Allowed file extensions
		$allowable = array('ttf');

		if (!in_array($format, $allowable))
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_WARNFILEEXTENSION'));

			return false;
		}

		// Max font file size is 2 Mb
		$maxSize = (int) (2 * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_WARNFILETOOLARGE'));

			return false;
		}

		// Only allow ttf fonts mime type
		if ($file['type'] == 'application/octet-stream')
		{
			$this->setError(JText::_('COM_REDDESIGN_ERROR_WARNINVALIDMIME'));

			return false;
		}

		return true;
	}
}