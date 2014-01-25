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
 * SVG helper.
 *
 * @package     Reddesign.Libraries
 * @subpackage  Helpers
 *
 * @since       1.0
 */
final class ReddesignHelpersSvg
{
	/**
	 * Returns Checkboard background in svg format
	 *
	 * @param   int     $width         checkerboard width
	 * @param   int     $height        checkerboard height
	 * @param   string  $checkerboard  Layout name for checkerboard, if not specified default is used
	 *
	 * @return string
	 */
	public static function getSVGCheckerboard($width = 800, $height = 600, $checkerboard = 'default')
	{
		$svgOutput = RLayoutHelper::render('svg.checkboard-' . $checkerboard, array(
				'width' => $width,
				'height' => $height,
			)
		);

		return trim(preg_replace('/\s+/', ' ', $svgOutput));
	}

	/**
	 * Returns Unit to Pixel radio
	 *
	 * @param   string  $unit  Unit name
	 *
	 * @return string
	 */
	public static function getUnitToPixelsRatio($unit)
	{
		if (empty($unit))
		{
			$config = ReddesignEntityConfig::getInstance();
			$unit = empty($unit) ? $config->getUnit() : $unit;
		}

		switch ($unit)
		{
			case 'cm':
				return '28.346456514';
			case 'mm':
				return '2.834645651';
			default:
				return '1';
		}
	}

	/**
	 * Returns Unit conversion ratio
	 * Calculate width and height in the selected unit at the configuration. 1 inch = 25.4 mm
	 *
	 * @param   string  $unit       Unit name
	 * @param   float   $sourceDpi  Source DPI
	 *
	 * @return string
	 */
	public static function getUnitConversionRatio($unit, $sourceDpi)
	{
		if (empty($unit) || empty($sourceDpi))
		{
			$config = ReddesignEntityConfig::getInstance();
			$unit = empty($unit) ? $config->getUnit() : $unit;
			$sourceDpi = empty($sourceDpi) ? $config->getSourceDpi() : $sourceDpi;
		}

		switch ($unit)
		{
			case 'cm':
				return $sourceDpi / 2.54;
			case 'mm':
				return $sourceDpi / 25.4;
			case 'px':
			default:
				return '1';
		}
	}
}
