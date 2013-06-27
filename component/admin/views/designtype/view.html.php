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
	public function display($tpl = null)
	{
		$this->input->setVar('hidemainmenu', true);

		$model 						= $this->getModel();
		$this->item 				= $model->getItem();
		$this->activeTab 			= JFactory::getApplication()->input->getString('tab', 'general');
		$this->document 			= JFactory::getDocument();
		$this->areas 				= null;
		$this->productionBackground = null;

		// If it's not a new design
		if (!empty($this->item->reddesign_designtype_id))
		{
			// Get all the backgrounds that belongs to this Designtype item
			$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();

			$areas = array();

			foreach ($this->backgrounds as $background)
			{
				// Get the background image that has been selected to be the Production PDF file image
				if ($background->isPDFbgimage)
				{
					$this->productionBackground = $background;

					// Get all areas existing in the database for this specific background
					$areaModel = FOFModel::getTmpInstance('Area', 'ReddesignModel')->reddesign_background_id($background->reddesign_background_id);
					$areas = $areaModel->getItemList();
				}
			}

			$this->areas = $areas;
		}

		parent::display();
	}
}
