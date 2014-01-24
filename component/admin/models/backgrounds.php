<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Backgrounds Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Models
 *
 * @since       1.0
 */
class ReddesignModelBackgrounds extends RModelList
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
				'id', 'b.id',
				'name', 'b.name',
				'designtype_id', 'b.designtype_id'
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
		$filterDesignTypeId = $this->getUserStateFromRequest($this->context . '.filter_designtype_id', 'filter_designtype_id');
		$this->setState('designtype_id', $filterDesignTypeId);

		parent::populateState('b.name', 'asc');
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
			->select('b.*')
			->from($db->quoteName('#__reddesign_backgrounds', 'b'));

		// Filter by Design Type
		$designTypeId = $this->getState('designtype_id', 0);

		if ($designTypeId)
		{
			$query->where($db->qn('b.designtype_id') . ' = ' . (int) $designTypeId);
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'b.name';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}
}
