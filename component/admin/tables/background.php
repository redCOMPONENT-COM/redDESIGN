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
class ReddesignTableBackground extends FOFTable
{
	/**
	 * Delete a background
	 *
	 * @param   integer  $oid  The primary key value of the item to delete
	 *
	 * @return  boolean  True on success
	 */
	public function delete($oid = null)
	{
		if ($oid)
		{
			$this->load($oid);
		}

		if (!$this->onBeforeDelete($oid))
		{
			return false;
		}

		$k  = $this->_tbl_key;
		$pk = (is_null($oid)) ? $this->$k : $oid;

		// If no primary key is given, return false.

		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// If tracking assets, remove the asset first.

		if ($this->_trackAssets)
		{
			// Get and the asset name.
			$this->$k = $pk;
			$name     = $this->_getAssetName();

			// Do NOT touch JTable here -- we are loading the core asset table which is a JTable, not a FOFTable
			$asset    = JTable::getInstance('Asset');

			if ($asset->loadByName($name))
			{
				if (!$asset->delete())
				{
					$this->setError($asset->getError());

					return false;
				}
			}
			else
			{
				$this->setError($asset->getError());

				return false;
			}
		}

		// If this resource has tags, delete the tags first
		if ($this->_has_tags)
		{
			if (!$this->_tagsHelper->deleteTagData($this, $pk))
			{
				$this->setError('Error deleting Tags');

				return false;
			}
		}

		// Delete background EPS
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->eps_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->eps_file);
		}

		// Delete background preview image
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->image_path))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->image_path);
		}

		// Delete background thumbnail
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/thumbnails/' . $this->thumbnail))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/thumbnails/' . $this->thumbnail);
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from($this->_tbl);
		$query->where($this->_tbl_key . ' = ' . $this->_db->q($pk));
		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		$result = $this->onAfterDelete($oid);

		return $result;
	}
}
