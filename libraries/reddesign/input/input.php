<?php
/**
 * @package     RedDESIGN.Library
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Input class.
 *
 * @package     RedDESIGN.Library
 * @subpackage  Input
 * @since       1.0
 */
final class ReddesignInput
{
	/**
	 * Get a field value from a jform.
	 *
	 * @param   string  $name     The input name
	 * @param   mixed   $default  The default value
	 *
	 * @return  mixed  The value or the default value
	 */
	public static function getField($name, $default = null)
	{
		$input = JFactory::getApplication()->input;
		$form = $input->get('jform', array(), 'array');

		if (isset($form[$name]))
		{
			return $form[$name];
		}

		return $default;
	}
}
