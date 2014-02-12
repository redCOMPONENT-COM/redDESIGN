<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * redDESIGN Product Type plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgRedshop_Product_TypeReddesign extends JPlugin
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

		$this->loadLanguage('com_reddesign', JPATH_SITE);
		$this->loadLanguage();

		// Register component prefix
		JLoader::registerPrefix('Reddesign', JPATH_ADMINISTRATOR . '/components/com_reddesign');

		// Register library prefix.
		JLoader::registerPrefix('Reddesign', JPATH_LIBRARIES . '/reddesign');

		JLoader::import('redcore.bootstrap');

		JFactory::getApplication()->input->set('redcore', true);

		// Load bootstrap + fontawesome
		JHtml::_('rbootstrap.framework');

		RHelperAsset::load('component.js', 'redcore');
		RHelperAsset::load('component.min.css', 'redcore');
	}

	/**
	 * Updates product type list with redDESIGN
	 *
	 * @return array
	 */
	public function onListProductTypes()
	{
		return array('value' => 'redDESIGN', 'text' => JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_REDDESIGN_PRODUCT_TYPE'));
	}

	/**
	 * Displays design types dropdown list for product type specific data tab.
	 *
	 * @param   object  $product_data  Product data object.
	 *
	 * @return void
	 */
	public function onDisplayProductTypeData($product_data)
	{
		if ($product_data->product_type == 'redDESIGN')
		{
			// Get design types.
			$designTypesModel = RModel::getAdminInstance('Designtypes', array('ignore_request' => true), 'com_reddesign');
			$designTypesModel->setState('list.ordering', 'name');
			$designTypes = $designTypesModel->getItems();

			// Get selected design type.
			$productDesignTypesMapping = $designTypesModel->getProductDesignTypesMapping($product_data->product_id);

			$designTypeOptions = array();
			$designTypeOptions[] = JHtml::_('select.option', '0', JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_SELECT'));

			foreach ($designTypes as $designType)
			{
				$designTypeOptions[] = JHtml::_('select.option', $designType->id, $designType->name);
			}

			// Create multiple select list of related design types
			$html = '<div>';
				$html .= '<label for="designType">' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_RELATED_DESIGN_TYPES') . '</label>';
				$html .= '<div style="padding-top: 7px" >';
					$html .= JHtml::_(
						'select.genericlist',
						$designTypeOptions,
						'relatedDesignTypes[]',
						' multiple class="inputbox" size="9" ',
						'value',
						'text',
						explode(',', $productDesignTypesMapping->related_designtype_ids)
					);
				$html .= '*</div>';
			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * Map redSHOP product with redDESIGN design type.
	 * For that purpose use #__reddesign_product_mapping.
	 *
	 * @param   object  $row    Product object.
	 * @param   bool    $isNew  Is this newly saved product.
	 *
	 * @return  bool
	 */
	public function onAfterProductSave($row, $isNew)
	{
		$input = JFactory::getApplication()->input;
		$relatedDesigntypeIds = $input->get('relatedDesignTypes', array(), 'ARRAY');

		$designTypesModel = RModel::getAdminInstance('Designtypes', array('ignore_request' => true), 'com_reddesign');

		return $designTypesModel->saveProductDesignTypesMapping($row->product_id, $relatedDesigntypeIds);
	}

	/**
	 * Map redSHOP product attribute value with redDESIGN background from a specific redDESIGN design type.
	 * For that purpose use #__reddesign_attribute_mapping.
	 * attribute[$property->k][property][$property->g][redDesignBackground]
	 *
	 * @param   object  $property  Property object.
	 *
	 * @return  void
	 */
	public function productTypeAttributeValue($property)
	{
		if (!empty($property->product) &&  $property->product->product_type == 'redDESIGN')
		{
			$product = $property->product;

			$designTypesModel = RModel::getAdminInstance('Designtypes', array('ignore_request' => true), 'com_reddesign');
			$designTypesProductMapping = $designTypesModel->getProductDesignTypesMapping($product->product_id);

			// Prepare array of backgrounds from related design types (default designtype + other related).

			$backgroundsOfRelatedDesignTypes = array();

			if ($designTypesProductMapping->related_designtype_ids)
			{
				$relatedDesignTypeIds = explode(',', $designTypesProductMapping->related_designtype_ids);

				// Merge arrays of backgrounds from rest of the design types.
				foreach ($relatedDesignTypeIds as $relatedDesignTypeId)
				{
					if ($relatedDesignTypeId)
					{
						$backgroundModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
						$backgroundModel->setState('designtype_id', $relatedDesignTypeId);
						$backgrounds = $backgroundModel->getItems();

						$backgroundsOfRelatedDesignTypes = array_merge($backgroundsOfRelatedDesignTypes, $backgrounds);
					}
				}

				/*
					All backgrounds from selected design types will be displayed in attributes tab.
					That will enable admin to select which backgrounds will be displayed in the frontend.
				*/

				$checked = '';
				$display = 'style="display: none;"';
				$backgroundModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
				$propertyBackgroundMapping = $backgroundModel->getBackgroundPropertyPair($property->property_id);

				if ($propertyBackgroundMapping->background_id)
				{
					$checked = 'checked="checked"';
					$display = 'style="display: block;"';
				}

				// Create background attribute HTML.
				$dropdownHtml = '<tr>' .
									'<td colspan="7" style="white-space: normal;">' .
										'<div>' .
											'<input type="checkbox" id="useBackgrounds' . $property->k . $property->g . '" ' .
													'name="useBackgrounds' . $property->k . $property->g . '" ' .
													'onclick="showBackgrounds(\'' . $property->k . $property->g . '\')" ' .
													'value="useBackgrounds' . $property->k . $property->g . '" ' .
													$checked .
											'>' .
												'<label for="useBackgrounds' . $property->k . $property->g . '">' .
													JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_BACKGROUND_ATTRIBUTE') .
												'</label>' .
										'</div>' .
										'<div id="designBackgrounds' . $property->k . $property->g . '" ' . $display . ' class="designBackgrounds">';

				foreach ($backgroundsOfRelatedDesignTypes as $background)
				{
					$checked = '';

					if ($background->id == $propertyBackgroundMapping->background_id)
					{
						$checked = 'checked="checked"';
					}

					$dropdownHtml .= '<input type="radio" ' .
											'name="attribute[' . $property->k . '][property][' . $property->g . '][redDesignBackground]" ' .
											'value="' . $background->id . '"' . $checked .
									' />';
					$dropdownHtml .= '&nbsp;&nbsp' . $background->name . '&nbsp;&nbsp;';
					// @todo put in object and load it with ajax or show png thumbnail
					//$dropdownHtml .= '<img src="' . JURI::root() . 'media/com_reddesign/backgrounds/' . $background->svg_file .
					//					'" alt="' . $background->name . '" style="width:100px;" />&nbsp;&nbsp;&nbsp;';
				}

					$dropdownHtml .= '</div>';
				$dropdownHtml .= '</td></tr>';

				echo $dropdownHtml;
			}
		}
	}

	/**
	 * Loads modified fields.js for backend product detail view.
	 *
	 * @param   object  $product  Current product object.
	 *
	 * @return  bool
	 */
	public function loadFieldsJSFromPlugin($product)
	{
		$jsLoaded = false;

		if ($product->product_type == 'redDESIGN')
		{
			$designTypesModel = RModel::getAdminInstance('Designtypes', array('ignore_request' => true), 'com_reddesign');
			$designTypesProductMapping = $designTypesModel->getProductDesignTypesMapping($product->product_id);

			// Prepare array of backgrounds from related design types (default designtype + other related).

			$dropdownHtml = '';
			$backgroundsOfRelatedDesignTypes = array();

			if ($designTypesProductMapping->related_designtype_ids)
			{
				$relatedDesignTypeIds = explode(',', $designTypesProductMapping->related_designtype_ids);

				// Merge arrays of backgrounds from rest of the design types.
				foreach ($relatedDesignTypeIds as $relatedDesignTypeId)
				{
					if ($relatedDesignTypeId)
					{
						$backgroundModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
						$backgroundModel->setState('designtype_id', $relatedDesignTypeId);
						$backgrounds = $backgroundModel->getItems();

						$backgroundsOfRelatedDesignTypes = array_merge($backgroundsOfRelatedDesignTypes, $backgrounds);
					}
				}

				// Create background attribute HTML.
				foreach ($backgroundsOfRelatedDesignTypes as $background)
				{
					$dropdownHtml .= '<input type="radio" name="attribute[{gh}][property][{total_g}][redDesignBackground]" value="' .
									$background->id . '" />';
					$dropdownHtml .= '&nbsp;&nbsp;' . $background->name . '&nbsp;&nbsp;';
					// @todo put in object and load it with ajax or show png thumbnail
					//$dropdownHtml .= '<img src="' . JURI::root() . 'media/com_reddesign/backgrounds/' . $background->svg_file .
					//	'" alt="' . $background->name . '" style="width:100px;" />&nbsp;&nbsp;&nbsp;';
				}
			}

			$document = JFactory::getDocument();

			$addDropdownJs  = "var backgroundsDropDownHtml = '$dropdownHtml';";
			$addDropdownJs .= 'var backgroundChckText = "' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_BACKGROUND_ATTRIBUTE') . '";';

			$document->addScriptDeclaration($addDropdownJs);
			$document->addScript(JURI::root() . 'plugins/redshop_product_type/reddesign/js/fields.js');

			$jsLoaded = true;
		}

		return $jsLoaded;
	}

	/**
	 * Save Attribute Property Data
	 *
	 * @param   object  $product             Product Description
	 * @param   array   &$property           Attribute Property Post Data
	 * @param   object  &$propertyAfterSave  Attribute Property Table Object
	 *
	 * @throws  RuntimeException
	 *
	 * @return  void
	 */
	public function onAttributePropertySaveLoop($product, &$property, &$propertyAfterSave)
	{
		$backgroundModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');

		try
		{
			$backgroundModel->saveBackgroundPropertyPair($propertyAfterSave->property_id, $property['redDesignBackground']);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}
	}
}
