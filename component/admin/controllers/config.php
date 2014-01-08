<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Font Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerConfig extends RControllerForm
{
	/**
	 * The URL view item variable.
	 *
	 * @var  string
	 */
	protected $view_item = 'config';

	/**
	 * The URL view list variable.
	 *
	 * @var  string
	 */
	protected $view_list = 'designtypes';

	/**
	 * Method to check whether an ID is in the edit list.
	 *
	 * @param   string   $context  The context for the session storage.
	 * @param   integer  $id       The ID of the record to add to the edit list.
	 *
	 * @return  boolean  True if the ID is in the edit list.
	 */
	protected function checkEditId($context, $id)
	{
		return true;
	}
}
