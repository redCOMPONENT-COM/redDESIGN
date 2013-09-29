<?php
/**
 * @package     RedSHOP
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class plgredshop_productbackgrounds extends JPlugin
{
	/**
	 * attribute[$k][property][$g][<plugin_custome_variable>]
	 *
	 * @param   object  &$property  Attribute Property Data
	 *
	 * @return  void
	 */
	public function onAttributePropertyPrepareLoop(&$property)
	{
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel');
		$backgrounds     = $backgroundModel->getItemList();

		$selectOption = array();
		$selectOption[] = JHtml::_('select.option', 0, JText::_('JSELECT'), 'reddesign_background_id', 'title');

		$backgrounds = array_merge($selectOption, $backgrounds);

		$backgroundBox = JHtml::_('select.genericlist', $backgrounds, 'attribute[' . $property->k . '][property][' . $property->g . '][backgrounds]', '', 'reddesign_background_id', 'title', 0);

		$html = '<td>
					<div><span>' . JText::_('PLG_REDSHOP_PRODUCT_BACKGROUNDS_SELECT_LABEL') . '<span></div>
					<div>' . $backgroundBox . '<div>
				</td>';

		$property->pluginHtml = $html;
	}

	/**
	 * Save Attribute Property Data
	 *
	 * @param   object  $product             Product Description
	 * @param   object  &$property           Attribute Property Post Data
	 * @param   object  &$propertyAfterSave  Attribute Property Table Object
	 *
	 * @return  void
	 */
	public function onAttributePropertySaveLoop($product, &$property, &$propertyAfterSave)
	{
		echo "<pre>";
		print_r($propertyAfterSave);
		print_r($property);
		echo "</pre>";
		die();
	}
}
