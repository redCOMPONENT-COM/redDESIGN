<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_COMPONENT . '/helpers');

/**
 * Design Type table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableArea extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reddesign_areas';

	/**
	 * @var  array
	 */
	public $cliparts;

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array to bind
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @since   1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['font_id']) && is_array($array['font_id']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['font_id']);
			$array['font_id'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Assign list of fonts to all areas.
	 *
	 * @param   string  $fontIds  JSON list of font IDs
	 * @param   array   $areaIds  Array of area IDs
	 *
	 * @throws  Exception
	 * @return  boolean  True on success.
	 */
	public function fontsToAllAreas($fontIds, $areaIds)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl)
			->set($this->_db->qn('font_id') . ' = ' . $this->_db->quote($fontIds))
			->where($k . ' = ' . implode(' OR ' . $k . ' = ', $areaIds));

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			throw new Exception(JText::_('COM_REDDESIGN_DB_ERROR'), 500);
		}

		return true;
	}

	/**
	 * Called after load().
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function afterLoad($keys = null, $reset = true)
	{
		// Load cliparts
		if ($this->areaType == 2)
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true)
				->select('ac.clipartId, c.*, c1.title as categoryName')
				->from($db->qn('#__reddesign_area_clipart_xref', 'ac'))
				->leftJoin($db->qn('#__reddesign_cliparts', 'c') . ' ON c.id = ac.clipartId')
				->leftJoin($db->qn('#__categories', 'c1') . ' on c.categoryId = c1.id')
				->where('c.state = 1 AND ac.areaId = ' . $this->id);

			$db->setQuery($query);
			$this->cliparts = $db->loadObjectList();
		}

		return parent::afterLoad($keys, $reset);
	}

	/**
	 * Called after store().
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterStore($updateNulls = false)
	{
		if ($this->areaType == 2)
		{
			$data = JFactory::getApplication()->input->get('jform', array(), 'array');

			// Delete all items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->delete($db->qn('#__reddesign_area_clipart_xref'))
				->where('areaId = ' . (int) $this->id);
			$db->setQuery($query);

			if (!$db->execute())
			{
				return false;
			}

			if (!empty($data['areaCliparts']))
			{
				JArrayHelper::toInteger($data['areaCliparts']);

				/** @var ReddesignTableArea_clipart_xref $xrefTable */
				$xrefTable = RTable::getAdminInstance('Area_clipart_xref');

				// Store the new items
				if (is_array($data['areaCliparts']) && count($data['areaCliparts']) > 0)
				{
					foreach ($data['areaCliparts'] as $clipart)
					{
						if (!$xrefTable->save(
								array(
									'areaId' => $this->id,
									'clipartId' => $clipart
								)
							))
						{
							$this->setError($xrefTable->getError());

							return false;
						}
					}
				}
			}
		}

		return parent::afterStore($updateNulls);
	}
}
