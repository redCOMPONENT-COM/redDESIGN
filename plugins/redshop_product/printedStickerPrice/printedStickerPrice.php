<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product.printedStickerPrice
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
class PlgRedshop_ProductPrintedStickerPrice extends JPlugin
{
	/**
	 * On Prepare redSHOP Product
	 *
	 * @param   string  &$template  Product Template Data
	 * @param   array   &$params    redSHOP Params list
	 * @param   object  $product    Product Data Object
	 *
	 * @return  boolean
	 */
	public function onPrepareProduct(&$template, &$params, $product)
	{
		$input         = JFactory::getApplication()->input;
		$view          = $input->get('view');
		$document      = JFactory::getDocument();
		$productHelper = new producthelper;
		$extraField = new extraField;

		$extraFieldData = $extraField->getSectionFieldDataList(5, 1, $product->product_id);

		if ($extraFieldData->data_txt != 'type2')
		{
			return false;
		}

		// Settlement to load attribute.js after printedStickerPrice.js
		unset($document->_scripts[JURI::root(true) . '/components/com_redshop/assets/js/attribute.js']);

		$document->addScript('plugins/redshop_product/printedStickerPrice/js/printedStickerPrice.js');

		// Adding script using this way because in redSHOP is using this code
		JHTML::Script('attribute.js', 'components/com_redshop/assets/js/', false);

		if ($view != 'product' )
		{
			return false;
		}

		$extraFieldData = $extraField->getSectionFieldDataList(6, 1, $product->product_id);
		$minWidth		= str_replace(",", ".", $extraFieldData->data_txt);
		$extraFieldData = $extraField->getSectionFieldDataList(7, 1, $product->product_id);
		$minHeight		= str_replace(",", ".", $extraFieldData->data_txt);

		$extraFieldData = $extraField->getSectionFieldDataList(8, 1, $product->product_id);
		$maxWidth		= str_replace(",", ".", $extraFieldData->data_txt);
		$extraFieldData = $extraField->getSectionFieldDataList(9, 1, $product->product_id);
		$maxHeight		= str_replace(",", ".", $extraFieldData->data_txt);

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
		$table .= '<td class="td_center">
					<input type="text"
							id="plg_dimension_base_input_' . $product->product_id . '"
							name="plg_dimension_base_input"
							size="5"
							value="' . str_replace(".", ",", $minWidth) . '"
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

				if (jsProductPrice && dpAllow)
				{
					return '&plg_product_price=' + jsProductPrice;
				}
				else
				{
					alert('" . sprintf(JText::_('PLG_REDSHOP_PRODUCT_DISCOUNT_CALCULATOR_REQUIRED_MINIMUM_HEIGHT'), $minWidth) . "');
				}
			}
		";

		$document->addScriptDeclaration($getExtraParamsJS);

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
				$productPrice = $product->product_price + $productHelper->getProductTax($product->product_id, $product->product_price);

				$table .= "<tr>"
					. "<td>1</td>"
					. "<td>"
					. "<input type='radio' class='printedStickerPrice_radio' name='printedStickerPrice_plg'
							value='1' price=\"$productPrice\"
							product_id=\"$product->product_id\"
							percentage='0'
							index='0'
							checked='checked' >"
					. "</td>"
					. "<td><span id='price_quantity0'></span></td><td>&nbsp;</td>"
					. "</tr>";
			}

			$percentage = round($price->product_price, 2);

			$index = $i + 1;

			$table .= "<tr>"
				. "<td>$price->price_quantity_end</td>"
				. "<td>"
				. "<input type='radio' class='printedStickerPrice_radio' name='printedStickerPrice_plg'
						value=\"$price->price_quantity_end\" price='0'
						product_id=\"$product->product_id\"
						percentage='" . $percentage / 100 . "'
						index='" . $index . "'
						>"
				. "</td>"
				. "<td><span id='price_quantity" . $index . "'></span></td>"
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
	 * @throws   string  If Query fails it will throw error
	 *
	 * @return  array    Price information object list
	 */
	private function getProductQuantityPrice($product_id)
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
