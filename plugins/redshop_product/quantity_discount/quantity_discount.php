<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product.Quantity_Discount
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * RedShop_Product Plugin for Quantity based Discount in redSHOP product
 *
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product
 * @since       1.0
 */
class PlgRedshop_ProductQuantity_Discount extends JPlugin
{
	/**
	 * On Prepare redSHOP Product
	 *
	 * @param   string  &$template  Product Template Data
	 * @param   array   &$params    redSHOP Params list
	 * @param   object  $product    Product Data Object
	 *
	 * @return  void
	 */
	public function onPrepareProduct(&$template, &$params, $product)
	{
		$input         = JFactory::getApplication()->input;
		$view          = $input->get('view');
		$document      = JFactory::getDocument();
		$productHelper = new producthelper;

		// Settlement to load attribute.js after quantity_discount.js
		unset($document->_scripts[JURI::root(true) . '/components/com_redshop/assets/js/attribute.js']);

		$document->addScript('plugins/redshop_product/quantity_discount/js/quantity_discount.js');

		// Adding script using this way because in redSHOP is using this code
		JHTML::Script('attribute.js', 'components/com_redshop/assets/js/', false);

		if ($view != 'product')
		{
			return;
		}

		$prices = $this->getProductQuantityPrice($product->product_id);

		$table = "<table>"
			. "<tr>"
				. "<th colspan='2'>" . JText::_('COM_REDSHOP_QUANTITY') . "</th>"
				. "<th colspan='2'>" . JText::_('COM_REDSHOP_PRICE') . "</th>"
			. "</tr>";

		for ($i = 0, $n = count($prices); $i < $n; $i++)
		{
			$price = $prices[$i];

			if ($i == 0)
			{
				$table .= "<tr>"
					. "<td>"
					. "<input type='radio' class='quantity_discount_radio' name='quantity_discount_plg'
							value='1' product_id=\"$product->product_id\" >"
					. "</td>"
					. "<td>1</td>"
					. "<td>" . $productHelper->getProductFormattedPrice($product->product_price) . "</td>"
					. "</tr>";
			}

			$difference = $product->product_price - $price->product_price;
			$percentage = round((100 * $difference) / $product->product_price, 2);

			$table .= "<tr>"
				. "<td>"
				. "<input type='radio' class='quantity_discount_radio' name='quantity_discount_plg'
						value=\"$price->price_quantity_end\" product_id=\"$product->product_id\" >"
				. "</td>"
				. "<td>$price->price_quantity_end</td>"
				. "<td>" . $productHelper->getProductFormattedPrice($price->product_price * $price->price_quantity_end) . "</td>"
				. "<td>$percentage%</td>"
				. "</tr>";
		}

		$table .= "</table>";

		$template = str_replace('{product_price_table_plugin}', $table, $template);
	}

	/**
	 * Get Product Quantity prices based on shopper group of current logged in user
	 *
	 * @param   integer  $product_id  Product Id
	 *
	 * @return  array    Price information object list
	 */
	private function getProductQuantityPrice($product_id)
	{
		require_once JPATH_SITE . '/components/com_redshop/helpers/user.php';

		$session     = JFactory::getSession();
		$user_helper = new rsUserhelper;
		$user        = JFactory::getUser();
		$user_id     = $user->id;

		$shopperGroupId = $user_helper->getShopperGroup($user_id);

		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('p.*')
			->from($db->quoteName('#__redshop_product_price', 'p'))
			->where($db->quoteName('p.product_id') . ' = ' . (int) $product_id)
			->where($db->quoteName('p.shopper_group_id') . ' = ' . (int) $shopperGroupId)
			->order($db->quoteName('p.price_quantity_start') . ' ASC');

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$prices = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		return $prices;
	}
}
