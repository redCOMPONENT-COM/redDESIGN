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
 * Designtype Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableDesigntype extends FOFTable
{
	/**
	 * Removes Designtype related files after erasing the item in database
	 *
	 * @param   int  $oid  Designtype database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete accessory image
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/designtypes/' . $this->sample_image))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/designtypes/' . $this->sample_image);
		}

		// Delete accessory thumbnail
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/designtypes/thumbnails/' . $this->sample_thumb))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/designtypes/thumbnails/' . $this->sample_thumb);
		}

		parent::onAfterDelete($oid);
	}
}
