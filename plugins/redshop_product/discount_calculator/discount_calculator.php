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

		$document->addScript('plugins/redshop_product/discount_calculator/js/discount_calculator.js');

		// Adding script using this way because in redSHOP is using this code
		JHTML::Script('attribute.js', 'components/com_redshop/assets/js/', false);

		if ($view != 'product')
		{
			return;
		}

		$table = '';
		$table .= '<table>';
		$table .= '<tr>';
		$table .= '<td>
					<select name="plg_dimention_base" id="plg_dimention_base_' . $product->product_id . '">
						<option value="w">' . JText::_("COM_REDSHOP_WIDTH") . '</option>
						<option value="h">' . JText::_("COM_REDSHOP_HEIGHT") . '</option>
					</select>
				</td>';
		$table .= '<td>
					<input type="text"
							id="plg_dimention_base_input_' . $product->product_id . '"
							name="plg_dimention_base_input"
							size="5"
							maxlength="5"
							default-width="' . $product->product_width . '" default-height="' . $product->product_height .
					'">
					<span id="plg_default_volume_unit_' . $product->product_id . '">' . DEFAULT_VOLUME_UNIT . '</span>
				</td>';
		$table .= '<td>
					<span id="plg_dimention_log_' . $product->product_id . '">' .
						$product->product_width . ' X ' . $product->product_height . ' ' . DEFAULT_VOLUME_UNIT .
					'</span>
					<input type="hidden" name="plg_dimention_width" value="' . $product->product_width . '" id="plg_dimention_width_' . $product->product_id . '">
					<input type="hidden" name="plg_dimention_height" value="' . $product->product_height . '" id="plg_dimention_height_' . $product->product_id . '">
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

				if (jsProductPrice && dpAllow)
				{
					return '&plg_product_price=' + jsProductPrice;
				}
				else
				{
					alert('" . sprintf(JText::_('PLG_REDSHOP_PRODUCT_DISCOUNT_CALCULATOR_REQUIRED_MINIMUM_HEIGHT'), $product->product_width) . "');
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

		$cart[$i]['product_price']              = $data['plg_product_price'];
		$cart[$i]['product_old_price']          = $data['plg_product_price'];
		$cart[$i]['product_old_price_excl_vat'] = $data['plg_product_price'];
		$cart[$i]['product_price_excl_vat']     = $data['plg_product_price'];

		// Set product custom price
		$cart['plg_product_price'][$cart[$i]['product_id']] = $data['plg_product_price'];

		return;
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
}
