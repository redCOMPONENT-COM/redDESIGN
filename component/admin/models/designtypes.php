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
 * Design Types Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelDesigntypes extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_designtypes';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'designtype_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

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
				'id', 'd.id',
				'name', 'd.name',
				'state', 'd.state',
				'ordering', 'd.ordering',
				'created_by', 'd.created_by',
				'created_date', 'd.created_date'
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
		parent::populateState('d.name', 'asc');
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
					->select('d.*')
					->from($db->qn('#__reddesign_designtypes', 'd'));

		// Filter search
		$search = $this->getState('filter.search_designtypes');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(d.name LIKE ' . $search . ')');
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'd.name';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Get product - designtype mapping. List of designtypes assigned to a redSHOP product.
	 *
	 * @param   int  $productId  Product ID
	 *
	 * @return  array
	 */
	public function getProductDesignTypes($productId)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select($db->quoteName('designtype_id'));
		$query->from($db->quoteName('#__reddesign_product_mapping'));
		$query->where($db->quoteName('product_id') . ' = ' . $productId);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Save product - designtype mapping. Assign designtypes to a redSHOP product.
	 *
	 * @param   int    $productId               Product ID
	 * @param   array  $reddesignDesigntypeIds  Design Type IDs
	 *
	 * @return  bool
	 */
	public function saveProductDesignTypes($productId, $reddesignDesigntypeIds)
	{
		$reddesignDesigntypeIds = implode(',', $reddesignDesigntypeIds);

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('designtype_id', 'product_id')));
		$query->from($db->quoteName('#__reddesign_product_mapping'));
		$query->where($db->quoteName('product_id') . ' = ' . $productId);
		$db->setQuery($query);
		$map = $db->loadObject();

		if (empty($map))
		{
			$map = new JObject;
			$map->product_id = $productId;
			$map->designtype_id = $reddesignDesigntypeIds;

			return $db->insertObject('#__reddesign_product_mapping', $map);
		}
		else
		{
			$map->designtype_id = $reddesignDesigntypeIds;

			return $db->updateObject('#__reddesign_product_mapping', $map, 'product_id');
		}
	}
}
