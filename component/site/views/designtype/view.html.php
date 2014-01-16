<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Designtype View
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignViewDesigntype extends JView
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
		$designTypeId = JFactory::getApplication()->input->getInt('id', null);
		$this->item = $model->getItem($designTypeId);

		// Get related design types. They are related through redSHOP product (multiple design types assigned per redSHOP product);
		$this->relatedDesignTypes = $this->config['input']->getString('relatedDesignTypes', '');
		$this->relatedDesignTypes = explode(',', $this->relatedDesignTypes);

		// Get Design related elements
		$backgroundsModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
		$backgroundsModel->setState('reddesign_designtype_id', $this->item->id);
		$this->backgrounds = $backgroundsModel->getItems();

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

		$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true));
		$this->fonts = $fontsModel->getItems();

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
			$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true));
			$areasModel->setState('background_id', $this->productionBackground->background_id);
			$this->productionBackgroundAreas = $areasModel->getItems();
			$this->imageSize = getimagesize(JURI::root() . 'media/com_reddesign/assets/backgrounds/' . $this->defaultPreviewBg->image_path);
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
