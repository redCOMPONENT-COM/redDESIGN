<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Backgrounds Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Models
 *
 * @since       1.0
 */
class ReddesignModelBackgrounds extends RModelList
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'reddesign_background_id', 'b.reddesign_background_id',
				'title', 'b.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('b.title', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('b.*')
			->from($db->quoteName('#__reddesign_backgrounds', 'b'));

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'b.title';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Set a specific designtype background as the background for the PDF production file
	 *
	 * @param   int  $designId  The designtype id
	 * @param   int  $bgId      The background id
	 *
	 * @return bool
	 */
	public function setAsProductionFileBg($designId, $bgId)
	{
		if (!$this->unsetAllIsProductionBg($designId))
		{
			return false;
		}

		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update set the specific background as PDF background for production file. Also remove it from preview backgrounds if is the case.
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isProductionBg') . ' = ' . $this->_db->q(1))
			->where($this->_db->qn('reddesign_background_id') . ' = ' . $this->_db->q($bgId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Set all Backgrounds from a specific design as Not Production PDF Files
	 *
	 * @param   int  $designId  The Design where the backgrounds belogns to.
	 *
	 * @return bool
	 */
	public function unsetAllIsProductionBg($designId)
	{
		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update all current design background and set them as none is the background for PDF production file.
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isProductionBg') . ' = ' . $this->_db->q(0))
			->where($this->_db->qn('reddesign_designtype_id') . ' = ' . $this->_db->q($designId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Set a specific designtype background as the background for preview
	 *
	 * @param   int  $designId  The designtype id
	 * @param   int  $bgId      The background id
	 *
	 * @return bool
	 */
	public function setAsPreviewbg($designId, $bgId)
	{
		if (!$this->unsetAllIsDefaultPreview($designId))
		{
			return false;
		}

		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update set the specific background as Preview background for production file. Also prevent to be used as PDF background
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isDefaultPreview') . ' = ' . $this->_db->q(1))
			->where($this->_db->qn('reddesign_background_id') . ' = ' . $this->_db->q($bgId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Set all Backgrounds from a specific design as Not Preview Files
	 *
	 * @param   int  $designId  The Design where the backgrounds belogns to.
	 *
	 * @return bool
	 */
	public function unsetAllIsDefaultPreview($designId)
	{
		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update all current design background and set them as none is the background for PDF production file.
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isDefaultPreview') . ' = ' . $this->_db->q(0))
			->where($this->_db->qn('reddesign_designtype_id') . ' = ' . $this->_db->q($designId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}
}
