<?php
/**
 * @package    Reddesign.Admin
 *
 * @copyright  Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Front controller.
 *
 * @package  Reddesign.Admin
 * @since    1.0
 */
class ReddesignController extends JControllerLegacy
{
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'designtypes'));
		$input->set('task', $input->get('task', 'display'));

		return parent::display($cachable, $urlparams);
	}
}