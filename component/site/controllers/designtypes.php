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

			// If we need autosize text than take different approach than solution for regular text.
			if (!empty($area->fontSize))
			{
				// Create an area image.
				$areaImage->newImage($this->areaItem->width, $this->areaItem->height, new ImagickPixel('none'));

				// Set color and font.
				$areaDraw->setFillColor('#' . $area->fontColor);
				$areaDraw->setFont($fontTypeFileLocation);
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
			}
			else
			{
				// Creating auto-sized text.
				$areaImage->setBackgroundColor(new ImagickPixel('none'));
				$areaImage->setFont($fontTypeFileLocation);
				$areaImage->setGravity(Imagick::GRAVITY_CENTER);
				$areaImage->newPseudoImage($this->areaItem->width, $this->areaItem->height, "caption:" . $area->textArea);
				$areaImage->colorizeImage('#' . $area->fontColor, 0.0);
			}

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
		$response['image'] = JURI::base() . 'media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';

		$response['imageTitle'] = $this->background->title;

		$imageSize = getimagesize(JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg');
		$response['imageWidth'] = $imageSize[0];
		$response['imageHeight'] = $imageSize[1];

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
		$data['designAreas'] = $design->areas;

		// Get designAccessories
		$designAccessories = array();

		foreach ($design->accessories as $accessoryId)
		{
			$accessoryModel = FOFModel::getTmpInstance('Accessory', 'ReddesignModel');
			$accessory = $accessoryModel->getItem($accessoryId->id);
			$designAccessories[] = $accessory;
		}

		$data['designAccessories'] = $designAccessories;

		$results = $dispatcher->trigger('onOrderButtonClick', array($data));

		if ($results[0])
		{
			$link = JRoute::_('index.php?option=com_redshop&view=cart', false);
			$app->Redirect($link);
		}

		$link = JRoute::_('index.php?option=com_reddesign&view=designtype&id=' . $designTypeId, false);
		$app->Redirect($link);
	}
}
