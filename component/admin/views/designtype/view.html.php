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
		JRequest::setVar('hidemainmenu', true);

		$model 				= $this->getModel();
		$this->item 		= $model->getItem();
		$this->activeTab 	= JFactory::getApplication()->input->getString('tab', 'general');

		if (!empty($this->item->reddesign_designtype_id))
		{
			$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')
				->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();
		}

		return true;
	}
}
