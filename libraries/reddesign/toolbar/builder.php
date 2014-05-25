<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Class helping to build toolbars.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
final class ReddesignToolbarBuilder
{
	/**
	 * Create categories button.
	 *
	 * @param   string  $url        Url to the view which will be displayed in the lightbox.
	 * @param   string  $text       The button text.
	 * @param   string  $iconClass  The icon class.
	 * @param   string  $class      The button class.
	 * @param   string  $rel        Rel attribute added to the hyperlink tag.
	 *
	 * @return  ReddesignToolbarButtonLightbox  The button.
	 */
	public static function createLightboxButton($url, $text, $iconClass, $class = '', $rel = '')
	{
		return new ReddesignToolbarButtonLightbox($url, $text, $iconClass, $class, $rel);
	}
}
