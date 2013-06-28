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
class ReddesignModelBackground extends FOFModel
{
	/**
	 * Set a specific designtype background as the background for the PDF production file
	 *
	 * @param   int  $designId  The designtype id
	 * @param   int  $bgId      The background id
	 *
	 * @return bool
	 */
	public function setAsPDFbg($designId, $bgId)
	{
		if (!$this->unsetAllPDFBg($designId))
		{
			return false;
		}

		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update set the specific background as PDF background for production file
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isPDFbgimage') . ' = ' . $this->_db->q(1))
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
	public function unsetAllPDFBg($designId)
	{
		// Create a new query object.
		$query = $this->_db->getQuery(true);

		// Update all current design background and set them as none is the background for PDF production file.
		$query
			->update($this->_db->qn('#__reddesign_backgrounds'))
			->set($this->_db->qn('isPDFbgimage') . ' = ' . $this->_db->q(0))
			->where($this->_db->qn('reddesign_designtype_id') . ' = ' . $this->_db->q($designId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}
}
