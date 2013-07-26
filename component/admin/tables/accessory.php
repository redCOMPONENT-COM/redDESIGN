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
 * Accessory Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableAccessory extends FOFTable
{
	/**
	 * Removes accessory related files after erasing the item in database
	 *
	 * @param   int  $oid  accessory database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete accessory image
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessories/' . $this->image))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessories/' . $this->image);
		}

		// Delete accessory thumbnail
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessories/thumbnails/' . $this->thumbnail))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessories/thumbnails/' . $this->thumbnail);
		}

		parent::onAfterDelete($oid);
	}
}
