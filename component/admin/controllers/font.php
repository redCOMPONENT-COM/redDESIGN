<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * The font edit controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class ReddesignControllerFont extends RControllerForm
{
	/**
	 * Method to save a record. We need first to upload font files,
	 * than after that we can save DB font record to the #__redddesign_fonts.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		$file = $this->input->files->get('jform');
		$file = $file['font_file'];
		$uploaded_file = null;

		// If file has been uploaded, process it
		if (!empty($file['name']) && !empty($file['type']))
		{
			// Upload the font file
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'fonts', 2, 'ttf');
		}

		// Delete font .ttf file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $uploaded_file['mangled_filename']))
		{
			// Create a image preview of the Font
			$this->createFontPreviewThumb($uploaded_file['mangled_filename']);

			$data  = $this->input->post->get('jform', array(), 'array');

			if (empty($data['title']))
			{
				$data['title'] = $file['name'];
				$this->input->post->set('jform', $data);
			}

			return parent::save($key, $urlVar);
		}
		else
		{
			$recordId = $this->input->getInt($urlVar, null);
			$this->setMessage(JText::_('COM_REDDESIGN_FONT_ERROR_UPLOAD_INPUT'), 'error');

			// Redirect back to the edit screen.
			$this->setRedirect(
				$this->getRedirectToItemRoute($this->getRedirectToItemAppend($recordId, $urlVar))
			);

			return false;
		}
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
		$font_file_location = JPATH_ROOT . '/media/com_reddesign/fonts/' . $font_file;
		$params				= JComponentHelper::getParams('com_reddesign');
		$text				= $params->get('font_preview_text', 'Lorem ipsum.');

		$im = $this->imageTtfJustifyText($text, $font_file_location, 2, 2);

		// Creates the Font thumb .png file
		$font_thumb = substr($font_file, 0, -3) . 'png';
		imagepng($im, JPATH_ROOT . '/media/com_reddesign/fonts/' . $font_thumb);

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
	 * @param   int     $fsize    Font size of text.
	 * @param   array   $color    RGB color array for text color.
	 * @param   array   $bgcolor  RGB color array for background.
	 *
	 * @return   resource  image resource
	 */
	private function imageTtfJustifyText($text, $font, $Justify = 2, $Leading = 0, $W = 0, $H = 0, $X = 0, $fsize = 20, $color = array(0x0, 0x0, 0x0), $bgcolor = array(0xFF, 0xFF, 0xFF))
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
		imagecolorallocate($im, $bgcolor[0], $bgcolor[1], $bgcolor[2]);

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
}
