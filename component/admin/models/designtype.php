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
 * Design Type Model
 *
 * @package     Reddesign.Backend
 * @subpackage  Models
 * @since       1.0
 */
class ReddesignModelDesigntype extends RModelAdmin
{
	/**
	 * Retrieve all backgrounds from the database that belongs to the current design.
	 *
	 * @param   int  $designTypeId  Design type ID
	 *
	 * @return  array  Array of backgrounds.
	 */
	public function getBackgrounds($designTypeId)
	{
		$backgroundsModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
		$backgroundsModel->setState('designtype_id', $designTypeId);

		return $backgroundsModel->getItems();
	}

	/**
	 * Retrieve the production file background from the backgrounds list.
	 *
	 *  @param   int  $designTypeId  Design type ID
	 *
	 * @return mixed  Returns the array that describes a background or null if there is no PDF background.
	 */
	public function getProductionBackground($designTypeId)
	{
		$backgrounds = $this->getBackgrounds($designTypeId);

		foreach ($backgrounds as $background)
		{
			if ($background->isProductionBg)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Production background", just text.
		return null;
	}

	/**
	 * Retrieve the preview background to be used as background for previews.
	 *
	 *  @param   int  $designTypeId  Design type ID
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no preview background.
	 */
	public function getDefaultPreviewBackground($designTypeId)
	{
		$backgrounds = $this->getBackgrounds($designTypeId);

		foreach ($backgrounds as $background)
		{
			if ($background->isDefaultPreview)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Preview background" set.
		return null;
	}
}
