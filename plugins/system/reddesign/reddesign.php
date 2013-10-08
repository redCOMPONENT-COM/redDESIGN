<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.RedDESIGN
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * System plugin for redDESIGN
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 * @since       1.0
 */
class PlgSystemRedDESIGN extends JPlugin
{
	/**
	 * This event is triggered immediately before pushing the document buffers into the template placeholders,
	 * retrieving data from the document and pushing it into the into the JResponse buffer.
	 * http://docs.joomla.org/Plugin/Events/System
	 *
	 * @return boolean
	 */
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();

		// No need to remove from administrator
		if ($app->isAdmin())
		{
			return;
		}

		$doc = JFactory::getDocument();

		if ($doc->_scripts)
		{
			// Settlement to load attribute.js after quantity_discount.js
			unset($doc->_scripts[JURI::root(true) . '/components/com_redshop/assets/js/attribute.js']);
		}
	}
}
