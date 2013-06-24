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

		if (!empty($this->item->reddesign_designtype_id))
		{
			$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();

			$options = array();

			$options[] = JHTML::_('select.option', 0, JText::_('COM_REDDESIGN_COMMON_SELECT'));

			foreach ($this->backgrounds as $background)
			{
				$options[] = JHTML::_('select.option', $background->reddesign_background_id, $background->title);
			}

			$this->backgroundsDropDownOptions = $options;
		}

		return true;
	}
}
