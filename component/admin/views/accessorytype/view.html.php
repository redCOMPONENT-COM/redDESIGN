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
 * Accessorytype View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewAccessorytype extends FOFViewHtml
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
		/* $this->activeTab 			= JFactory::getApplication()->input->getString('tab', 'general');
		$this->document 			= JFactory::getDocument();
		$this->areas 				= null;
		$this->productionBackground = null;*/
		$this->editor				= JFactory::getEditor();

		if (empty($this->item->reddesign_accessorytype_id))
		{
			$this->pageTitle = JText::_('COM_REDDESIGN_ACCESSORYTYPE_ADD_TITLE');
		}
		else
		{
			$this->pageTitle = JText::_('COM_REDDESIGN_ACCESSORYTYPE_EDIT_TITLE');
		}

		parent::display();
	}
}
