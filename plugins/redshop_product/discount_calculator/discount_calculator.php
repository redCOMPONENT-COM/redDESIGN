<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product.Discount_Calculator
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * RedShop_Product Plugin for calculate discount in redSHOP product
 *
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product
 * @since       1.0
 */
class PlgRedshop_ProductDiscount_Calculator extends JPlugin
{
	/**
	 * On Prepare redSHOP Product
	 *
	 * @param   string  &$template  Product Template Data
	 * @param   array   &$params    redSHOP Parameter list
	 * @param   object  $product    Product Data Object
	 *
	 * @return  mixed
	 */
	public function onPrepareProduct(&$template, &$params, $product)
	{
		$input         = JFactory::getApplication()->input;
		$view          = $input->get('view');
		$document      = JFactory::getDocument();
		$extraField    = new extraField;

		$extraFieldData = $extraField->getSectionFieldDataList(5, 1, $product->product_id);

		if ($extraFieldData->data_txt != 'type1')
		{
			return false;
		}

		// Settlement to load attribute.js after quantity_discount.js
		unset($document->_scripts[JURI::root(true) . '/components/com_redshop/assets/js/attribute.js']);

		$document->addScript('plugins/redshop_product/discount_calculator/js/discount_calculator.js');

		// Adding script using this way because in redSHOP is using this code
		JHTML::Script('attribute.js', 'components/com_redshop/assets/js/', false);

		if ($view != 'product')
		{
			return false;
		}

		$table = '';
		$table .= '<table>';
		$table .= '<tr>';
		$table .= '<td class="td_first">
					<div class="bgSelect">
						<select name="plg_dimension_base" id="plg_dimension_base_' . $product->product_id . '">
							<option value="w">' . JText::_("COM_REDSHOP_WIDTH") . '</option>
							<option value="h">' . JText::_("COM_REDSHOP_HEIGHT") . '</option>
						</select>
					</div>
				</td>';

		$extraFieldData = $extraField->getSectionFieldDataList(6, 1, $product->product_id);
		$minWidth		= str_replace(",", ".", $extraFieldData->data_txt);
		$extraFieldData = $extraField->getSectionFieldDataList(7, 1, $product->product_id);
		$minHeight		= str_replace(",", ".", $extraFieldData->data_txt);

		$extraFieldData = $extraField->getSectionFieldDataList(8, 1, $product->product_id);
		$maxWidth		= str_replace(",", ".", $extraFieldData->data_txt);
		$extraFieldData = $extraField->getSectionFieldDataList(9, 1, $product->product_id);
		$maxHeight		= str_replace(",", ".", $extraFieldData->data_txt);

		$table .= '<td class="td_center">
					<input type="text"
							id="plg_dimension_base_input_' . $product->product_id . '"
							name="plg_dimension_base_input"
							size="5"
							value="' . str_replace(".", ",", round($minWidth, 2)) . '"
							maxlength="5"
							default-width="' . $minWidth . '"
							default-height="' . $minHeight . '"
							max-width="' . $maxWidth . '"
							max-height="' . $maxHeight . '">
					<span id="plg_default_volume_unit_' . $product->product_id . '">' . DEFAULT_VOLUME_UNIT . '</span>
				</td>';
		$table .= '<td class="td_last">
					<span id="plg_dimension_log_' . $product->product_id . '">'
					. str_replace(".", ",", $minWidth) . ' X ' . str_replace(".", ",", $minHeight) . ' ' . DEFAULT_VOLUME_UNIT
					. '</span>
					<input type="hidden" name="plg_dimension_width" value="' . $minWidth . '" id="plg_dimension_width_' . $product->product_id . '">
					<input type="hidden" name="plg_dimension_height" value="' . $minHeight . '" id="plg_dimension_height_' . $product->product_id . '">
					<input type="hidden" name="plg_product_price" value="' . $product->product_price . '" id="plg_product_price_' . $product->product_id . '">
				</td>';
		$table .= '</tr>';
		$table .= '</table>';

		$template = str_replace('{discount_calculator_plg}', $table, $template);

		$getExtraParamsJS = "
			var dpAllow = false;

			function getExtraParams(frm)
			{
				var jsProductPrice = rsjQuery('input[id^=\"plg_product_price_\"]').val();

				if (jsProductPrice)
				{
					return '&plg_product_price=' + jsProductPrice;
				}
			}
		";

		$document->addScriptDeclaration($getExtraParamsJS);
	}

	/**
	 * Discount Calculator check session cart for same product
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$cart  Cart session array
	 * @param   array  $data   Cart Data
	 *
	 * @return  boolean
	 */
	public function checkSameCartProduct(&$cart, $data)
	{
		return true;
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$cart  Cart session array
	 * @param   array  $data   Cart Data
	 *
	 * @return  boolean
	 */
	public function onBeforeSetCartSession(&$cart, $data)
	{
		if ( !isset($data['plg_product_price']) )
		{
			return;
		}

		$i = $cart['idx'];

		$cart[$i]['product_old_price']  		= $data['plg_product_price'];
		$cart[$i]['product_old_price_excl_vat'] = $data['plg_product_price'];
		$cart[$i]['product_price_excl_vat']     = $data['product_price'] + $data['product_old_price_excl_vat'];
		$productVat								= $cart[$i]['product_price'] * 0.25;
		$cart[$i]['product_price']              = $data['product_price'] + $productVat;
		$cart[$i]['product_vat'] 				= $productVat;

		// Set product custom price
		$cart['plg_product_price'][$cart[$i]['product_id']] = $data['plg_product_price'];

		return;
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$data  Post Data
	 *
	 * @return  boolean
	 */
	public function onAfterBaseProductPriceSet(&$data)
	{
		if ( !isset($data['plg_product_price']) )
		{
			return;
		}

		$data['product_price']              = $data['plg_product_price'];

		return;
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$cartArr  Cart session array
	 * @param   int    $index     Cart index
	 *
	 * @return  boolean
	 */
	public function onBeforeLoginCartSession(&$cartArr, $index)
	{
		$i = $index;

		if ( !isset($cartArr['plg_product_price'][$cartArr[$i]['product_id']]))
		{
			return;
		}

		$cartArr[$i]['product_price_excl_vat']  = $cartArr[$i]['product_old_price_excl_vat'];
		$productVat								= $cartArr[$i]['product_old_price'] * 0.25;
		$cartArr[$i]['product_price']           = $cartArr[$i]['product_old_price_excl_vat'] + $productVat;
		$cartArr[$i]['product_vat'] 			= $productVat;

		return;
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$cart  Cart session array
	 * @param   int    $i      Cart index
	 * @param   array  $data   Cart Data
	 *
	 * @return  boolean
	 */
	public function onAfterCartItemUpdate(&$cart, $i, $data)
	{
		if ( !isset($cart['plg_product_price'][$cart[$i]['product_id']]))
		{
			return;
		}

		$cart[$i]['product_old_price'] = $cart['plg_product_price'][$cart[$i]['product_id']];
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array  &$cart              Cart session array
	 * @param   int    $i                  Cart index
	 * @param   float  &$calculator_price  Product price
	 *
	 * @return  boolean
	 */
	public function onBeforeCartItemUpdate(&$cart, $i, &$calculator_price)
	{
		$dimension = $cart[$i]['rs_dimension'];
		$quantity  = $cart[$i]['quantity'];
		$productId = $cart[$i]['product_id'];

		// Get difference type of calculations
		$calculator_price = $this->getTypesCalculation($dimension, $productId, $quantity);

		$cart['plg_product_price'][$productId] = $calculator_price;
	}

	/**
	 * Get Product Quantity prices based on shopper group of current logged in user
	 *
	 * @param   integer  $product_id  Product Id
	 * @param   integer  $quantity    Cart quantity
	 *
	 * @throws   string  If Query fails it will throw error
	 *
	 * @return  array    Price information object list
	 */
	private function getProductQuantityPrice($product_id, $quantity)
	{
		require_once JPATH_SITE . '/components/com_redshop/helpers/user.php';

		$user_helper    = new rsUserhelper;
		$user           = JFactory::getUser();
		$user_id        = $user->id;
		$shopperGroupId = $user_helper->getShopperGroup($user_id);

		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('p.*')
			->from($db->quoteName('#__redshop_product_price', 'p'))
			->where($db->quoteName('p.product_id') . ' = ' . (int) $product_id)
			->where($db->quoteName('p.shopper_group_id') . ' = ' . (int) $shopperGroupId)
			->where($db->quoteName('p.price_quantity_start') . ' <= ' . (int) $quantity)
			->where($db->quoteName('p.price_quantity_end') . ' >= ' . (int) $quantity)
			->order($db->quoteName('p.price_quantity_start') . ' ASC');

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$prices = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		return $prices;
	}

	/**
	 * Discount Calculator update cart session variables
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param   array    &$cart  Cart session array
	 * @param   array    $data   Cart Data
	 * @param   integer  $i      Index Variable
	 *
	 * @return  boolean
	 */
	public function onSameCartProduct(&$cart, $data,$i)
	{
		if ( !isset($data['plg_product_price']) )
		{
			return;
		}

		$cart[$i]['product_price']              = $data['plg_product_price'];
		$cart[$i]['product_old_price']          = $data['plg_product_price'];
		$cart[$i]['product_old_price_excl_vat'] = $data['plg_product_price'];
		$cart[$i]['product_price_excl_vat']     = $data['plg_product_price'];

		// Set product custom price
		$cart['plg_product_price'][$cart[$i]['product_id']] = $data['plg_product_price'];
	}

	/**
	 * update cart session variables
	 *
	 * Method is called by the redSHOP product frontend helper from getProductNetPrice function
	 *
	 * @param   integer  $product_id  The product id
	 *
	 * @return  int/boolean  return product price if success else return false
	 */
	public function setProductCustomPrice($product_id)
	{
		$session = JFactory::getSession();
		$cart    = $session->get('cart');
		$result  = false;

		if (!empty($cart['plg_product_price']))
		{
			$prices = $cart['plg_product_price'];
		}

		if (isset( $prices[$product_id]) )
		{
			$result = $prices[$product_id];
		}

		return $result;
	}

	/**
	 * Get Different Type of Calculations
	 *
	 * @param   string   $dimension  Product Dimension
	 * @param   integer  $productId  Product Id
	 * @param   integer  $quantity   Product Item Quantity
	 *
	 * @return  float    Product final calculated Price
	 */
	private function getTypesCalculation($dimension, $productId, $quantity)
	{
		$extraField 	= new extraField;
		$extraFieldData = $extraField->getSectionFieldDataList(5, 1, $productId);

		$calculator_price = 0;

		// Type 2 calculation
		if ($extraFieldData->data_txt == 'type2')
		{
			if (isset($dimension) && $dimension)
			{
				$chars = preg_split('/ /', $dimension, -1, PREG_SPLIT_OFFSET_CAPTURE);

				$lang = JFactory::getLanguage();
				$lang->load('plg_redshop_product_addToCartValidation', JPATH_ADMINISTRATOR);

				// Width X Height Unit
				$width 		= $chars[0][0];
				$height 	= $chars[2][0];
			}
			else
			{
				return 0;
			}

			$elements        = 39;
			$meterPerPrice   = $width * $height / 10000;
			$meterTotalPrice = $meterPerPrice * $quantity;

			$meters = array(
				0 => 703.5,
				1 => 646.8,
				2 => 617.4,
				3 => 580.3,
				4 => 536.2,
				5 => 492.1,
				10 => 387.8,
				20 => 385.7,
				50 => 378.0,
				999 => 349.3
			);

			$pricePerMeter = 0;

			foreach ($meters as $key => $value)
			{
				if ($key >= floor($meterTotalPrice))
				{
					$pricePerMeter = $value;
					break;
				}
			}

			$totalPricePerMeter = $pricePerMeter * $meterTotalPrice;

			$quntityDiscount = array(
				0 => 1,
				5 => 0.9,
				10 => 0.85,
				25 => 0.80,
				50 => 0.75,
				500 => 0.70
			);

			$discountAmount = 0;

			foreach ($quntityDiscount as $key => $value)
			{
				if ($key >= $elements)
				{
					$discountAmount = $value;
					break;
				}
			}

			// Price Per Piece
			$pricePerPiece = $totalPricePerMeter / $quantity * $discountAmount + $elements / $quantity;
			$price = $this->getProductQuantityPrice($productId, $quantity);

			$percentage       = round($price->product_price, 2);
			$calculator_price = $pricePerPiece - abs($pricePerPiece * $percentage / 100);
		}

		return $calculator_price;
	}

	/**
	 * This event will trigger when need to reorder the product
	 *
	 * @param   array  &$orderItem  Current Order Item row
	 *
	 * @return  void
	 */
	public function onReorderCartItem(&$orderItem)
	{
		$dimension = $this->getOrderItemDimention($orderItem);
		$orderItem['rs_dimension'] = $dimension;

		$quantity  = $orderItem['product_quantity'];
		$productId = $orderItem['product_id'];

		// Get difference type of calculations
		$calculator_price = $this->getTypesCalculation($dimension, $productId, $quantity);

		$orderItem['plg_product_price'] = $calculator_price;
	}

	/**
	 * Get Product Dimension from the order item.
	 *
	 * @param   array  $orderItem  Order Item information
	 *
	 * @return  string              Dimension Text
	 */
	private function getOrderItemDimention($orderItem)
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create a sub-query for the subcategory list
		$subQuery = $db->getQuery(true);
		$subQuery->select('field_id')
				->from('#__redshop_fields')
				->where($db->quoteName('field_name') . ' LIKE "rs_dimension"');

		// Create the base select statement.
		$query->select('fd.data_txt')
			->from($db->quoteName('#__redshop_fields_data') . ' AS fd')
			->where($db->quoteName('fd.itemid') . ' = ' . (int) $orderItem['order_item_id'])
			->where($db->quoteName('fd.section') . ' = 12');

		// Add the sub-query to the main query
		$query->where($db->quoteName('fd.fieldid') . ' IN (' . $subQuery->__toString() . ')');

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$dataTxt = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		return $dataTxt;
	}
}
