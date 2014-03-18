<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Areas Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelAreas extends RModelList
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$filterBackgroundId = $this->getUserStateFromRequest($this->context . '.filter_background_id', 'filter_background_id');
		$this->setState('filter.background_id', $filterBackgroundId);

		parent::populateState('a.name', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('a.*')
			->from($db->quoteName('#__reddesign_areas', 'a'));

		// Filter by Background ID
		$backgroundId = $this->getState('filter.background_id', 0);

		if ($backgroundId)
		{
			$query->where($db->qn('a.background_id') . ' = ' . $db->quote($backgroundId));
		}

		$query->order($db->escape('a.ordering') . ' ' . $db->escape('ASC'));

		return $query;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array    $pks    An array of primary key ids.
	 * @param   integer  $order  Order.
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function saveOrder($pks = null, $order = null)
	{
		// Initialise variables.
		$table = RTable::getAdminInstance('Area');

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState())
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Saves order on arrow up click.
	 *
	 * @param   int      $areaId         Clicker area ID.
	 * @param   int      $previousOrder  Previous order value at clicked area.
	 * @param   array    $pks            An array of primary key ids.
	 * @param   integer  $order          Order.
	 * @param   integer  $isUp           Ordering it up or down?
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function orderUpDown($areaId, $previousOrder, $pks, $order, $isUp)
	{
		$table = RTable::getAdminInstance('Area');

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState())
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
			}
		}

		$clickedAreaKey = array_search($areaId, $pks);

		$areaToMove = RTable::getAdminInstance('Area');

		if ($isUp)
		{
			$areaToMove->load($pks[--$clickedAreaKey]);
		}
		else
		{
			$areaToMove->load($pks[++$clickedAreaKey]);
		}

		$clickedAreaNewOrder = $areaToMove->ordering;
		$areaToMove->ordering = $previousOrder;

		if (!$areaToMove->store())
		{
			$this->setError($areaToMove->getError());

			return false;
		}

		$clickedArea = RTable::getAdminInstance('Area');
		$clickedArea->load($areaId);
		$clickedArea->ordering = $clickedAreaNewOrder;

		if (!$clickedArea->store())
		{
			$this->setError($clickedArea->getError());

			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   11.1
	 */
	protected function canEditState()
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', $this->option);
	}
}
