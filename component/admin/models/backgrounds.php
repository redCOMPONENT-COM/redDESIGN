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
	protected function populateState($ordering = 'b.name', $direction = 'asc')
	{
		$filterDesignTypeId = $this->getUserStateFromRequest($this->context . '.filter_designtype_id', 'filter_designtype_id');
		$this->setState('designtype_id', $filterDesignTypeId);

		parent::populateState($ordering, $direction);
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

	/**
	 * Gets backgrounds to properties mapping.
	 *
	 * @param   int  $propertyId  Property ID
	 *
	 * @return  object  $mapping  Property ID - Background ID pair.
	 */
	public function getBackgroundPropertyPair($propertyId)
	{
		$db = $this->getDbo();
		$mapping = new JObject;
		$mapping->property_id = $propertyId;
		$mapping->background_id = 0;

		$query = $db->getQuery(true);
		$query->select($db->qn(array('property_id', 'background_id')))
			->from($db->qn('#__reddesign_property_background_mapping'))
			->where($db->qn('property_id') . ' = ' . $propertyId);
		$db->setQuery($query);
		$result = $db->loadObject();

		if (!empty($result))
		{
			$mapping = $result;
		}

		return $mapping;
	}

	/**
	 * Save backgrounds to properties mapping.
	 *
	 * @param   int  $propertyId    Property ID
	 * @param   int  $backgroundId  Background ID
	 *
	 * @return  object  $mapping  Property ID - Background ID pair.
	 */
	public function saveBackgroundPropertyPair($propertyId, $backgroundId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('property_id', 'background_id')))
			->from($db->qn('#__reddesign_property_background_mapping'))
			->where($db->qn('property_id') . ' = ' . $propertyId);
		$db->setQuery($query);
		$mapping = $db->loadObject();

		if (empty($mapping))
		{
			$mapping = new JObject;
			$mapping->property_id = $propertyId;
			$mapping->background_id = $backgroundId;

			return $db->insertObject('#__reddesign_property_background_mapping', $mapping);
		}
		else
		{
			$mapping->background_id = $backgroundId;

			return $db->updateObject('#__reddesign_property_background_mapping', $mapping, 'property_id');
		}
	}

	/**
	 * Gets join list of backgrounds as properties for a product.
	 *
	 * @param   int  $product_id  Product ID
	 *
	 * @return  array  Properties and backgrounds per product.
	 */
	public function getBackgroundsFromAttributes($product_id)
	{
		// Get attributes and properites of this product
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(
				$db->qn(
					array('attr.product_id', 'attr.attribute_id', 'prop.property_id', 'prop.ordering', 'prop.setdefault_selected', 'bg.background_id')
				)
			)
			->from(
				$db->qn('#__redshop_product_attribute', 'attr')
			)
			->innerJoin(
				$db->qn('#__redshop_product_attribute_property', 'prop') . ' ON (' . $db->qn('attr.attribute_id') . ' = ' . $db->qn('prop.attribute_id') . ')'
			)
			->innerJoin(
				$db->qn('#__reddesign_property_background_mapping', 'bg') . ' ON (' . $db->qn('prop.property_id') . ' = ' . $db->qn('bg.property_id') . ')'
			)
			->where(
				$db->qn('attr.product_id') . ' = ' . $product_id
			)
			->order(
				$db->qn('prop.ordering') . ' DESC'
			);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get default background (default as default attribute, "preselected" option in attributes tab).
	 *
	 * @param   int  $product_id  Product ID
	 *
	 * @return  object
	 */
	public function getDefaultBackground($product_id)
	{
		$backgrounds = $this->getBackgroundsFromAttributes($product_id);
		$defaultKey = 0;

		foreach ($backgrounds as $key => $object)
		{
			if ($object->setdefault_selected)
			{
				$defaultKey = $key;
				break;
			}
		}

		return $backgrounds[$defaultKey];
	}
}
