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

		// Filter orders according to plugin parameter
		$plugin = &JPluginHelper::getPlugin('redshop_product', 'reddesign_orders');
		$pluginParams = new JRegistry($plugin->params);
		$order_filter = $pluginParams->get('order_filter', '1');

		if ($order_filter)
		{
			$this->orders   = $model->getPaidOrders($this->orders);
		}

		parent::display();
	}
}
