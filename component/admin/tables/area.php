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
}
