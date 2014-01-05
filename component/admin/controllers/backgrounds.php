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
		$designId	= $this->input->getInt('reddesign_designtype_id', '');
		$bgId		= $this->input->getInt('reddesign_background_id', '');

		$model = $this->getThisModel();

		$app = JFactory::getApplication();

		if (!$model->setAsProductionFileBg($designId, $bgId))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_ERROR_SWITCHING_PDF_BG'), 'error');
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_PDF_BG_UPDATED'));
		}

		$this->setRedirect('index.php?option=com_reddesign&view=designtype&id=' . $designId . '&tab=backgrounds');
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
		$designId	= $this->input->getInt('reddesign_designtype_id', '');
		$bgId		= $this->input->getInt('reddesign_background_id', '');

		$model = $this->getThisModel();

		$app = JFactory::getApplication();

		if (!$model->setAsPreviewbg($designId, $bgId))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_ERROR_SWITCHING_PREVIEW_BG'), 'error');
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_PDF_PREVIEW_UPDATED'));
		}

		$this->setRedirect('index.php?option=com_reddesign&view=designtype&id=' . $designId . '&tab=backgrounds');
		$this->redirect();
	}

	/**
	 * Method for load Backgrounds List by AJAX
	 *
	 * @return array
	 */
	public function ajaxBackgrounds()
	{
		$designTypeId = $this->input->getInt('designtype_id', null);

		if ($designTypeId)
		{
			/** @var RedshopbModelUsers $usersModel */

			$view = $this->getView('Backgrounds', 'html');
			$model = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
			$view->setModel($model, true);

			$model->setState('filter.designtypeid', $designTypeId);

			$view->display();
		}

		JFactory::getApplication()->close();
	}
}
