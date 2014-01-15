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
class ReddesignControllerArea extends RControllerForm
{
	/**
	 * Saves design areas for AJAX request.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxSave()
	{
		$app = JFactory::getApplication();
		$response = array();

		$data = $this->input->post->get('jform', array(), 'array');

		$model = RModel::getAdminInstance('Area');

		if ($model->save($data))
		{
			$response['status']		= 1;
			$response['message']	= '<div class="alert alert-success">' . JText::sprintf('COM_REDDESIGN_DESIGNTYPE_AREA_SAVED', $data['name']) . '</div>';

			// Set message queue
			$app->enqueueMessage(JText::sprintf('COM_REDDESIGN_DESIGNTYPE_AREA_SAVED', $data['name']), 'message');
			$session = JFactory::getSession();
			$session->set('application.queue', $app->getMessageQueue());

			echo json_encode($response);
		}
		else
		{
			$response['status']		= 0;
			$response['message'] 	= '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_CANT_SAVE') . '</div>';

			echo json_encode($response);
		}

		$app->close();
	}

	/**
	 * Removes design areas for AJAX request.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxRemove()
	{
		$areaId = $this->input->getInt('id', 0);
		$response = array();

		$response['status'] = 0;
		$response['message'] = JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_ERROR_WHILE_REMOVING');

		if ($areaId)
		{
			$model = $this->getModel();

			if ($model->delete($areaId))
			{
				$response['status'] = 1;
				$response['message'] = JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_SUCCESSFULLY_REMOVED');
			}
		}

		echo json_encode($response);

		JFactory::getApplication()->close();
	}

	/**
	 * Gets areas for AJAX request.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxGetAreas()
	{
		$model = RModel::getAdminInstance('Areas', array('ignore_request' => true));
		$backgroundId = $this->input->getInt('reddesign_background_id');

		$model->setState('reddesign_background_id', $backgroundId);
		$items = $model->getItems();

		echo json_encode($items);
		JFactory::getApplication()->close();
	}

	/**
	 * Saves text color for an area.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxUpdateColors()
	{
		$areaId		= $this->input->getInt('id', null);
		$colorCodes = $this->input->getString('color_code', '');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$field = $db->quoteName('color_code') . ' = ' . $db->quote($colorCodes);
		$condition = $db->quoteName('id') . '=' . (int) $areaId;
		$query->update($db->quoteName('#__reddesign_areas'))->set($field)->where($condition);
		$db->setQuery($query);
		$db->query();
	}
}
