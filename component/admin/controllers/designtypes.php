<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Design Types Controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class ReddesignControllerDesigntypes extends RControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 */
	public function __construct()
	{
		parent::__construct();

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}
}
