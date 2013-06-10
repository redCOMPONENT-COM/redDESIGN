<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Design editor Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignControllerReddesign extends FOFController
{
	/**
	 * Default task of the component is Browse
	 *
	 * @param   string  $task  task to be executed by the controller
	 *
	 * @return bool|null|void
	 */
	public function execute($task)
	{
		parent::execute('browse');
	}
}
