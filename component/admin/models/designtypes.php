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
	public function getProductDesignTypesMapping($productId)
	{
		$db = $this->getDbo();
		$mapping = new JObject;
		$mapping->product_id = $productId;
		$mapping->default_designtype_id = 0;
		$mapping->related_designtype_ids = 0;

		$query = $db->getQuery(true);
		$query->select($db->qn(array('product_id', 'default_designtype_id', 'related_designtype_ids')))
			->from($db->qn('#__reddesign_product_mapping'))
			->where($db->qn('product_id') . ' = ' . $productId);
		$db->setQuery($query);
		$result = $db->loadObject();

		if (!empty($result))
		{
			$mapping = $result;
		}

		return $mapping;
	}

	/**
	 * Save product - designtype mapping. Assign designtypes to a redSHOP product.
	 *
	 * @param   int    $productId             Product ID
	 * @param   int    $defaultDesigntypeId   ID of design type which will be displayed first in the frontend.
	 * @param   array  $relatedDesigntypeIds  Design Type IDs
	 *
	 * @return  bool
	 */
	public function saveProductDesignTypesMapping($productId, $defaultDesigntypeId, $relatedDesigntypeIds)
	{
		$relatedDesigntypeIds = implode(',', $relatedDesigntypeIds);

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('product_id', 'default_designtype_id', 'related_designtype_ids')))
			->from($db->qn('#__reddesign_product_mapping'))
			->where($db->qn('product_id') . ' = ' . $productId);
		$db->setQuery($query);
		$mapping = $db->loadObject();

		if (empty($mapping))
		{
			$mapping = new JObject;
			$mapping->product_id = $productId;
			$mapping->default_designtype_id = $defaultDesigntypeId;
			$mapping->related_designtype_ids = $relatedDesigntypeIds;

			return $db->insertObject('#__reddesign_product_mapping', $mapping);
		}
		else
		{
			$mapping->default_designtype_id = $defaultDesigntypeId;
			$mapping->related_designtype_ids = $relatedDesigntypeIds;

			return $db->updateObject('#__reddesign_product_mapping', $mapping, 'product_id');
		}
	}
}
