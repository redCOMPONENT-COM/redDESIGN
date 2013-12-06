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
 * Design Types List View
 *
 * @package     Reddesign.Backend
 * @subpackage  View
 * @since       2.0
 */
class ReddesignViewDesigntypes extends ReddesignView
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
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

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
		return JText::_('COM_REDDESIGN_DESIGNTYPES_LIST');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo        The commented lines are going to be implemented once we have setup ACL requirements for redDESIGN
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		// $canDo = ReddesignHelper::getActions($this->state->get('filter.category_id'));
		$user = JFactory::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.admin', 'com_reddesign.panel'))
		{
			// Add / edit
			// if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_reddesign', 'core.create'))) > 0)
			// {
			$new = RToolbarBuilder::createNewButton('designtype.add');
			$secondGroup->addButton($new);
			// }

			// if (($canDo->get('core.edit')))
			// {
			$edit = RToolbarBuilder::createEditButton('designtype.edit');
			$secondGroup->addButton($edit);
			// }

			// Delete / Trash
			// if ($canDo->get('core.delete'))
			// {
			$delete = RToolbarBuilder::createDeleteButton('designtypes.delete');
			$thirdGroup->addButton($delete);
			// }
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup)
			->addGroup($thirdGroup);

		return $toolbar;
	}
}
