<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Accessorytype Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelAccessorytypes extends FOFModel
{
	/**
	 * Deletes one or several Accessorytypes and it's linked accessories
	 *
	 * @return  boolean True on success
	 */
	public function delete()
	{
		if (is_array($this->id_list) && !empty($this->id_list))
		{
			$table = $this->getTable($this->table);

			foreach ($this->id_list as $id)
			{
				if (!$this->onBeforeDelete($id, $table))
				{
					continue;
				}

				if (!$table->delete($id))
				{
					$this->setError($table->getError());

					return false;
				}
				else
				{
					// Remove related Backgrounds
					$db		= JFactory::getDbo();
					$query	= $db->getQuery(true);

					$query
						->select('reddesign_accessory_id')
						->from('#__reddesign_accessories as ac')
						->where('ac.reddesign_accessorytype_id = ' . (int) $id)
						->order('ac.reddesign_accessory_id ASC');

					$db->setQuery($query);

					// Load the results as a list of stdClass objects.
					$accessories = $db->loadObjectList();

					$accessoryTable = FOFTable::getAnInstance('Accessory', 'ReddesignTable');

					foreach ($accessories as $accessory)
					{
						$accessoryTable->delete($accessory->reddesign_accessory_id);
					}

					$this->onAfterDelete($id);
				}
			}
		}

		return true;
	}
}