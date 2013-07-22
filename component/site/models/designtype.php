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
	 * Retrieve the preview background to be used as background for previews
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no preview background
	 */
	public function getPreviewBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isPreviewbgimage)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Preview background" setted
		return false;
	}

	/**
	 * Retrieve all the backgrounds to be used for previews
	 *
	 * @return mixed  Returns the array of preview backgrounds
	 */
	public function getPreviewBackgrounds()
	{
		$backgrounds		= $this->getBackgrounds();
		$previewBackgrounds	= array();

		foreach ($backgrounds as $background)
		{
			if (!$background->isPDFbgimage)
			{
				$previewBackgrounds[] = $background;
			}
		}

		return $previewBackgrounds;
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
	 * Retrieves the Accessories belonging to a specific design
	 *
	 * @return  array  Returns the array of parts that belonging to a design
	 */
	public function getAccessories()
	{
		$designAccessoryTypesIds = array_map('trim', explode(',', $this->getItem()->accessorytypes));


		$designAccessoriestypes = array();

		foreach ($designAccessoryTypesIds as $key => $value)
		{
			// Get each accessorytype available in current design
			$accessorytypesModel = FOFModel::getTmpInstance('Accessorytype', 'ReddesignModel');
			$designAccessoryType = $accessorytypesModel->getItem($value);

			// Check that this Accessorytype is published
			if ($designAccessoryType->enabled)
			{
				// Get the accessories in the Accessorytype
				$accessoriesModel = FOFModel::getTmpInstance('Accessories', 'ReddesignModel')
					->reddesign_accessorytype_id($designAccessoryType->reddesign_accessorytype_id);
				$accessoriesModel->setState('enabled', '1');

				// Set the accessories into the AccessoryType
				$designAccessoryType->accessories = $accessoriesModel->getItemList(true, 'reddesign_accessory_id');

				// Add the Accessorytype to the Accessorytypes list in the Design
				$designAccessoriestypes[] = $designAccessoryType;
			}
		}

		return $designAccessoriestypes;
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
