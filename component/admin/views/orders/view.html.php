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
 * Accessory View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewOrders extends FOFViewHtml
{
	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$this->input->setVar('hidemainmenu', true);

		$model    = $this->getModel();
		$this->orders   = $model->getItemList();

		if (count($this->orders) > 0)
		{
			JPluginHelper::importPlugin('redshop_product', 'reddesign_orders');
			$dispatcher = JDispatcher::getInstance();
			$reddesignOrders = $dispatcher->trigger('onOrderFilterOrders');
			$this->orders = $reddesignOrders[0];
		}

		parent::display();
	}
}
