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
jimport('joomla.filesystem.folder');

/**
 * Design Type table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableDesigntype extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reddesign_designtypes';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableKey = 'reddesign_designtype_id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'published';

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		// Create alias for title
		$this->alias = JFilterOutput::stringURLSafe($this->title);

		if (!parent::store($updateNulls))
		{
			return false;
		}

		return true;
	}
}
