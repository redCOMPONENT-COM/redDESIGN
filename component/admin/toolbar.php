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
	 * Overrides function from the parent class to be able to reorder views for the toolbar.
	 * Automatically detects all views of the component.
	 *
	 * @return  array  A list of all views, in the order to be displayed in the toolbar submenu
	 */
	protected function getMyViews()
	{
		$views = parent::getMyViews();
		$newViewsOrder = array();

		if (!empty($views))
		{
			foreach ($views as $view)
			{
				switch ($view)
				{
					case 'designtypes':
						$newViewsOrder[0] = $view;
						break;
					case 'fonts':
						$newViewsOrder[1] = $view;
						break;
					case 'accessorytypes':
						$newViewsOrder[2] = $view;
						break;
					case 'accessories':
						$newViewsOrder[3] = $view;
						break;
					case 'orders':
						$newViewsOrder[4] = $view;
						break;
				}
			}

			ksort($newViewsOrder);
			$views = $newViewsOrder;
		}

		return $views;
	}

	/**
	 * Prepares the toolbar for Designtype view
	 *
	 * @return void
	 */
	public function onBrowse()
	{
		parent::onBrowse();

		$view = $this->input->getCmd('view', '');
		JToolBarHelper::title(JText::_('COM_REDDESIGN') . ' - ' . ucfirst($view),  $view);

		// Add Components options (see config.xml)
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_reddesign');
	}
}
