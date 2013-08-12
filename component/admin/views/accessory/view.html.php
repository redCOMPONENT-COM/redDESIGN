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
 * Accessory View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewAccessory extends FOFViewHtml
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

		$model                = $this->getModel();
		$this->item           = $model->getItem();
		$this->editor         = JFactory::getEditor();
		$this->accessorytypes = null;

		if (empty($this->item->reddesign_accessory_id))
		{
			$this->pageTitle = JText::_('COM_REDDESIGN_ACCESSORY_ADD_TITLE');
		}
		else
		{
			$this->pageTitle = JText::_('COM_REDDESIGN_ACCESSORY_EDIT_TITLE');
		}

		$accessorytypesModel = FOFModel::getTmpInstance('Accessorytypes', 'ReddesignModel');
		$accessorytypes = $accessorytypesModel->getItemList();
		$accessorytypesOptions = array();

		foreach ($accessorytypes as $type)
		{
			$accessorytypesOptions[] = JHtml::_('select.option', $type->reddesign_accessorytype_id, $type->title);
		}

		$this->accessorytypes = JHtml::_(
			'select.genericlist',
			$accessorytypesOptions,
			'reddesign_accessorytype_id',
			'',
			'value',
			'text',
			$this->item->reddesign_accessorytype_id
		);

		// Check to ensure that the e-commerce and redDESING have same currency (symbol)
		$dispatcher	= JDispatcher::getInstance();

		JPluginHelper::importPlugin('reddesign');
		$result = $dispatcher->trigger('onDesigntypeDisplayCheckCurrency');

		if ($result[0]['wrongCurrency'])
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDDESIGN_CURRENCY_NOT_SET', $result[0]['reddesign_currency_symbol'], $result[0]['ecommerce_currency_symbol']), 'notice');
		}


		parent::display();
	}
}
