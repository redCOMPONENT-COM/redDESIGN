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
	 * Removes background related files after erasing the background in database
	 *
	 * @param   int  $oid  background database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
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

		parent::onAfterDelete($oid);
	}
}
