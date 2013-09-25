<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Designtype View
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignViewDesigntype extends FOFViewHtml
{
	/**
	 * Executes before rendering the page for the Read task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$this->params = JComponentHelper::getParams('com_reddesign');

		// Get Design
		$this->item = $model->getItem();

		// Get Design related elements
		$this->backgrounds			= $model->getBackgrounds();
		$this->previewBackground	= $model->getPreviewBackground();
		$this->previewBackgrounds	= $model->getPreviewBackgrounds();
		$this->productionBackground = $model->getProductionBackground();
		$this->fonts				= $model->getFonts();

		if (empty($this->imageSize))
		{
			$this->imageSize = array(0, 0);
		}

		if (empty($this->previewBackground) || empty($this->productionBackground))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_DESIGNTYPE_NO_BACKGROUNDS'), 'notice');
		}
		else
		{
			$this->productionBackgroundAreas = $model->getProductionBackgroundAreas($this->productionBackground->reddesign_background_id);
			$this->imageSize = getimagesize(FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->previewBackground->image_path);
		}

		if (empty($this->productionBackgroundAreas))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_DESIGNTYPE_NO_DESIGN_AREAS'), 'notice');
		}
		else
		{
			parent::display($tpl);
		}
	}
}
