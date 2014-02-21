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
 * Area helper.
 *
 * @package     Reddesign.Libraries
 * @subpackage  Helpers
 *
 * @since       1.0
 */
final class ReddesignHelpersBackground
{
	/**
	 * Check if thumbnail exists and if it is right size.
	 * And creates thumbnail if it doesn't pass the check.
	 *
	 * @param   string  $backgroundFileName  Background file name.
	 *
	 * @return  string  Path to the thumbnail image.
	 */
	public static function getThumbnail($backgroundFileName)
	{
		// Remove .svg extension
		$backgroundName = substr($backgroundFileName, 0, -4);

		// Check if thumbnail file exists.
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundName . '.png'))
		{
			$config = ReddesignEntityConfig::getInstance();
			$maxThumbWidth = $config->getBgThumbnailWidth();
			$maxThumbHeight = $config->getBgThumbnailHeight();

			$thumb = new Imagick;
			$thumb->readImage(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundName . '.png');
			$measures = $thumb->getImageGeometry();

			if ($measures['width'] != $maxThumbWidth && $measures['height'] != $maxThumbHeight)
			{
				return self::createThumbnail($backgroundFileName);
			}
			else
			{
				return JURI::root() . 'media/com_reddesign/backgrounds/' . $backgroundName . '.png';
			}
		}
		else
		{
			return self::createThumbnail($backgroundFileName);
		}
	}

	/**
	 * Creates thumbnail.
	 *
	 * @param   string  $backgroundFileName  Background file name.
	 *
	 * @return  string  Path to the thumbnail image.
	 */
	private static function createThumbnail($backgroundFileName)
	{
		// Remove .svg extension
		$backgroundName = substr($backgroundFileName, 0, -4);
		$config = ReddesignEntityConfig::getInstance();
		$maxThumbWidth = $config->getBgThumbnailWidth();
		$maxThumbHeight = $config->getBgThumbnailHeight();

		$im = new Imagick;
		$im->setBackgroundColor(new ImagickPixel('transparent'));
		$svg = file_get_contents(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundFileName);
		$im->readImageBlob($svg);
		$im->setImageFormat("png32");
		$im->resizeImage($maxThumbWidth, $maxThumbHeight, Imagick::FILTER_LANCZOS, 1, true);
		$im->writeImage(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundName . '.png');
		$im->destroy();

		return JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundName . '.png';
	}
}
