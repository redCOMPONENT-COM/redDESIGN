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
 * Font Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelFont extends FOFModel
{
	/**
	 * This method runs after an item has been gotten from the database in a read
	 * operation. You can modify it before it's returned to the MVC triad for
	 * further processing.
	 *
	 * @param   FOFTable  &$record  FOFTable object populated with data.
	 *
	 * @return  void
	 */
	protected function onAfterGetItem(&$record)
	{
		parent::onAfterGetItem($record);

		if (!empty($record->reddesign_font_id))
		{
			// Create a new query object.
			$query = $this->_db->getQuery(true);

			// Select character settings for a given font.
			$query
				->select('chars.reddesign_char_id, chars.font_char, chars.width, chars.height, chars.typography, chars.typography_height')
				->from('#__reddesign_chars as chars')
				->where('chars.reddesign_font_id = ' . $record->reddesign_font_id)
				->order('chars.reddesign_char_id ASC');

			// Reset the query using our newly populated query object.
			$this->_db->setQuery($query);

			// Load the results as a list of stdClass objects.
			$charSettings = $this->_db->loadObjectList();

			$record->chars = $charSettings;
		}
	}
}
