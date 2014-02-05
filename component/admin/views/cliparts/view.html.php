<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Cliparts List View
 *
 * @package     Reddesign.Backend
 * @subpackage  View
 * @since       2.0
 */
class ReddesignViewCliparts extends ReddesignView
{
	/**
	 * Do we have to display a sidebar ?
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('Form');
		$this->activeFilters = $model->getActiveFilters();

		$this->ordering = array();

		foreach ($this->items as &$item)
		{
			$this->ordering[0][] = $item->id;
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   2.0
	 */
	public function getTitle()
	{
		return JText::_('COM_REDDESIGN_CLIPART_LIST');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = JFactory::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;
		$fourthGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.admin', 'com_reddesign.panel'))
		{
			$new = RToolbarBuilder::createNewButton('clipart.add');
			$secondGroup->addButton($new);

			$edit = RToolbarBuilder::createEditButton('clipart.edit');
			$secondGroup->addButton($edit);

			$delete = RToolbarBuilder::createDeleteButton('cliparts.delete');
			$thirdGroup->addButton($delete);

			$categories = RToolbarBuilder::createStandardButton(
				'cliparts.categories',
				JText::_('COM_REDDESIGN_CLIPART_CATEGORIES_LABEL'),
				'',
				'icon-sitemap',
				false
			);

			$fourthGroup->addButton($categories);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup)
			->addGroup($thirdGroup)
			->addGroup($fourthGroup);

		return $toolbar;
	}
}
