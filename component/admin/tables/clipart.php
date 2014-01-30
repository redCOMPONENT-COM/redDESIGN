<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Clipart table.
 *
 * @package     Reddesign.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableClipart extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_cliparts';

	/**
	 * @var  integer
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  integer
	 */
	public $categoryId = 0;

	/**
	 * @var string
	 */
	public $clipartFile = '';

	/**
	 * @var  integer
	 */
	public $state = 1;

	/**
	 * @var  integer
	 */
	public $ordering = 0;

	/**
	 * @var  integer
	 */
	public $created_by = null;

	/**
	 * @var  string
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $modified_by = null;

	/**
	 * @var  string
	 */
	public $modified_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $checked_out = null;

	/**
	 * @var  string
	 */
	public $checked_out_time = '0000-00-00 00:00:00';

	/**
	 * Removes clipart related files after erasing the clipart in database.
	 * Called after delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null)
	{
		// Delete clipart file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/cliparts/' . $this->clipartFile))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/cliparts/' . $this->clipartFile);
		}

		return parent::afterDelete($pk);
	}
}
