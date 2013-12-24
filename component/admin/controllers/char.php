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
class ReddesignControllerChar extends RControllerForm
{
	/**
	 * Saves character specific data.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxSaveChar()
	{
		$data = array();
		$data['id']                = $this->input->getInt('id', null);
		$data['font_char']         = $this->input->getString('font_char', '');
		$data['width']             = $this->input->getFloat('width', null);
		$data['height']            = $this->input->getFloat('height', null);
		$data['typography']        = $this->input->getInt('typography', null);
		$data['typography_height'] = $this->input->getFloat('typography_height', null);
		$data['font_id']           = $this->input->getInt('font_id', null);

		$charModel = RModel::getAdminInstance('Char', array('ignore_request' => true));
		$charItem = $charModel->getItem($data['id']);

		/*if ($this->apply())
		{
			$response['message'] = JText::sprintf('COM_REDDESIGN_FONT_CHAR_SUCCESSFULLY_SAVED_CHAR', $this->input->getString('font_char', ''));
			$response['reddesign_char_id'] = $this->input->getInt('id', null);
			$response['font_char'] = $this->input->getString('font_char', null);
			$response['width'] = $this->input->getFloat('width', null);
			$response['height'] = $this->input->getFloat('height', null);
			$response['typography'] = $this->input->getInt('typography', null);
			$response['typography_height'] = $this->input->getFloat('typography_height', null);

			echo json_encode($response);
		}
		else
		{
			echo JText::_('COM_REDDESIGN_FONT_CHAR_CANT_SAVE_CHAR');
		}*/

		echo json_encode($data);

		JFactory::getApplication()->close();
	}

	/**
	 * Deletes a specific character from the font view.
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
			echo JText::_('COM_REDDESIGN_FONT_CHAR_SUCCESSFULLY_REMOVED');
		}
		else
		{
			echo JText::_('COM_REDDESIGN_FONT_CHAR_ERROR_WHILE_REMOVING');
		}
	}
}
