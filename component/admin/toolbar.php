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
 * RedDesign Toolbar
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator.Dispatcher
 *
 * @since       1.0
 */
class ReddesignToolbar extends FOFToolbar
{
	/**
	 * Prepares the toolbar for Designtype view
	 *
	 * @return void
	 */
	public function onBrowse()
	{
		parent::onBrowse();

		$view = $this->input->getCmd('view', 'cpanel');
		JToolBarHelper::title(JText::_('COM_REDDESIGN') . ' - ' . ucfirst($view),  $view);

		// Add Components options (see config.xml)
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_reddesign');
	}

	/**
	 * Prepares the toolbar for Designtype view
	 *
	 * @return void
	 */
	public function onEdit()
	{
		parent::onEdit();

		$view = $this->input->getCmd('view', 'cpanel');
		JToolBarHelper::title(JText::_('COM_REDDESIGN') . ' - ' . ucfirst($view), $view);

		// Add Components options (see config.xml)
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_reddesign');
	}

	/**
	 * Prepares the toolbar for Designtype view
	 *
	 * @return void
	 */
	public function onAdd()
	{
		parent::onAdd();

		$view = $this->input->getCmd('view', 'cpanel');
		JToolBarHelper::title(JText::_('COM_REDDESIGN') . ' - ' . ucfirst($view), $view);

		// Add Components options (see config.xml)
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_reddesign');
	}
}
