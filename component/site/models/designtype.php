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
class ReddesignModelDesigntype extends FOFModel
{
	/**
	 * Retrieve all backgrounds from the database that belongs to the current design
	 *
	 * @return array  Array of backgrounds
	 */
	public function getBackgrounds()
	{
		$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->getId());

		$backgrounds = $backgroundModel->getItemList();

		return $backgrounds;
	}

	/**
	 * Retrieve the production file background from the backgrounds list
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no PDF background
	 */
	public function getProductionBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isPDFbgimage)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Production background", just text
		return false;
	}

	/**
	 * Retrieve the background to be used as background for previews
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no preview background
	 */
	public function getPreviewBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if (!$background->isPDFbgimage)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Preview background", just text
		return false;
	}

	/**
	 * Retrieves the areas belonging to a specific production background
	 *
	 * @param   int  $productionBackgroundId  The production background id
	 *
	 * @return  array  Returns the array of areas that belongs to a background
	 */
	public function getProductionBackgroundAreas($productionBackgroundId)
	{
		$areasModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($productionBackgroundId);

		$areas = $areasModel->getItemList();

		return $areas;
	}

	/**
	 * Retrieves the parts belonging to a specific design
	 *
	 * @return  array  Returns the array of parts that belonging to a design
	 */
	public function getParts()
	{
		$partsModel = FOFModel::getTmpInstance('Parts', 'ReddesignModel')->reddesign_designtype_id($this->getId());

		$parts = $partsModel->getItemList();

		return $parts;
	}

	/**
	 * Retrieves the fonts in the system
	 *
	 * @return  array  Returns the array of fonts
	 */
	public function getFonts()
	{
		$fontsModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel');

		$fonts = $fontsModel->getItemList(false, 'reddesign_font_id');

		return $fonts;
	}
}
