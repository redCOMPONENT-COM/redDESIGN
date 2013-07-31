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
		// Initialize session
		$session 			= JFactory::getSession();

		// Get design Data
		$design = new JRegistry;
		$design->loadString($this->input->getString('designarea'), 'JSON');
		$design = $design->get('Design');

		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($design->reddesign_desingtype_id);
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

		$backgroundImage_file_location = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $backgroundImage;
		$newjpg_file_location = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $mangledname . '.jpg';

		foreach ($design->areas as $area)
		{
			if ($area->fontTypeId)
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
				$this->fontType = $fontModel->getItem($area->fontTypeId);
				$fontType_file_location = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $this->fontType->font_file;
			}
			else
			{
				$fontType_file_location = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($design->reddesign_background_id);
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

			$line_gap = 0;

	         $cmd = "convert $backgroundImage_file_location  \
		     		\( $gravity -font $fontType_file_location -pointsize $area->fontSize -interline-spacing -$line_gap -fill '#$area->fontColor'  -background transparent label:'$area->textArea' \ -virtual-pixel transparent \
					\) $gravity -geometry +$offsetLeft+$offsetTop -composite   \
		      		$newjpg_file_location";
			exec($cmd);
			$backgroundImage_file_location = $newjpg_file_location;
		}

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
		JPluginHelper::importPlugin('reddesign');
		$dispatcher = JDispatcher::getInstance();

		// Get design type data.
		$designTypeId    = $this->input->getInt('reddesign_designtype_id', null);
		$designTypeModel = FOFModel::getTmpInstance('Designtype', 'ReddesignModel')->reddesign_designtype_id($designTypeId);
		$designType      = $designTypeModel->getItem();

		$data = array();
		$data['reddesign_designtype_id'] = $designTypeId;

		// @Todo: Here form other data and send it to the plugin via dispatcher.
		// Get reddesign_background_id
		$reddesign_background_id = $this->input->getInt('reddesign_background_id');
		$data['reddesign_background_id'] = $reddesign_background_id;

		// Get designAreas
		$design = new JRegistry;
		$design->loadString($this->input->getString('designAreas'), 'JSON');
		$design = $design->get('Design');

		$data['desingAreas'] = $design->areas;
		$data['desingAccessories'] = $design->accessories;

		$results = $dispatcher->trigger('onOrderButtonClick', $data);
	}
}
