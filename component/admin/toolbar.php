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
	 * Prepares the toolbar for Cpanel view
	 *
	 * @return void
	 */
	public function onCpanelsBrowse()
	{
		// Set the toolbar title
		JToolBarHelper::title(JText::_('COM_REDDESIGN_CPANEL_TITLE_DASHBOARD'), 'reddesign');

		// Add Components options (see config.xml)
		JToolBarHelper::preferences('com_reddesign');
	}
}
