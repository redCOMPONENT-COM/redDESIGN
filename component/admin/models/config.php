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
 * Config Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelConfig extends RModelAdmin
{
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
		if (array_key_exists('tags', $data))
		{
			unset($data['tags']);
		}

		// Check if #__reddesign_config table is empty.
		$item = $this->getItem(1);

		if ($item)
		{
			$new = array('id' => 1, 'params' => json_encode($data));
		}
		else
		{
			$new = array('params' => json_encode($data));
		}

		return parent::save($new);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = 1)
	{
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($pk);

		// Check for a table object error.
		if ($return === false)
		{
			return false;
		}

		// Get the table properties.
		$properties = $table->getProperties(1);

		// Decode the config.
		return json_decode($properties['params'], true);
	}
}
