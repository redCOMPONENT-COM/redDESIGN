<?php
/**
 * @package     RedDesign.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;


/**
 * Font helper.
 *
 * @package     Reddesign.Libraries
 * @subpackage  Helpers
 *
 * @since       1.0
 */
final class ReddesignHelpersFont
{
	/**
	 * Returns Style Declaration for given fonts
	 *
	 * @param   array  $fonts  List of fonts that is going to be loaded
	 *
	 * @return string
	 */
	public static function getFontStyleDeclaration($fonts = null)
	{
		if (!empty($fonts))
		{
			/** @var ReddesignModelFonts $fontsModel */
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');
			$fontsModel->setState('filter.id', $fonts);

			$fontStyleDeclaration = RLayoutHelper::render('svg.font-face', array(
					'items' => $fontsModel->getItems(),
				),
				JPATH_ROOT . '/administrator/components/com_reddesign/layouts'
			);

			return trim(preg_replace('/\s+/', ' ', $fontStyleDeclaration));
		}
	}

	/**
	 * Returns Font Format Name for given Extension
	 *
	 * @param   string  $extension  Extension name
	 *
	 * @return string
	 */
	public static function getFontExtensionFormatName($extension)
	{
		$extension = strtolower($extension);
		$formats = array(
			'ttf' => 'truetype',
			'eot' => 'embedded-opentype',
			'woff' => 'woff',
			'svg' => 'svg',
		);

		return isset($formats[$extension]) ? $formats[$extension] : '';
	}

	/**
	 * Get List of used fonts for areas
	 *
	 * @param   array  $areas  List of area objects
	 *
	 * @return  array
	 */
	public static function getSelectedFontsFromArea($areas = null)
	{
		$selectedFonts = array();

		if (!empty($areas))
		{
			foreach ($areas as $area)
			{
				$fonts = (array) json_decode($area->font_id);

				if (!empty($fonts) && is_array($fonts))
				{
					foreach ($fonts as $font)
					{
						$selectedFonts[$font] = $font;
					}
				}
			}
		}

		return $selectedFonts;
	}
}
