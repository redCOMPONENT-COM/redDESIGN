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
 * Area Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelArea extends RModelAdmin
{
	/**
	 * Saves design areas for AJAX request.
	 *
	 * @param   string  $data  Array data.
	 * 
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxSave($data)
	{
		$input   = JFactory::getApplication()->input;
		$table = $this->getTable('Area');

		// Insert new order item
		if (!$table->bind((array) $data))
		{
			throw new Exception($table->getError());
		}

		if (!$table->check())
		{
			throw new Exception($table->getError());
		}

		if (!$table->store())
		{
			throw new Exception($table->getError());
		}

		return true;

		/*
		foreach ($data as $key => $item)
		{
			if (strpos($key, 'item') === false)
			{
				continue;
			}

			// Insert new order item
			if (!$table->bind((array) $item))
			{
				throw new Exception($table->getError());
			}

			if (!$table->check())
			{
				throw new Exception($table->getError());
			}

			if (!$table->store())
			{
				throw new Exception($table->getError());
			}
		}

		return true;
		*/
	}
}
