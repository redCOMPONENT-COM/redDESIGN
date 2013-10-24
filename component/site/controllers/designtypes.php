<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;


/**
 * Designtype Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignControllerDesigntypes extends FOFController
{
	/**
	 * Class constructor
	 *
	 * @param   array  $config  Config.
	 *
	 * @access public
	 */
	public function  __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'Designtypes';
	}

	/**
	 * Executes a task
	 *
	 * @param   string  $task  The task to be executed
	 *
	 * @return bool|null|void
	 *
	 * @access public
	 */
	public function execute($task)
	{
		$this->registerDefaultTask('read');

		parent::execute($task);
	}

	/**
	 * Returns a customized design image url
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function ajaxGetDesign()
	{
		JSession::checkToken('get') or jexit('Invalid Token');

		// Initialize session
		$session = JFactory::getSession();

		// Get design Data
		$design = new JRegistry;
		$design->loadString($this->input->getString('designarea', ''), 'JSON');
		$design = $design->get('Design');

		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($design->reddesign_designtype_id);
		$background = $backgroundModel->getItem($design->reddesign_background_id);
		$backgroundImage = $background->image_path;

		// Get a (very!) randomized name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverkey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverkey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $backgroundImage . microtime() . $serverkey;

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

		$backgroundImageFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/checkerboard' . $backgroundImage;
		$newjpgFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';

		// Create Imagick object.
		$newImage = new Imagick;
		$newImage->readImage($backgroundImageFileLocation);

		// Add text areas to the background image.
		foreach ($design->areas as $area)
		{
			if (!empty($area->textArea) || $area->textArea == 0)
			{
				// Create needed objects.
				$areaImage = new Imagick;
				$areaDraw  = new ImagickDraw;

				// Get font.
				if (!empty($area->fontTypeId))
				{
					$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
					$fontType = $fontModel->getItem($area->fontTypeId);
					$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $fontType->font_file;
				}
				else
				{
					$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
					$fontType = new stdClass;
					$fontType->title = 'Arial';
					$fontType->font_file = 'arial.ttf';
					$fontType->default_width = 0.9;
					$fontType->default_height = 0.9;
					$fontType->default_caps_height = 0.9;
					$fontType->default_baseline_height = 0.9;
					$area->fontTypeId = 0;
				}

				// Get area.
				$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($design->reddesign_background_id);
				$this->areaItem = $areaModel->getItem($area->id);
				$topoffset = 0;

				// If we need autosize text than take different approach than solution for regular text.
				if (empty($area->fontSize))
				{
					$newAutoSizeData = $this->getFontSizeOnCharsBase($area->fontTypeId, $area->textArea, $fontType, $this->areaItem->height, $this->areaItem->width);

					if (!empty($area->plg_dimension_base) && !empty($area->plg_dimension_base_input))
					{
						$dimension = $this->getCanvaseDimension(
							$area->plg_dimension_base,
							$area->plg_dimension_base_input,
							$area->fontTypeId,
							$area->textArea,
							$fontType
						);
						$topoffset = $newAutoSizeData['topoffset'];
						$newAutoSizeData['canvasHeight'] = $dimension['canvasHeight'];
						$newAutoSizeData['canvasWidth'] = $dimension['canvasWidth'];
					}

					$area->fontSize = $newAutoSizeData['fontSize'];
					$newAutoSizeData['reddesign_area_id'] = $this->areaItem->reddesign_area_id;
					$autoSizeData[] = $newAutoSizeData;
					$this->areaItem->textalign = 3;
				}

				// Create an area image.
				$areaImage->newImage($this->areaItem->width, $this->areaItem->height, new ImagickPixel('transparent'));

				// Set color and font.
				$areaDraw->setFont($fontTypeFileLocation);
				$areaDraw->setFillColor('#' . $area->fontColor);
				$areaDraw->setFillOpacity(1);
				$areaDraw->setFontSize($area->fontSize);
				$areaDraw->setTextAntialias(true);

				/*
				 * Text alingment condition:
				 * 1 is left,
				 * 2 is right,
				 * 3 is center.
				 */
				if ((int) $this->areaItem->textalign == 1)
				{
					$areaDraw->setGravity(Imagick::GRAVITY_WEST);
				}
				elseif ((int) $this->areaItem->textalign == 2)
				{
					$areaDraw->setGravity(Imagick::GRAVITY_EAST);
				}
				else
				{
					$areaDraw->setGravity(Imagick::GRAVITY_CENTER);
				}

				// Add text to the area image.
				$areaImage->annotateImage($areaDraw, 0, $topoffset, 0, $area->textArea);

				// Convert CMYK color profile of the EPS image to RGB color profile.
				if ($newImage->getImageColorspace() == Imagick::COLORSPACE_CMYK)
				{
					$profiles = $newImage->getImageProfiles('*', false);

					// We're only interested if ICC profile(s) exist.
					$has_icc_profile = (array_search('icc', $profiles) !== false);

					// If it doesnt have a CMYK ICC profile, we add one.
					if ($has_icc_profile === false)
					{
						$icc_cmyk = file_get_contents(JPATH_ROOT . '/media/com_reddesign/assets/colorprofiles/USWebUncoated.icc');
						$newImage->profileImage('icc', $icc_cmyk);
						unset($icc_cmyk);
					}

					// Then we add an RGB profile.
					$icc_rgb = file_get_contents(JPATH_ROOT . '/media/com_reddesign/assets/colorprofiles/sRGB_v4_ICC_preference.icc');
					$newImage->profileImage('icc', $icc_rgb);
					unset($icc_rgb);
				}

				// This will drop down the size of the image dramatically (removes all profiles).
				$newImage->stripImage();

				// Put second image on top of the first.
				$newImage->compositeImage($areaImage, $areaImage->getImageCompose(), $this->areaItem->x1_pos, $this->areaItem->y1_pos);

				$newImage->writeImage($newjpgFileLocation);

				// Free resources.
				$areaImage->clear();
				$areaImage->destroy();
			}
			else
			{
				$newImage->writeImage($newjpgFileLocation);
			}
		}

		// Free resources.
		$newImage->clear();
		$newImage->destroy();

		// Create session to store Image
		$session->set('customizedImage', $mangledname);
		$response['image'] = JURI::base() . 'media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';
		$response['imageTitle'] = $background->title;
		$imageSize = getimagesize(JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg');
		$response['imageWidth'] = $imageSize[0];
		$response['imageHeight'] = $imageSize[1];

		if (!empty($autoSizeData))
		{
			$response['autoSizeData'] = $autoSizeData;
		}

		echo json_encode($response);
	}

	/**
	 *  Calculates Font size and Offset when Auto-size is on.
	 *
	 *  @param   int    $fontId         FontId.
	 *  @param   array  $enteredChars   EnteredChars.
	 *  @param   array  $fontDetailArr  FontDetailArr.
	 *  @param   int    $canvasHeight   CanvasHeight.
	 *  @param   int    $canvasWidth    CanvasWidth.
	 *
	 * @return array of Font Size and Offset respectively
	 *
	 * @access public
	 */
	public function getFontSizeOnCharsBase($fontId, $enteredChars, $fontDetailArr, $canvasHeight, $canvasWidth)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$char = str_split(mb_convert_encoding(urldecode($enteredChars), "ISO-8859-1", "UTF-8"));

		// Select character settings for a given font.
		$query
			->select('max(chars.height) as height, group_concat(typography separator ", ") as typography, typography_height')
			->from('#__reddesign_chars as chars')
			->where('chars.reddesign_font_id = ' . (int) $fontId)
			->where('chars.font_char IN ("' . implode('","', $char) . '")')
			->order('chars.reddesign_char_id ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects.
		$charArr = $db->loadObject();
		$maxHeight = $charArr->height;

		$width = 0;

		for ($i = 0;$i < count($char);$i++)
		{
			$query = $db->getQuery(true);
			$query
				->select('chars.width')
				->from('#__reddesign_chars as chars')
				->where('binary chars.font_char = ' . $db->quote($char[$i]))
				->where('chars.reddesign_font_id = "' . (int) $fontId . '" ');
			$db->setQuery($query);
			$width 		= $width + $db->loadResult();
		}

		$avgWidth = $width / count($char);

		if ($avgWidth == 0)
		{
			$avgWidth = $fontDetailArr->default_width;
		}

		if ($maxHeight == 0)
		{
			$maxHeight = $fontDetailArr->default_height;
		}

		$typoArr = explode(',', $charArr->typography);

		if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array(4, $typoArr))
		{
			$maxHeight = $maxHeight + $fontDetailArr->default_caps_height;
		}

		$perLineChar = explode("\n", $enteredChars);

		// @to-do take it from area table.....
		$maxCharsInSingleLine = max(array_map('strlen', $perLineChar));

		// @to-do take it from area table..
		$lineCount = count($perLineChar);

		$fontSize = $this->calculateFontSize($maxCharsInSingleLine, $avgWidth, $maxHeight, $lineCount, $canvasHeight, $canvasWidth);

		$offset = $this->getCharOffset($char, $fontId, $fontSize);

		$autoSizeData = array();
		$autoSizeData['fontSize'] = $fontSize;
		$autoSizeData['stringLines'] = $perLineChar;
		$autoSizeData['topoffset'] = $offset[0];
		$autoSizeData['maxHeight'] = $maxHeight;

		return $autoSizeData;
	}

	/**
	 *  Calculates Font size When Auto-size is on.
	 *
	 *  @param   int    $maxCharsInSingleLine  maxCharsInSingleLine.
	 *  @param   float  $fontW                 fontW.
	 *  @param   float  $fontH                 fontH.
	 *  @param   int    $lineCount             lineCount.
	 *  @param   int    $canvasHeight          canvasHeight.
	 *  @param   int    $canvasWidth           canvasWidth.
	 *
	 * @return int
	 *
	 * @access public
	 */
	public function calculateFontSize($maxCharsInSingleLine=3, $fontW=0.5366667, $fontH=0.77, $lineCount=1, $canvasHeight=504, $canvasWidth=324)
	{
		// To find the font size for particular one line, need to divide it by number of lines.
		$canvasEffectiveHeight = $canvasHeight / $lineCount;
		$fontSizeByHeightMax = $canvasEffectiveHeight / $fontH;
		$fontSizeByWidthMax = $canvasWidth / (($fontW) * $maxCharsInSingleLine);

		/*
		* Mid-Height (eg. char "a,s,u")
		* Cap-Height (eg. char "b,B,X")
		* Base-Height (eg. char "j,q,y")
		*/

		if ($fontSizeByWidthMax > $fontSizeByHeightMax)
		{
			$finalFontSize = $fontSizeByHeightMax;
			$finalFontSize = $finalFontSize / 1.1;
		}
		else
		{
			$finalFontSize = $fontSizeByWidthMax;
		}

		return $finalFontSize;
	}

	/**
	 *  Calculates Font size When Auto-size is on.
	 *
	 * @param   string  $canvaseDimensionBase       It can be width(w) or height(h).
	 * @param   int     $canvaseDimensionBaseInput  Dimension amount.
	 * @param   int     $fontId                     Font id.
	 * @param   string  $enteredChars               Entered char by user.
	 * @param   array   $fontDetailArr              Default height and width of fonts.
	 *
	 * @return Array
	 *
	 * @access public
	 */
	public function getCanvaseDimension($canvaseDimensionBase, $canvaseDimensionBaseInput, $fontId, $enteredChars, $fontDetailArr)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$char = str_split(mb_convert_encoding(urldecode($enteredChars), "ISO-8859-1", "UTF-8"));

		// Select character settings for a given font.
		$query
			->select('max(chars.height) as height, group_concat(typography separator ", ") as typography, typography_height')
			->from('#__reddesign_chars as chars')
			->where('chars.reddesign_font_id = ' . (int) $fontId)
			->where('chars.font_char IN ("' . implode('","', $char) . '")')
			->order('chars.reddesign_char_id ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects.
		$charArr = $db->loadObject();
		$maxHeight = $charArr->height;

		$width = 0;

		for ($i = 0;$i < count($char);$i++)
		{
			$query = $db->getQuery(true);
			$query
				->select('chars.width')
				->from('#__reddesign_chars as chars')
				->where('binary chars.font_char = ' . $db->quote($char[$i]))
				->where('chars.reddesign_font_id = "' . (int) $fontId . '" ');
			$db->setQuery($query);
			$width 		= $width + $db->loadResult();
		}

		if ($width == 0)
		{
			$width = $fontDetailArr->default_width;
		}

		if ($maxHeight == 0)
		{
			$maxHeight = $fontDetailArr->default_height;
		}

		$typoArr = explode(',', $charArr->typography);

		if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array(4, $typoArr))
		{
			$maxHeight = $maxHeight + $fontDetailArr->default_caps_height;
		}

		if ($canvaseDimensionBase == 'w')
		{
			$fontSize = $canvaseDimensionBaseInput / ($width * 1.1);
			$canvasHeight = $fontSize * $maxHeight;
			$canvasWidth = $canvaseDimensionBaseInput;
		}
		else
		{
			$fontSize = $canvaseDimensionBaseInput / $maxHeight;
			$canvasWidth = $fontSize * $width;
			$canvasHeight = $canvaseDimensionBaseInput;
		}

		$canvaseDimension = array();
		$canvaseDimension['canvasHeight'] = $canvasHeight;
		$canvaseDimension['canvasWidth'] = $canvasWidth;

		return $canvaseDimension;
	}

	/**
	 *  Calculates Font size When Auto-size is on.
	 *
	 * @param   array  $char      Char array entered by user.
	 * @param   int    $fontId    Font id.
	 * @param   int    $fontSize  This is font size of entered chars.
	 *
	 * @return Array
	 *
	 * @access public
	 */
	public function getCharOffset($char, $fontId, $fontSize)
	{
		$db = JFactory::getDBO();

		if (count($char))
		{
			$query = $db->getQuery(true);
			$query
				->select('fonts.default_height, fonts.default_caps_height, fonts.default_baseline_height')
				->from('#__reddesign_fonts as fonts')
				->where('fonts.reddesign_font_id = ' . (int) $fontId);

			$db->setQuery($query);
			$HeightArray = $db->loadObject();

			$query = $db->getQuery(true);
			$query
				->select('max(height) as height,  group_concat(typography separator ", ") as typography, typography_height')
				->from('#__reddesign_chars as chars')
				->where('chars.reddesign_font_id = ' . (int) $fontId)
				->where('binary chars.font_char IN ("' . implode('","', $char) . '")')
				->order('chars.reddesign_char_id ASC');

			$db->setQuery($query);
			$ResultArray = $db->loadObject();

			$typoArr = explode(',', $ResultArray->typography);

			if (empty($HeightArray))
			{
				return array( '+0', 0 );
			}

			$totalHeight = $HeightArray->default_height + $ResultArray->typography_height;
			$totalHeight = $totalHeight + $HeightArray->default_caps_height + $HeightArray->default_baseline_height;

			if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array('4', $typoArr))
			{
				$difference = $totalHeight - $ResultArray->height - $HeightArray->default_caps_height;
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}
			elseif (in_array('3', $typoArr) || in_array('4', $typoArr))
			{
				$difference = $totalHeight - ($ResultArray->height);
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}
			elseif (in_array('2', $typoArr))
			{
				$difference = $totalHeight - ( $ResultArray->height + $HeightArray->default_baseline_height);
				$offsetTop = (($difference * $fontSize) / 2);
				$offsetTop = "+" . $offsetTop;
			}
			else
			{
				$difference = $totalHeight - ( $ResultArray->height + $HeightArray->default_baseline_height);
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}

			$diff = $totalHeight - $ResultArray->height;

			if (!$ResultArray->height)
			{
				$offsetTop = '+0';
			}

			return array($offsetTop, $diff );
		}
		else
		{
			return array( '+0', 0 );
		}
	}
}
