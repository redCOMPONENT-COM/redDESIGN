<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * The font edit controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class ReddesignControllerBackground extends RControllerForm
{
	/**
	 * Method for saving Background Form from DataType
	 *
	 * @return void
	 */
	public function backgroundSave()
	{
		$app = JFactory::getApplication();
		$backgroundModel = $this->getModel();

		$data = $app->input->get('jform', array(), 'array');

		if ($backgroundModel->saveBackground($data))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_STORE_SUCCESS'), 'message');
		}

		$this->setRedirect(base64_decode($data['returnurl']));
	}
}
