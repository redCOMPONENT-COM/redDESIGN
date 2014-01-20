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
}
