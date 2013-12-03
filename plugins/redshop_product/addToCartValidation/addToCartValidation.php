<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product.addToCartValidation
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;
jimport('joomla.plugin.plugin');

/**
 * RedShop_Product Plugin for Quantity based Discount in redSHOP product
 *
 * @package     Joomla.Plugin
 * @subpackage  RedShop_Product
 * @since       1.0
 */
class PlgRedshop_ProductaddToCartValidation extends JPlugin
{
	/**
	 * On Prepare redSHOP Product
	 *
	 * @param   array  $post  Posted data
	 *
	 * @return  void
	 */
	public function onBeforeAddProductToCart($post)
	{
		if (isset($post['rs_dimension']) && $post['rs_dimension'])
		{
			$chars = preg_split('/ /', $post['rs_dimension'], -1, PREG_SPLIT_OFFSET_CAPTURE);

			$lang = JFactory::getLanguage();
			$lang->load('plg_redshop_product_addToCartValidation', JPATH_ADMINISTRATOR);

			// Width X Height Unit
			$width 		= $chars[0][0];
			$height 	= $chars[2][0];
			$extraField = new extraField;

			$extraFieldData = $extraField->getSectionFieldDataList(6, 1, $post['product_id']);
			$minWidth		= $extraFieldData->data_txt;
			$extraFieldData = $extraField->getSectionFieldDataList(7, 1, $post['product_id']);
			$minHeight		= $extraFieldData->data_txt;

			if ($minWidth || $minHeight)
			{
				if ($width < $minWidth || $height < $minHeight)
				{
					echo "`0`";
					echo sprintf(JText::_("PLG_REDSHOP_MINIMUM_WIDTH_AND_HEIGHT"), $minWidth, $minHeight);
					exit;
				}
			}

			$extraFieldData = $extraField->getSectionFieldDataList(8, 1, $post['product_id']);
			$maxWidth		= $extraFieldData->data_txt;
			$extraFieldData = $extraField->getSectionFieldDataList(9, 1, $post['product_id']);
			$maxHeight		= $extraFieldData->data_txt;

			if ($maxWidth || $maxHeight)
			{
				if ($width > $maxWidth || $height > $maxHeight)
				{
					echo "`0`";
					echo sprintf(JText::_("PLG_REDSHOP_MAXIMUM_WIDTH_AND_HEIGHT"), $maxWidth, $maxHeight);
					exit;
				}
			}
		}
	}
}
