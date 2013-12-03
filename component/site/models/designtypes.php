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
 * Designtype Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelDesigntypes extends FOFModel
{
	/**
	 * Retrieve all backgrounds from the database that belongs to the current design.
	 *
	 * @return array  Array of backgrounds.
	 */
	public function getBackgrounds()
	{
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel');
		$backgroundModel->setState('reddesign_designtype_id', $this->getId());

		$backgrounds = $backgroundModel->getItemList();

		return $backgrounds;
	}

	/**
	 * Retrieve the production file background from the backgrounds list.
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no PDF background.
	 */
	public function getProductionBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isProductionBg)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Production background", just text.
		return false;
	}

	/**
	 * Retrieve the preview background to be used as background for previews.
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no preview background.
	 */
	public function getPreviewBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isDefaultPreview)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Preview background" set.
		return false;
	}

}
