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
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   11.1
	 */
	public function save($data)
	{
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		if ($pk > 0)
		{
			$isNew = false;
		}

		if ($isNew)
		{
			$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
			$areasModel->setState('filter.background_id', $data['background_id']);
			$displayedAreas = $areasModel->getItems();
			$newOrderingValue = end($displayedAreas);
			$newOrderingValue = $newOrderingValue->ordering;
			$newOrderingValue++;
			$data['ordering'] = $newOrderingValue;
		}

		return parent::save($data);
	}
}
