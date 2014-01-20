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
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true));
			$fontsModel->setState('id', $fonts);

			$fontStyleDeclaration = RLayoutHelper::render('svg.font-face', array(
					'items' => $fontsModel->getItems(),
				)
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
}
