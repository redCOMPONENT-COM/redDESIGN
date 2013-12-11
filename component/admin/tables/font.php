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
		'fonts.store' => true,
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
	public $reddesign_font_id;

	/**
	 * @var  string
	 */
	public $title;

	/**
	 * @var  integer
	 */
	public $enabled = 1;

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
	public $created_on = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $modified_by = null;

	/**
	 * @var  string
	 */
	public $modified_on = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $locked_by = null;

	/**
	 * @var  string
	 */
	public $locked_on = '0000-00-00 00:00:00';

	/**
	 * @var  string
	 */
	public $font_file = '';

	/**
	 * @var  string
	 */
	public $font_thumb = '';

	/**
	 * @var  float
	 */
	public $default_width = 1;

	/**
	 * @var  float
	 */
	public $default_height = 1;

	/**
	 * @var  float
	 */
	public $default_caps_height = 1;

	/**
	 * @var  float
	 */
	public $default_baseline_height = 1;

	/**
	 * This is an object list of characters from the #__reddesign_chars table.
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
	 * Called before load(). Populate font table object with characters (character specific settings).
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function beforeLoad($keys = null, $reset = true)
	{
		if ($this->_eventBeforeLoad && $this->reddesign_font_id)
		{
			$db = $this->_db;

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select character settings for a given font.
			$query
				->select($db->qn(array('reddesign_char_id', 'font_char', 'width', 'height', 'typography', 'typography_height')))
				->from($db->qn('#__reddesign_chars'))
				->where($db->qn('reddesign_font_id') . ' = ' . $this->reddesign_font_id)
				->order('reddesign_char_id ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects.
			$charSettings = $db->loadObjectList();

			$this->chars = $charSettings;
		}

		return parent::beforeLoad($keys, $reset);
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
		// Trigger after delete
		if ($this->_eventAfterDelete)
		{
			// Delete font thumb file
			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_thumb))
			{
				JFile::delete(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_thumb);
			}

			// Delete font .ttf file
			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_file))
			{
				JFile::delete(JPATH_SITE . '/media/com_reddesign/fonts/' . $this->font_file);
			}
		}

		return parent::afterDelete($pk);
	}
}
