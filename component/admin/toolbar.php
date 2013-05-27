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
	 * Renders the submenu (toolbar links) for all detected views of this component
	 */
	protected function renderSubmenu()
	{
		$views = $this->getMyViews();
		if (empty($views))
			return;

		$activeView = $this->input->getCmd('view', 'cpanel');

		$top_level_views = array('cpanels', 'designs', 'fonts');

		foreach ($views as $view)
		{
			if(in_array($view,$top_level_views))
			{
				// Get the view name
				$key = strtoupper($this->component) . '_TITLE_' . strtoupper($view);
				if (strtoupper(JText::_($key)) == $key)
				{
					$altview = FOFInflector::isPlural($view) ? FOFInflector::singularize($view) : FOFInflector::pluralize($view);
					$key2 = strtoupper($this->component) . '_TITLE_' . strtoupper($altview);
					if (strtoupper(JText::_($key2)) == $key2)
					{
						$name = ucfirst($view);
					}
					else
					{
						$name = JText::_($key2);
					}
				}
				else
				{
					$name = JText::_($key);
				}

				$link = 'index.php?option=' . $this->component . '&view=' . $view;

				$active = $view == $activeView;

				$this->appendLink($name, $link, $active);
			}
		}
	}

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
