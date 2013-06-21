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
class ReddesignControllerChar extends FOFController
{
	/**
	 * Saves character specific data.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxSave()
	{
		$response = array();

		if ($this->applySave())
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
		}
	}

	/**
	 * Saves character specific data.
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
