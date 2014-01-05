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
 * Background Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerBackgrounds extends RControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 */
	public function __construct()
	{
		parent::__construct();

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}

	/**
	 * Method to set  a background as the PDF background.
	 *
	 * @since	1.0
	 *
	 * @return void
	 */
	public function setProductionFileBg()
	{
		$app = JFactory::getApplication();
		$model = RModel::getAdminInstance('Background', array('ignore_request' => true));

		$designId	= $this->input->get('reddesign_designtype_id', 0);
		$bgIds		= $this->input->get('cid', null, 'array');
		$bgId 		= 0;
		$return 	= $this->input->get('return', null, 'base64');

		if (bgIds)
		{
			$bgId = $bgIds[0];
		}

		if ((designId) && ($bgId))
		{
			if (!$model->setAsProductionFileBg($designId, $bgId))
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_ERROR_SWITCHING_PDF_BG'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_PDF_BG_UPDATED'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->designId . '&tab=backgrounds');
		}

		$this->redirect();
	}

	/**
	 * Method to set a background as the Preview default background.
	 *
	 * @since	1.0
	 *
	 * @return void
	 */
	public function setPreviewBg()
	{
		$app = JFactory::getApplication();
		$model = RModel::getAdminInstance('Background', array('ignore_request' => true));

		$designId	= $this->input->get('reddesign_designtype_id', 0);
		$bgIds		= $this->input->get('cid', null, 'array');
		$bgId 		= 0;
		$return 	= $this->input->get('return', null, 'base64');

		if (bgIds)
		{
			$bgId = $bgIds[0];
		}

		if ((designId) && ($bgId))
		{
			if (!$model->setAsPreviewbg($designId, $bgId))
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_ERROR_SWITCHING_PREVIEW_BG'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_PDF_PREVIEW_UPDATED'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->designId . '&tab=backgrounds');
		}

		$this->redirect();
	}
}
