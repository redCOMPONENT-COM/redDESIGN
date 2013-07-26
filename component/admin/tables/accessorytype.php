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
 * Accessorytype Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableAccessorytype extends FOFTable
{
	/**
	 * Removes Accessorytype related files after erasing the item in database
	 *
	 * @param   int  $oid  Accessorytype database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete accessorytype image
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/' . $this->sample_image))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/' . $this->sample_image);
		}

		// Delete accessorytype thumbnail
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $this->sample_thumb))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $this->sample_thumb);
		}

		parent::onAfterDelete($oid);
	}
}
