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
	 * @param   array  $data  Array data.
	 *
	 * @throws Exception
	 * @access public
	 *
	 * @return mixed
	 */
	public function ajaxSave($data)
	{
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
	}
}
