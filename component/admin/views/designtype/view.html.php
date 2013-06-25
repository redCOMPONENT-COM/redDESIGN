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
 * Background View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewDesigntype extends FOFViewHtml
{
	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	protected function onAdd($tpl = null)
	{
		$this->input->setVar('hidemainmenu', true);

		$this->document = JFactory::getDocument();
		$model			= $this->getModel();
		$this->item		= $model->getItem();
		$this->areas = null;
		$this->productionBackground = null;

		if (!empty($this->item->reddesign_designtype_id))
		{
			$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();

			$areas = array();

			foreach ($this->backgrounds as $background)
			{
				if ($background->isPDFbgimage)
				{
					$this->productionBackground = $background;

					$areaModel = FOFModel::getTmpInstance('Area', 'ReddesignModel')->reddesign_background_id($background->reddesign_background_id);
					$areas = $areaModel->getItemList();
				}
			}

			$this->areas = $areas;
		}

		return true;
	}
}
