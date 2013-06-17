<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_redesign
 * @since          1.6
 */
class JFormFieldUserList extends JFormFieldList
{
	/**
	 * A Fontsize list that respects access controls
	 *
	 * @var        string
	 * @since    1.6
	 */
	public $type = 'UserList';

	/**
	 * Method to get a list of Users that respects access controls
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$db    = JFactory :: getDBO();
		$Query = "SELECT id,name FROM #__users as u LEFT join #__user_usergroup_map as um ON u.id = um.user_id WHERE um.group_id = 8";
		$db->setQuery($Query);
		$adminList = $db->loadObjectlist();

		$optionadmin = array();
		for ($i = 0; $i < count($adminList); $i++)
		{
			$optionadmin[] = JHTML::_('select.option', $adminList[$i]->id, $adminList[$i]->name);
		}
		return $optionadmin;

	}
}
