<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.application.component.controller');

/**
 * RedDesign Main Controller
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       2.0
 */
class ReddesignController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since	2.0
	 */
	protected $default_view = 'Panel';

	/**
	 * Display task
	 *
	 * @return void
	 */
	function display()
	{
		// Call parent behavior
		parent::display();
	}
}
