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

		if ($session->get('customizedImage') != "")
		{
			$mangledname = $session->get('customizedImage');
		}
		else
		{
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
		}

		$backgroundImageFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $backgroundImage;
		$newjpgFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';

		// Create Imagick object.
		$newImage = new Imagick;
		$newImage->readImage($backgroundImageFileLocation);

		// Add text areas to the background image.
		foreach ($design->areas as $area)
		{
			if (!empty($area->textArea))
			{
				// Create needed objects.
				$areaImage = new Imagick;
				$areaDraw  = new ImagickDraw;

				// Get font.
				if ($area->fontTypeId)
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
				}

				// Get area.
				$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($design->reddesign_background_id);
				$this->areaItem = $areaModel->getItem($area->id);

				// If we need autosize text than take different approach than solution for regular text.
				if (empty($area->fontSize))
				{
					$newAutoSizeData = $this->getFontSizeOnCharsBase($area->fontTypeId, $area->textArea, $fontType, $this->areaItem->height, $this->areaItem->width);
					$area->fontSize = $newAutoSizeData['fontSize'];
					$newAutoSizeData['reddesign_area_id'] = $this->areaItem->reddesign_area_id;
					$autoSizeData[] = $newAutoSizeData;
					$this->areaItem->textalign = 3;
				}

				// Create an area image.
				$areaImage->newImage($this->areaItem->width, $this->areaItem->height, new ImagickPixel('none'));

				// Set color and font.
				$areaDraw->setFillColor('#' . $area->fontColor);
				$areaDraw->setFont($fontTypeFileLocation);

				// End Auto size.
				$areaDraw->setFontSize($area->fontSize);

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
				$areaImage->annotateImage($areaDraw, 0, 0, 0, $area->textArea);

				// Add area image on top of background image.
				$newImage->compositeImage($areaImage, Imagick::COMPOSITE_DEFAULT, $this->areaItem->x1_pos, $this->areaItem->y1_pos);
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
	 * There is event trigger inside this function.
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function orderProduct()
	{
		JPluginHelper::importPlugin('reddesign');
		$dispatcher = JDispatcher::getInstance();
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Get design type data.
		$designTypeId    = $this->input->getInt('reddesign_designtype_id', null);
		$designTypeModel = FOFModel::getTmpInstance('Designtype', 'ReddesignModel')->reddesign_designtype_id($designTypeId);
		$designType      = $designTypeModel->getItem($designTypeId);

		$data = array();
		$data['designType'] = $designType;

		// Get Background Data
		$reddesign_background_id = $this->input->getInt('reddesign_background_id', null);
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($reddesign_background_id);
		$data['designBackground'] = $backgroundModel->getItem($reddesign_background_id);

		// Get designAreas
		$design = new JRegistry;
		$design->loadString($this->input->getString('designAreas', ''), 'JSON');
		$design = $design->get('Design');
		$data['designAreas'] = $design->areas;

		// Get desingAccessories
		$designAccessories = array();

		foreach ($design->accessories as $accessoryId)
		{
			$accessoryModel = FOFModel::getTmpInstance('Accessory', 'ReddesignModel');
			$accessory = $accessoryModel->getItem($accessoryId->id);
			$designAccessories[] = $accessory;
		}

		$data['designAccessories'] = $designAccessories;

		$autoSizeData = $this->input->getString('autoSizeData', '');
		$autoSizeData = json_decode($autoSizeData);
		$data['autoSizeData'] = $autoSizeData;

		$results = $dispatcher->trigger('onOrderButtonClick', array($data));

		if ($results[0])
		{
			$link = JRoute::_('index.php?option=com_redshop&view=cart', false);
			$app->Redirect($link);
		}

		$link = JRoute::_('index.php?option=com_reddesign&view=designtype&id=' . $designTypeId, false);
		$app->Redirect($link);
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
			->where('chars.reddesign_font_id = ' . $fontId . ' and chars.font_char in("' . implode('","', $char) . '")')
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
				->where('binary chars.font_char = "' . $char[$i] . '" AND chars.reddesign_font_id = "' . $fontId . '" ');
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

		$autoSizeData = array();
		$autoSizeData['fontSize'] = $fontSize;
		$autoSizeData['stringLines'] = $perLineChar;
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
		$FontSizeByHeightMax = $canvasEffectiveHeight / $fontH;
		$FontSizeByWidthMax = $canvasWidth / (($fontW) * $maxCharsInSingleLine);

		/*
		* Mid-Height (eg. char "a,s,u")
		* Cap-Height (eg. char "b,B,X")
		* Base-Height (eg. char "j,q,y")
		*/

		$finalFontSize = 0;

		if ($FontSizeByWidthMax > $FontSizeByHeightMax)
		{
			$finalFontSize = $FontSizeByHeightMax;
			$finalFontSize = $finalFontSize / 1.1;
		}
		else
		{
			$finalFontSize = $FontSizeByWidthMax;
			$finalFontSize = $finalFontSize / 1.2;
		}

		return $finalFontSize;
	}
}
