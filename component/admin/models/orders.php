<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Orders Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelOrders extends FOFModel
{
	/**
	 * fetch Only Confirmed and Paid Orders
	 *
	 * @param   array  $orders  Array populated with reddesign Order data.
	 *
	 * @return  array Information of Paid and confirmed orders
	 */
	public function getPaidOrders($orders)
	{
		$db     = JFactory::getDbo();
		$orderId = array();

		foreach ($orders as $order)
		{
			$orderId[] = $order->redshop_order_id;
		}

		$query = $db->getQuery(true);
		$query->select(array('o.order_id', 'ro.*'));
		$query->from('#__redshop_orders as o');
		$query->join('INNER', '#__reddesign_orders AS ro ON (o.order_id = ro.redshop_order_id)');
		$query->where('o.order_status = "C" and o.order_payment_status = "Paid"');
		$query->order('o.order_id DESC');
		$db->setQuery($query);
		$reddesignOrders = $db->loadObjectList();

		return $reddesignOrders;
	}
}
