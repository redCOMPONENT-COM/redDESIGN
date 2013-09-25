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
		$view = $this->input->getCmd('view', '');
		JToolBarHelper::title(JText::_('COM_REDDESIGN') . ' - ' . ucfirst($view),  $view);

		// On frontend, buttons must be added specifically

		if (FOFPlatform::getInstance()->isBackend() || $this->renderFrontendSubmenu)
		{
			$this->renderSubmenu();
		}

		if (!FOFPlatform::getInstance()->isBackend() && !$this->renderFrontendButtons)
		{
			return;
		}

		parent::onBrowse();

		// Set toolbar title
		$option = $this->input->getCmd('option', 'com_reddesign');
		$subtitle_key = strtoupper($option . '_TITLE_' . $this->input->getCmd('view', 'cpanel'));
		JToolBarHelper::title(JText::_(strtoupper($option)) . ' &ndash; <small>' . JText::_($subtitle_key) . '</small>', str_replace('com_', '', $option));

		// Add Components options (see config.xml)
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_reddesign');
	}
}
