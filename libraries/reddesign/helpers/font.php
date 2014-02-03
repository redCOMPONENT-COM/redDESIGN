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
	 * Returns Style Declaration for given fonts Ids
	 *
	 * @param   array  $fontIds  List of font Ids that is going to be loaded
	 *
	 * @return string
	 */
	public static function getFontStyleDeclaration($fontIds = null)
	{
		if (!empty($fontIds) || !empty($fontList))
		{
			/** @var ReddesignModelFonts $fontsModel */
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');
			$fontsModel->setState('filter.id', $fontIds);

			return self::createFontStyleDeclaration($fontsModel->getItems());
		}
	}

	/**
	 * Returns Style Declaration for given fonts objects
	 *
	 * @param   object  $fontList  List of font Ids that is going to be loaded
	 *
	 * @return string
	 */
	public static function createFontStyleDeclaration($fontList = null)
	{
		if (!empty($fontList))
		{
			$fontStyleDeclaration = RLayoutHelper::render('svg.font-face', array(
					'items' => $fontList,
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
	 * Returns List of all fonts
	 *
	 * @return array
	 */
	public static function getFontSelectOptions()
	{
		// Get all fonts in the system to be choosen or not for the current design.
		$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');
		$fonts = $fontsModel->getItems();
		$fontsOptions = array();

		foreach ($fonts as $font)
		{
			$fontsOptions[] = JHtml::_('select.option', $font->id, $font->name);
		}

		return $fontsOptions;
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
