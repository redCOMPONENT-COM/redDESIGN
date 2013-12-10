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
 * Wardrobe table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableFont extends RTable
{
	/**
	 * The options.
	 *
	 * @var  array
	 */
	protected $_options = array(
		'fonts.load' => true,
		'fonts.store' => false,
		'chars.store' => false,
	);

	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_chars';

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
	public $state;

	/**
	 * This is an array of department id from
	 * the #__reddesign_chars table.
	 *
	 * @var  array
	 */
	public $chars;


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

		if (empty($this->font_file))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_FONT_FILE'), 'error');

			return false;
		}

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
	 * Removes font related files after erasing the font in database
	 *
	 * @param   int  $oid  font database row id
	 *
	 * @return bool|void
	 */
	/*protected function onAfterDelete($oid)
	{
		// Delete font thumb file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb);
		}

		// Delete font .ttf file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file);
		}

		parent::onAfterDelete($oid);
	}*/
}
