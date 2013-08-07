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
		// Initialize session
		$session 			= JFactory::getSession();

		// Get design Data
		$design = new JRegistry;
		$design->loadString($this->input->getString('designarea', ''), 'JSON');
		$design = $design->get('Design');

		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($design->reddesign_designtype_id);
		$this->background = $backgroundModel->getItem($design->reddesign_background_id);
		$backgroundImage = $this->background->image_path;

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
			// Create needed objects.
			$areaImage = new Imagick;
			$areaDraw  = new ImagickDraw;

			// Get font.
			if ($area->fontTypeId)
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
				$this->fontType = $fontModel->getItem($area->fontTypeId);
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $this->fontType->font_file;
			}
			else
			{
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			// Get area.
			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($design->reddesign_background_id);
			$this->areaItem = $areaModel->getItem($area->id);

			// Create an area image.
			$areaImage->newImage($this->areaItem->width, $this->areaItem->height, new ImagickPixel('none'));

			// Set color and font.
			$areaDraw->setFillColor('#' . $area->fontColor);
			$areaDraw->setFont($fontTypeFileLocation);

			// Set font size
			if (!empty($area->fontSize))
			{
				$areaDraw->setFontSize($area->fontSize);
			}
			else
			{
				/*
				 * Font size is height of the em-square. Being a square for the em-square that means that it has same width as height.
				 * So, we have to calculate width of text's em squares. We just get length of the string/count of how many characters
				 * string have and divide that on the area width and that is new font size.
				 */
				$stringLength = strlen($area->textArea);
				$emSquareFontSize = $this->areaItem->width;

				// Don't divide by zero.
				if ($stringLength != 0)
				{
					$emSquareFontSize = $this->areaItem->width / $stringLength;
				}

				// Consider height. Font size should not be bigger than height.
				if ($emSquareFontSize > $this->areaItem->height)
				{
					$emSquareFontSize = $this->areaItem->height;
				}

				$area->fontSize = $emSquareFontSize;
				$areaDraw->setFontSize($area->fontSize);
			}

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
		}

		// Free resources.
		$areaImage->clear();
		$areaImage->destroy();
		$newImage->clear();
		$newImage->destroy();

		// Create session to store Image
		$session->set('customizedImage', $mangledname);
		$response['image']		   = JURI::base() . 'media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';

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
		$app    = JFactory::getApplication();
		JPluginHelper::importPlugin('reddesign');
		$dispatcher = JDispatcher::getInstance();

		// Get design type data.
		$designTypeId    = $this->input->getInt('reddesign_designtype_id', null);
		$designTypeModel = FOFModel::getTmpInstance('Designtype', 'ReddesignModel')->reddesign_designtype_id($designTypeId);
		$designType      = $designTypeModel->getItem($designTypeId);

		$data = array();
		$data['designType'] = $designType;

		// Get Background Data
		$reddesign_background_id = $this->input->getInt('reddesign_background_id', null);
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($reddesign_background_id);
		$this->background = $backgroundModel->getItem($reddesign_background_id);
		$data['designBackground'] = $this->background;

		// Get designAreas
		$design = new JRegistry;
		$design->loadString($this->input->getString('designAreas', ''), 'JSON');
		$design = $design->get('Design');
		$data['desingAreas'] = $design->areas;

		// Get desingAccessories
		$desingAccessories = array();
		foreach ($design->accessories as $accessoryId)
		{
			$accessoryModel = FOFModel::getTmpInstance('Accessory', 'ReddesignModel');
			$accessory = $accessoryModel->getItem($accessoryId->id);
			$desingAccessories[] = $accessory;
		}
		$data['desingAccessories'] = $desingAccessories;

		$results = $dispatcher->trigger('onOrderButtonClick', array($data));

		if ($results)
		{
			$link = JRoute::_('index.php?option=com_redshop&view=cart', false);
			$app->Redirect($link);
		}
	}
}
