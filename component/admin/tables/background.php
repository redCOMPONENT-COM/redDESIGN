<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Background Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableBackground extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_backgrounds';

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
	 * @var  string
	 */
	public $svg_file = '';

	/**
	 * @var  bool
	 */
	public $isProductionBg = '1';

	/**
	 * @var  bool
	 */
	public $isPreviewBg = '1';

	/**
	 * @var  bool
	 */
	public $isDefaultPreview = '1';

	/**
	 * @var  bool
	 */
	public $useCheckerboard = '1';

	/**
	 * @var  bool
	 */
	public $designtype_id;
	/**
	 * Removes background related files after deleting background from the database.
	 * Called after delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null)
	{
		// Delete background EPS
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->svg_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->svg_file);
		}

		return parent::afterDelete($pk);
	}
}
