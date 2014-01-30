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
 * Cliparts Controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 * @since       1.0
 */

class ReddesignControllerCliparts extends RControllerAdmin
{
	/**
	 * Method to redirect to Joomla Categories View
	 *
	 * @return  null
	 */
	public function categories()
	{
		$this->setRedirect('index.php?option=com_categories&view=categories&extension=com_reddesign');
	}
}
