<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/order.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/order.php';
}

if (JFile::exists(JPATH_SITE . '/components/com_redshop/helpers/product.php'))
{
	require_once JPATH_SITE . '/components/com_redshop/helpers/product.php';
}
/**
 * redDesign Order Plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgRedshop_ProductReddesign_Orders extends JPlugin
{
	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @access  public
	 *
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Plugin method for event on building new product for redSHOP.
	 *
	 * @param   array  $cart  An array that holds cart information
	 *
	 * @param   array  $order  An array that holds order information
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function afterOrderPlace($cart,$order)
	{
		$app    = JFactory::getApplication();
		$db     = JFactory::getDbo();
		$params = $this->params;
		$session = JFactory::getSession();

		$order_functions = new order_functions;
		$producthelper = new producthelper;
		$orderProducts = $order_functions->getOrderItemDetail($order->order_id);

		$productIds = array();
		$productionFiles = array();

		foreach ($orderProducts as $orderProduct)
		{
			$product = $producthelper->getProductById($orderProduct->product_id);
			if (strstr($product->product_number , "redDESIGN"))
			{
			
				$productionFile = pathinfo($product->product_full_image);
				$productionFile = "reddesign" . $productionFile['filename'] . '.' . pdf;
				$productIds[] = $orderProduct->product_id;
				$productNumbers[] = $orderProduct->order_item_sku;
				$productionFiles[] = $productionFile;

			} 
		}
		if(count($productIds)>0)
		{
			$newOrderProduct = new stdClass;
			$newOrderProduct->redshop_order_id = $order->order_id;
			$newOrderProduct->redshop_product_id = implode(",", $productIds);
			$newOrderProduct->redshop_product_number = implode(",", $productNumbers);
			$newOrderProduct->reddesign_productionfile = implode(",", $productionFiles);
			$result = $db->insertObject('#__reddesign_orders', $newOrderProduct);		
			if (!$result)
			{
				$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDDESIGN_ORDERS_NOT_SAVED'), 'notice');
				return false;
			}
		}
		return true;
	}
}

