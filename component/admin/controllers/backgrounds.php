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
class ReddesignControllerBackgrounds extends FOFController
{
	/**
	 * Constructor to set the right model
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'backgrounds';
	}

	/**
	 * Method to set the a backgrond as the PDF background.
	 *
	 * @since	1.0
	 *
	 * @return void
	 */
	public function setPDFbg()
	{
		$designId = $this->input->getInt('backgrounds_reddesign_designtype_id', '');
		$bgId = $this->input->getInt('backgrounds_reddesign_background_id', '');

		$model = $this->getThisModel();

		$app = JFactory::getApplication();

		if (!$model->setAsPDFbg($designId, $bgId))
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
}
