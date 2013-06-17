<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_redesign
 * @since          1.6
 */
class JFormFieldFontSize extends JFormFieldList
{
	/**
	 * A Fontsize list that respects access controls
	 *
	 * @var        string
	 * @since    1.6
	 */
	public $type = 'FontSize';

	/**
	 * Method to get a list of fontsize that respects access controls
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$optionfontsize = array();
		for ($j = 1; $j <= 50; $j++)
		{
			$fontsizename     = $j . "pt";
			$optionfontsize[] = JHTML::_('select.option', $fontsizename, $fontsizename);
		}

		return $optionfontsize;
	}
}
