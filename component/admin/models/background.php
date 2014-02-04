<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Background Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelBackground extends RModelAdmin
{
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		$query = $this->_db->getQuery(true);
		$query->select($this->_db->qn('property_id'))
			->from($this->_db->qn('#__reddesign_property_background_mapping'))
			->where($this->_db->qn('background_id') . ' = ' . $pk);
		$this->_db->setQuery($query);
		$propertyId = $this->_db->loadResult();

		$item->property_id = $propertyId;

		return $item;
	}

	/**
	 * Gets background by redSHOP property ID.
	 *
	 * @param   integer  $propertyId  The id of the redSHOP property.
	 *
	 * @return  object    Object on success, false on failure.
	 */
	public function getItemByProperty($propertyId = null)
	{
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->qn('background_id'))
			->from($this->_db->qn('#__reddesign_property_background_mapping'))
			->where($this->_db->qn('property_id') . ' = ' . $propertyId);
		$this->_db->setQuery($query);
		$backgroundId = $this->_db->loadResult();

		return $this->getItem($backgroundId);
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
			->where($this->_db->qn('id') . ' = ' . $this->_db->q($bgId));

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
			->where($this->_db->qn('designtype_id') . ' = ' . $this->_db->q($designId));

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
			->where($this->_db->qn('id') . ' = ' . $this->_db->q($bgId));

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
			->where($this->_db->qn('designtype_id') . ' = ' . $this->_db->q($designId));

		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Save Background from Design Type
	 *
	 * @param   array  $data  jform post values
	 *
	 * @return bool
	 */
	public function saveBackground($data)
	{
		$app = JFactory::getApplication();
		$updatedSVG = false;
		$uploaded_file = null;
		$file = $app->input->files->get('jform');
		$file = $file['bg_svg_file'];

		// Get component configuration
		$config = ReddesignEntityConfig::getInstance();

		// Error in file upload
		if (!empty($file['name']) && !empty($file['error']) && (int) $file['error'] > 0)
		{
			$app->enqueueMessage(
				JText::sprintf('COM_REDDESIGN_FILE_HELPER_ERROR_FILE_TOOLARGE', str_replace('M', '', ini_get('upload_max_filesize'))),
				'error'
			);

			return false;
		}

		// If file has has not been uploaded
		if (empty($file['name']) || empty($file['type']))
		{
			// If is a new background and the file is not attached return error
			if (!$data['id'])
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_NO_FILE'), 'error');

				return false;
			}
		}
		else
		{
			if (empty($data['name']))
			{
				$data['name'] = str_replace('.' . JFile::getExt($file['name']), '', $file['name']);
			}

			$updatedSVG = true;

			// Upload the background file
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'backgrounds', $config->getMaxSVGFileSize(), 'svg');

			if (!$uploaded_file)
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_UPLOAD_FAILED'), 'error');

				return false;
			}
		}

		// On edit
		if (!empty($data['id']))
		{
			// If images has been updated remove old images
			if ($updatedSVG)
			{
				$table = $this->getTable();
				$table->load($data['id']);

				// Delete old SVG
				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $table->svg_file))
				{
					JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $table->svg_file);
				}

				$data['svg_file'] = $uploaded_file['mangled_filename'];
			}
			else
			{
				unset($data['svg_file']);
			}
		}
		else
		{
			// Update the database with the new path of the SVG file.
			$data['svg_file'] = $uploaded_file['mangled_filename'];
		}

		// If this new background will be the SVG Production background, switch it against the previous production background.
		if (!empty($data['isProductionBg']))
		{
			// Set all other backgrounds as non SVG backgrounds.
			$this->unsetAllIsProductionBg($data['designtype_id']);
		}

		// If this new background will be the preview background, switch it against the previous preview background.
		if (!empty($data['isDefaultPreview']))
		{
			// Set all other backgrounds as non SVG backgrounds.
			$this->unsetAllIsDefaultPreview($data['designtype_id']);
		}

		if (empty($data['isProductionBg']))
		{
			$data['isProductionBg'] = 0;
		}

		if (empty($data['isDefaultPreview']))
		{
			$data['isDefaultPreview'] = 0;
		}

		if (empty($data['isPreviewBg']))
		{
			$data['isPreviewBg'] = 0;
		}

		if (empty($data['useCheckerboard']))
		{
			$data['useCheckerboard'] = 0;
		}

		return parent::save($data);
	}
}
