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
class ReddesignControllerArea extends FOFController
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
		$response = array();

		$fontIds = $this->input->getString('font_id', '');

		if (!empty($fontIds))
		{
			$fontIds = implode(',', $fontIds);
			$this->input->set('font_id', $fontIds);
		}

		if ($this->applySave())
		{
			$response['reddesign_area_id'] = $this->input->getInt('id', null);
			$response['title']			   = $this->input->getString('title', '');
			$response['x1_pos']		   = $this->input->getInt('x1_pos', null);
			$response['y1_pos']		   = $this->input->getInt('y1_pos', null);
			$response['x2_pos']		   = $this->input->getInt('x2_pos', null);
			$response['y2_pos']		   = $this->input->getInt('y2_pos', null);
			$response['width']			   = $this->input->getInt('width', null);
			$response['height']			   = $this->input->getInt('height', null);
			$response['message']		   = JText::sprintf('COM_REDDESIGN_DESIGNTYPE_AREA_SAVED', $this->input->getString('title', ''));

			echo json_encode($response);
		}
		else
		{
			echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREA_CANT_SAVE');
		}
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
		$items = $model->getList();

		echo json_encode($items);
	}
}
