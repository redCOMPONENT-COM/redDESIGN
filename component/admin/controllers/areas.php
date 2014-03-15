<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Areas Controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class ReddesignControllerAreas extends RControllerAdmin
{
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');

		// Save the ordering
		$return = $model->saveOrder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Moves an area up in the order on arrow Up click.
	 *
	 * @return void
	 */
	public function orderUpAjax()
	{
		// Get the input
		$areaId        = $this->input->post->get('areaId', array(), 'int');
		$previousOrder = $this->input->post->get('previousOrder', array(), 'int');
		$pks           = $this->input->post->get('cid', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');

		// Save the ordering
		$return = $model->orderUp($areaId, $previousOrder, $pks);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Moves an area down in the order on arrow down click.
	 *
	 * @return void
	 */
	public function orderDownAjax()
	{
		// Get the input
		$areaId        = $this->input->post->get('areaId', array(), 'int');
		$previousOrder = $this->input->post->get('previousOrder', array(), 'int');
		$pks           = $this->input->post->get('cid', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');

		// Save the ordering
		$return = $model->orderDown($areaId, $previousOrder, $pks);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
