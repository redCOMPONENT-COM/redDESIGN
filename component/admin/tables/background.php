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
class ReddesignTableBackground extends FOFTable
{
	/**
	 * Removes font related files after erasing the font in database
	 *
	 * @param   int  $oid  font database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete background EPS file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->epsfile))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->epsfile);
		}

		// Delete JPG preview of the EPS file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->jpegpreviewfile))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/backgrounds/' . $this->jpegpreviewfile);
		}

		parent::onAfterDelete($oid);
	}
}
