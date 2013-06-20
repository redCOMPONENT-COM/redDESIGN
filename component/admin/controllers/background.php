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
class ReddesignControllerBackground extends FOFController
{
	/**
	 * Stores a background via AJAX
	 *
	 * @return void
	 */
	public function ajaxSave()
	{
		$response = array();

		if ($this->applySave())
		{
			$response['message'] = JText::sprintf('COM_REDDESIGN_FONT_CHAR_SUCCESSFULLY_SAVED_CHAR', $this->input->getString('font_char', ''));
			$response['reddesign_designtype_id'] = $this->input->getInt('reddesign_designtype_id', null);
			$response['title'] = $this->input->getString('title', null);

			echo json_encode($response);
		}
		else
		{
			echo JText::_('COM_REDDESIGN_BACKGROUND_CANT_SAVE_BACKGROUND');
		}
	}
}
