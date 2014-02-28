<?php
/**
 * @package     Redshopb.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Font table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableFont extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_fonts';

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
	 * @var string
	 */
	public $font_file = '';

	/**
	 * @var  float
	 */
	public $default_width = 1.0;

	/**
	 * @var  float
	 */
	public $default_height = 1.0;

	/**
	 * @var  float
	 */
	public $default_caps_height = 1.0;

	/**
	 * @var  float
	 */
	public $default_baseline_height = 1.0;

	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		$app = JFactory::getApplication();

		if (empty($this->default_width))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_WIDTH'), 'error');

			return false;
		}

		if (empty($this->default_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_HEIGHT'), 'error');

			return false;
		}

		if (empty($this->default_caps_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_CAPS_HEIGHT'), 'error');

			return false;
		}

		if (empty($this->default_baseline_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_BASELINE_HEIGHT'), 'error');

			return false;
		}

		return parent::check();
	}

	/**
	 * Removes font related files after erasing the font in database.
	 * Called after delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null)
	{
		// Delete font thumb file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . substr($this->font_file, 0, -3) . 'png'))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/fonts/' . substr($this->font_file, 0, -3) . 'png');
		}

		// Delete font .ttf file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_file);
		}

		return parent::afterDelete($pk);
	}
}
