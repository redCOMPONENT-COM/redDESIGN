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
class ReddesignControllerDesigntype extends FOFController
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

		$this->modelName = 'Designtype';
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
		// @Todo: the ramSign engine for generating images needs to be ported here. Now just a dummy image is returned to frontend editor
		// Here we will import all the requested values using JInput
		// but right now I'm creating a dummy array that contains all values for generating a image
		$values = json_decode('{"Design":{"areas":[{"id":"1","textArea":"Mrs. Vipula","fontArea":"1","fontColor":"#000000","fontSize":"22","fontTypeId":"1"},{"id":"2","textArea":"Developer at redCOMPONENT","fontColor":"#000000","fontSize":"22","fontTypeId":"1"}],"backgroundId":"2","id":"1"}}');
		// var_dump($values) to see the ajax message structure to get the resulting image;

		foreach ($values as $value)
		{
			$design_id = $value->id;
			$areas = $value->areas;

			$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($value->id);
			$this->background = $backgroundModel->getItem($value->backgroundId);
			$backgroundImage = $this->background->image_path;

			// Get a (very!) randomised name
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

			$backgroundImage_file_location = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $backgroundImage;
			$newjpg_file_location = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/customized/' . $mangledname . '.jpg';

			foreach ($areas as $area)
			{
				$fontSize = $area->fontSize;
				$fontColor = $area->fontColor;
				$fontTypeId = $area->fontTypeId;
				$fontText = $area->textArea;

				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
				$this->fontType = $fontModel->getItem($fontTypeId);

				$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($value->backgroundId);
				$this->areaItem = $areaModel->getItem($area->id);
				/*
					Text alingment condition
					1 is left
					2 is right
					3 is center
				 */
				if ((int) $this->areaItem->textalign == 1)
				{
					$gravity = '-gravity NorthWest';
					$offsetTop = $this->areaItem->y1_pos;
					$offsetLeft = $this->areaItem->x1_pos;
				}
				elseif ((int) $this->areaItem->textalign == 2)
				{
					$resource 	= new Imagick($backgroundImage_file_location);
					$imagewidth = $resource->getImageWidth();
					$gravity = '-gravity NorthEast';
					$offsetTop = $this->areaItem->y1_pos;
					$offsetLeft = $imagewidth - $this->areaItem->x2_pos;
				}
				else
				{
					$gravity = ' ';
					$offsetTop = $this->areaItem->y1_pos;
					$offsetLeft = $this->areaItem->x1_pos + ($this->areaItem->width / 4);
				}

				$fontType_file_location = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $this->fontType->font_file;
				$line_gap = 0;

			    $cmd = "convert $backgroundImage_file_location  \
			     		\( $gravity -font $fontType_file_location -pointsize $fontSize -interline-spacing -$line_gap -fill '$fontColor'  -background transparent label:'$fontText' \ -virtual-pixel transparent \
						\) $gravity -geometry +$offsetLeft+$offsetTop -composite   \
			      		$newjpg_file_location";
				exec($cmd);

				$backgroundImage_file_location = $newjpg_file_location;
			}
		}

		// Dummy image is returned to frontend editor:
		$response['image']		   = JURI::base() . 'media/com_reddesign/assets/backgrounds/customized/' . $mangledname . '.jpg';

		echo json_encode($response);
	}

	/**
	 * There is event triger inside this function.
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function orderProduct()
	{
		JPluginHelper::importPlugin('reddesign');
		$dispatcher = JDispatcher::getInstance();

		// Get design type data.
		$designTypeId    = $this->input->getInt('reddesign_designtype_id', null);
		$designTypeModel = FOFModel::getTmpInstance('Designtype', 'ReddesignModel')->reddesign_designtype_id($designTypeId);
		$designType      = $designTypeModel->getItem();

		$data = array();
		$data['reddesign_designtype_id'] = $designTypeId;

		// @Todo: Here form other data and send it to the plugin via dispatcher.

		// Get accessory data.
		$accessoryTypes = $designTypeModel->getAccessories();
		$selectedAccessories = array();

		foreach ($accessoryTypes as $type)
		{
			$selectedAccessory = $this->input->getString('accessorytype' . $type->reddesign_accessorytype_id . '[]', '');

			if (!empty($selectedAccessory))
			{
				$selectedAccessories[] = $selectedAccessory;
			}
		}

		if (!empty($selectedAccessories))
		{
			$data['selectedAccessories'] = $selectedAccessories;
		}

		$results = $dispatcher->trigger('onOrderButtonClick', $data);
	}
}
