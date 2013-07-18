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
		$values = json_decode('{"Design":{"areas":[{"id":"1","textArea":"Mrs. Vipula","fontArea":"1","fontColor":"#000000","fontSize":"22"},{"id":"2","textArea":"Developer at redCOMPONENT","fontColor":"#000000","fontSize":"22"}],"background":"2","id":"1"}}');
		// var_dump($values) to see the ajax message structure to get the resulting image;

		// Dummy image is returned to frontend editor:
		$response['image']		   = JURI::base() . 'media/com_reddesign/assets/images/custom_background.png';

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
			$selectedAccessory = $this->input->getString('accessorytype' . $type->reddesign_accessorytype_id . [], '');

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
