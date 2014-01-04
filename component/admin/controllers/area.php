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
		$input = $app->input;

		$response = array();

		$fontIds = $input->getString('font_id', '');

		if (!empty($fontIds))
		{
			$fontIds = implode(',', $fontIds);
			$input->set('font_id', $fontIds);
		}

		$data = $input->post->get('jform', array(), 'array');

		echo json_encode($data);
		$app->close();

		$model = RModel::getAdminInstance('Area');

		if ($model->ajaxSave($data))
		{
			$response['reddesign_area_id'] 	= $data['id'];
			$response['title']			   	= $data['name'];
			$response['x1_pos']		   		= $data['x1_pos'];
			$response['y1_pos']		   		= $data['y1_pos'];
			$response['x2_pos']		   		= $data['x2_pos'];
			$response['y2_pos']		   		= $data['y2_pos'];
			$response['width']			   	= $data['width'];
			$response['height']			   	= $data['height'];
			$response['message']		   	= JText::sprintf('COM_REDDESIGN_DESIGNTYPE_AREA_SAVED', $data['name']);

			echo json_encode($response);
		}
		else
		{
			echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_CANT_SAVE');
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
		$model = $this->getThisModel();
		$model->setIDsFromRequest();

		if ($model->delete())
		{
			echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_SUCCESSFULLY_REMOVED');
		}
		else
		{
			echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_ERROR_WHILE_REMOVING');
		}
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
		$model = $this->getThisModel();
		$model->setIDsFromRequest();
		$items = $model->getList();

		echo json_encode($items);
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
		$areaId		= $this->input->getInt('reddesign_area_id', null);
		$colorCodes = $this->input->getString('color_code', '');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$field = $db->quoteName('color_code') . '=\'' . $colorCodes . '\'';
		$condition = $db->quoteName('reddesign_area_id') . '=' . $areaId;
		$query->update($db->quoteName('#__reddesign_areas'))->set($field)->where($condition);
		$db->setQuery($query);
		$db->query();
	}
}
