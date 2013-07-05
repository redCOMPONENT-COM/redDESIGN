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
 * Font Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerFont extends FOFController
{
	/**
	 * Constructor to set the right model
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'font';
	}

	/**
	 * Uploads the font file and generates a image preview of the font
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  boolean  Returns true on success
	 */
	public function onBeforeApplySave(&$data)
	{
		$file = $this->input->files->get('font_file');

		// If file has been uploaded, process it
		if (!empty($file['name']) && !empty($file['type']))
		{
			// Upload the font file
			$uploaded_file = $this->uploadFile($file);

			// Create a image preview of the Font
			$font_thumb = $this->createFontPreviewThumb($uploaded_file['mangled_filename']);

			// Update the database with the new path of the font file
			$data['font_file'] = $uploaded_file['mangled_filename'];
			$data['font_thumb'] = $font_thumb;

			// Update the database with the font file name
			if (empty($data['title']))
			{
				$data['title'] = $this->getFontFileName($uploaded_file['filepath']);
			}
		}

		return $data;
	}

	/**
	 * Moves an uploaded font file to the media://com_reddesign/assets/font
	 * under a random name and returns a full file definition array, or false if
	 * the upload failed for any reason.
	 *
	 * @param   array  $file  The file descriptor returned by PHP
	 *
	 * @return array|bool
	 */
	private function uploadFile($file)
	{
		$app = JFactory::getApplication();

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
			elseif (function_exists('sha1'))
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
				$app->enqueueMessage(JText::_('COM_REDDESIGN_ERROR_FONT_FILENAMEALREADYEXIST'), 'error');

				return false;
			}

			// Do the upload
			if (!JFile::upload($file['tmp_name'], $filepath))
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_ERROR_FONT_CANTJFILEUPLOAD'), 'error');

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
				$mime = 'application/octet-stream';
			}

			// Return the file info
			return array(
				'original_filename' => $file['name'],
				'mangled_filename' => $mangledname . '.ttf',
				'mime_type' => $mime,
				'filepath' => $filepath
			);
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_ERROR_FONT_NOFILE'), 'error');

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
		$app = JFactory::getApplication();

		if (empty($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_UPLOAD_INPUT'), 'error');

			return false;
		}

		jimport('joomla.filesystem.file');

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_FILE_NAME'), 'error');

			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		// Allowed file extensions
		$allowable = array('ttf');

		if (!in_array($format, $allowable))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_WRONG_FILE_EXTENSION'), 'error');

			return false;
		}

		// Max font file size is 2 Mb
		$maxSize = (int) (2 * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_FILE_TOOLARGE'), 'error');

			return false;
		}

		// Only allow ttf fonts mime type
		if (!$file['type'] == 'application/octet-stream')
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_INVALID_MIME'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Creates a image based on a ttf font file to show the look and feel of the font.
	 *
	 * @param   string  $font_file  the path to a .ttf font file
	 *
	 * @return  string
	 */
	private function createFontPreviewThumb($font_file)
	{
		$font_file_location = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $font_file;

		$text = 'Lorem ipsum';

		$im = $this->imagettfJustifytext(
			$text,
			$font_file_location,
			2,
			2
		);

		// Creates the Font thumb .png file
		$font_thumb = substr($font_file, 0, -3) . 'png';
		imagepng(
			$im,
			JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $font_thumb
		);

		return $font_thumb;
	}

	/**
	 * Function for create image from text with selected font. Justify text in image (0-Left, 1-Right, 2-Center).
	 *
	 * @param   string  $text     String to convert into the Image.
	 * @param   string  $font     Font name of the text. Kip font file in same folder.
	 * @param   int     $Justify  Justify text in image (0-Left, 1-Right, 2-Center).
	 * @param   int     $Leading  Space between lines.
	 * @param   int     $W        Width of the Image.
	 * @param   int     $H        Hight of the Image.
	 * @param   int     $X        x-coordinate of the text into the image.
	 * @param   int     $Y        y-coordinate of the text into the image.
	 * @param   int     $fsize    Font size of text.
	 * @param   array   $color    RGB color array for text color.
	 * @param   array   $bgcolor  RGB color array for background.
	 *
	 * @return   resource  image resource
	 */
	private function imagettfJustifytext($text, $font, $Justify = 2, $Leading = 0, $W = 0, $H = 0, $X = 0, $Y = 0,
		$fsize = 20, $color = array(0x0, 0x0, 0x0), $bgcolor = array(0xFF, 0xFF, 0xFF))
	{
		$angle = 0;
		$_bx = imageTTFBbox($fsize, 0, $font, $text);

		// Array of lines
		$s = explode('\n', $text);

		// Number of lines
		$nL = count($s);

		// If Width not initialized by programmer then it will detect and assign perfect width.
		$W = ($W == 0) ? abs($_bx[2] - $_bx[0]) : $W;

		// If Height not initialized by programmer then it will detect and assign perfect height.
		$H = ($H == 0) ? abs($_bx[5] - $_bx[3]) + ($nL > 1 ? ($nL * $Leading) : 0) : $H;

		$im = imagecreate($W + 2, $H + 8)
		or die("Cannot Initialize new GD image stream");

		// RGB color background.
		$background_color = imagecolorallocate($im, $bgcolor[0], $bgcolor[1], $bgcolor[2]);

		// RGB color text.
		$text_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);

		if ($Justify == 0)
		{
			// Justify Left
			imagettftext($im, $fsize, $angle, $X, $fsize, $text_color, $font, $text);
		}
		else
		{
			// Create alpha-nummeric string with all international characters - both upper- and lowercase
			$alpha = implode('', range("a", "z"));
			$alpha = $alpha . strtoupper($alpha) . implode('', range(0, 9));

			// Use the string to determine the height of a line
			$_b = imageTTFBbox($fsize, 0, $font, $alpha);
			$_H = abs($_b[5] - $_b[3]);
			$__H = 4;

			for ($i = 0; $i < $nL; $i++)
			{
				$_b = imageTTFBbox($fsize, 0, $font, $s[$i]);
				$_W = abs($_b[2] - $_b[0]);

				// Justify Right
				if ($Justify == 1)
				{
					$_X = $W - $_W;
				}
				else
				{
					// Justify Center
					$_X = abs($W / 2) - abs($_W / 2);
				}

				// Defining the Y coordinate.
				$__H += $_H;
				imagettftext($im, $fsize, $angle, $_X, $__H, $text_color, $font, $s[$i]);
				$__H += $Leading;
			}
		}

		return $im;
	}

	/**
	 * Returns the font name from a specific .ttf file using an external helper
	 *
	 * @param   string  $ttf_file  The .ttf resource file
	 *
	 * @return  string|false
	 */
	public function getFontFileName($ttf_file)
	{
		// Get the font name from the .ttf file
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/classTTFInfo.php';

		$ttf = new ttf;
		$ttf_info = $ttf->get_friendly_ttf_name($ttf_file);

		if (empty($ttf_info['fullfontname']))
		{
			return false;
		}

		return $ttf_info['fullfontname'];
	}
}
