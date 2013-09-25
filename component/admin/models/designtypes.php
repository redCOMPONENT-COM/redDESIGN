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
 * Designtype Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelDesigntypes extends FOFModel
{
	/**
	 * Deletes one or several designs and it's linked backgrounds
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

				// Remove related Images
				$table->load($id, true);

				// Remove related Backgrounds
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);

				$query
					->select('reddesign_background_id')
					->from('#__reddesign_backgrounds as bg')
					->where('bg.reddesign_designtype_id = ' . (int) $id)
					->order('bg.reddesign_background_id ASC');

				$db->setQuery($query);

				// Load the results as a list of stdClass objects.
				$backgrounds = $db->loadObjectList();
				$bgTable = FOFTable::getAnInstance('Background', 'ReddesignTable');

				foreach ($backgrounds as $background)
				{
					// Table needs to be loaded because Background onAfterDelete needs the fields to execute it's task
					$bgTable->load($background->reddesign_background_id, true);
					$bgTable->delete($background->reddesign_background_id);
				}

				if (!$table->delete($id))
				{
					$this->setError($table->getError());

					return false;
				}
				else
				{
					$this->onAfterDelete($id);
				}
			}
		}

		return true;
	}
}
