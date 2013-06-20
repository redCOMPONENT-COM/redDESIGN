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
 * Font Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableFont extends FOFTable
{
	/**
	 * Class Constructor.
	 *
	 * @param   string          $table  Name of the database table to model.
	 * @param   string          $key    Name of the primary key field in the table.
	 * @param   JDatabaseDriver &$db    Database driver
	 */
	function __construct($table, $key, &$db)
	{
		parent::__construct($table, $key, $db);

		$query = $this->_db->getQuery(true)
			->select('chars.reddesign_font_char_id, chars.font_char, chars.width, chars.height, chars.typography, chars.typography_height')
			->leftJoin('#__reddesign_font_chars as chars ON ' . $this->_tbl . '.reddesign_font_id = chars.reddesign_font_id');

		$this->setQueryJoin($query);
	}

	/**
	 * NOTICE: This function is overrided because of the bug in FOF 2.1.a1 2013-05-11, line number 450
	 * Method to load a row from the database by primary key and bind the fields
	 * to the FOFTable instance properties.
	 *
	 * @param   mixed   $keys        An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                               set the instance property value is used.
	 * @param   boolean $reset       True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function load($keys = null, $reset = true)
	{
		if (!$this->_tableExists)
		{
			$result = false;
		}

		if (empty($keys))
		{
			// If empty, use the value of the current key
			$keyName = $this->_tbl_key;

			if (isset($this->$keyName))
			{
				$keyValue = $this->$keyName;
			}
			else
			{
				$keyValue = null;
			}

			// If empty primary key there's is no need to load anything
			if (empty($keyValue))
			{
				$result = true;

				return $this->onAfterLoad($result);
			}

			$keys = array($keyName => $keyValue);
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl . '.*');
		$query->from($this->_tbl);

		// Joined fields are ok, since I initialized them in the constructor
		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!in_array($field, $fields))
			{
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
			}

			// Add the search tuple to the query.
			$query->where($this->_db->qn($this->_tbl . '.' . $field) . ' = ' . $this->_db->q($value));
		}

		// Do I have any joined table?
		$j_query = $this->getQueryJoin();

		if ($j_query)
		{
			if ($j_query->select && $j_query->select->getElements())
			{
				$query->select($this->normalizeSelectFields($j_query->select->getElements(), true));
			}

			if ($j_query->join)
			{
				foreach ($j_query->join as $join)
				{
					$t = (string) $join;

					// Guess what? Joomla doesn't provide any access to the "name" variable, so
					// I have to work with strings... -.-
					if (stripos($t, 'inner') !== false)
					{
						$query->innerJoin($join->getElements());
					}
					elseif (stripos($t, 'left') !== false)
					{
						$query->leftJoin($join->getElements());
					}
				}
			}
		}

		$this->_db->setQuery($query);

		$row = $this->_db->loadAssoc();

		// Check that we have a result.
		if (empty($row))
		{
			$result = true;

			return $this->onAfterLoad($result);
		}

		// Bind the object with the row and return.
		$result = $this->bind($row);

		$this->onAfterLoad($result);

		return $result;
	}

	/**
	 * NOTICE: This function is overrided because of the bug in FOF 2.1.a1 2013-05-11, line number 1690
	 * Normalizes the fields, returning an array with all the fields.
	 * Ie array('foobar, foo') becomes array('foobar', 'foo')
	 *
	 * @param    array $fields    Array with column fields
	 * @param bool     $extended
	 *
	 * @internal param bool $useAlias Should I use the column alias or use the extended syntax?
	 *
	 * @return   array     Normalized array
	 */
	protected function normalizeSelectFields($fields, $extended = false)
	{
		$return = array();

		foreach ($fields as $field)
		{
			$t_fields = explode(',', $field);

			foreach ($t_fields as $t_field)
			{
				// Is there any alias for this column?
				preg_match('#\sas\s`?\w+`?#i', $t_field, $match);
				$alias = empty($match) ? '' : $match[0];
				$alias = preg_replace('#\sas\s?#i', '', $alias);

				// Grab the "standard" name
				// @TODO Check this pattern since it's blind copied from forums
				preg_match('/([\w]++)`?+(?:\s++as\s++[^,\s]++)?+\s*+($)/i', $t_field, $match);
				$column = $match[1];
				$column = preg_replace('#\sas\s?#i', '', $column);

				// Trim whitespace
				$alias = preg_replace('#^[\s-`]+|[\s-`]+$#', '', $alias);
				$column = preg_replace('#^[\s-`]+|[\s-`]+$#', '', $column);

				// Do I want the column name with the original name + alias?
				if ($extended && $alias)
				{
					$alias = $column . ' AS ' . $alias;
				}

				if (!$alias)
				{
					$alias = $column;
				}

				$return[$column] = $alias;
			}
		}

		return $return;
	}

	/**
	 * Performs validation check for field values
	 *
	 * @access public
	 *
	 * @param void
	 *
	 * @return bool
	 */
	function check()
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
	 * @param   int $oid  font database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete font and font thumb file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb);
		}

		// Delete font and font file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file);
		}

		parent::onAfterDelete($oid);
	}
}
