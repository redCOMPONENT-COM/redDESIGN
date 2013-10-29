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
	public $previewBackground;

	public $previewBackgrounds;

	public $imageSize;

	public $productionBackgroundAreas;

	/**
	 * Executes before rendering the page for the Read task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$this->params = JComponentHelper::getParams('com_reddesign');

		// Get Design
		$this->item = $model->getItem();

		// Get Design related elements
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
		$this->backgrounds = $backgroundModel->getItemList();

		foreach ($this->backgrounds as $background)
		{
			if ($background->isDefaultPreview)
			{
				$this->defaultPreviewBg = $background;
			}

			if ($background->isProductionBg)
			{
				$this->productionBackground = $background;
			}
		}

		$fontsModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel');
		$this->fonts = $fontsModel->getItemList(false, 'reddesign_font_id');

		if (empty($this->imageSize))
		{
			$this->imageSize = array(0, 0);
		}

		if (empty($this->defaultPreviewBg) || empty($this->productionBackground))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_DESIGNTYPE_NO_BACKGROUNDS'), 'notice');
		}
		else
		{
			$areasModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($this->productionBackground->reddesign_background_id);
			$this->productionBackgroundAreas = $areasModel->getItemList();
			$this->imageSize = getimagesize(FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->defaultPreviewBg->image_path);
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
