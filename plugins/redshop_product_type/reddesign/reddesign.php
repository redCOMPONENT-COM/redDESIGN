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

		$this->loadLanguage();

		if (!defined('FOF_INCLUDED'))
		{
			JLoader::import('fof.include');
		}
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
			$db = JFactory::getDbo();

			// Get design types.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id', 'name')));
			$query->from($db->quoteName('#__reddesign_designtypes'));
			$db->setQuery($query);
			$designTypes = $db->loadObjectList();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__reddesign_product_mapping'));
			$query->where($db->quoteName('product_id') . ' = ' . $product_data->product_id);
			$db->setQuery($query);
			$selectedDesignTypes = $db->loadResult();

			$designTypeOptions = array();
			$designTypeOptions[] = JHtml::_('select.option', '0', JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_SELECT'));

			foreach ($designTypes as $designType)
			{
				$designTypeOptions[] = JHtml::_('select.option', $designType->id, $designType->name);
			}

			$html = '<div>';
				$html .= '<label for="designType">' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_DESIGN_TYPE') . '</label>';
				$html .= '<div style="padding-top: 7px" >';
					$html .= JHtml::_(
										'select.genericlist',
										$designTypeOptions,
										'designType[]',
										' multiple class="inputbox" size="9" ',
										'value',
										'text',
										explode(',', $selectedDesignTypes)
									);
				$html .= '</div>';
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
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		$reddesignDesigntypeIds = $app->input->get('designType', array(), 'ARRAY');
		$reddesignDesigntypeIds = implode(',', $reddesignDesigntypeIds);

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'product_id')));
		$query->from($db->quoteName('#__reddesign_product_mapping'));
		$query->where($db->quoteName('product_id') . ' = ' . $row->product_id);
		$db->setQuery($query);
		$map = $db->loadObject();

		if (empty($map))
		{
			$map = new JObject;
			$map->product_id = $row->product_id;
			$map->id = $reddesignDesigntypeIds;

			return $db->insertObject('#__reddesign_product_mapping', $map);
		}
		else
		{
			$map->id = $reddesignDesigntypeIds;

			return $db->updateObject('#__reddesign_product_mapping', $map, 'product_id');
		}

		return null;
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
		$product = $property->product;
		$backgrounds = array();

		$db = JFactory::getDbo();

		// Get selected design type.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__reddesign_attribute_mapping'))
			->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id)
			->where($db->quoteName('property_id') . ' = ' . (int) $property->property_id);

		$db->setQuery($query);
		$selectedDesignType = $db->loadResult();

		$checked = '';
		$style   = 'style="display: none;"';

		if ($selectedDesignType)
		{
			$checked = 'checked="checked"';
			$style   = '';
		}

		// Get selected design type.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__reddesign_product_mapping'))
			->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
		$db->setQuery($query);
		$designTypeId = $db->loadResult();

		// Get all the backgrounds that belongs to selected Design Type item.
		if (!empty($designTypeId))
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('reddesign_background_id', 'name', 'thumbnail')))
				->from($db->quoteName('#__reddesign_backgrounds'))
				->where($db->quoteName('isProductionBg') . ' = ' . 0)
				->where($db->quoteName('id') . ' IN (' . $designTypeId . ')');
			$db->setQuery($query);
			$backgrounds = $db->loadObjectList();
		}

		$dropdownHtml = '<tr>'
		. '<td>'
			. '<div>'
			. '<input type="checkbox"
					id="useBackgrounds' . $property->k . $property->g . '"
					name="useBackgrounds' . $property->k . $property->g . '"
					onclick="showBackgrounds(\'' . $property->k . $property->g . '\')"
					value="useBackgrounds' . $property->k . $property->g . '"
					' . $checked . '>'
				. '<label for="useBackgrounds' . $property->k . $property->g . '">'
					. JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_BACKGROUND_ATTRIBUTE')
				. '</label>' .
			'</div>';

		$dropdownHtml .= '<div id="designBackgrounds' . $property->k . $property->g . '" ' . $style . ' class="designBackgrounds">';

		foreach ($backgrounds as $background)
		{
			if ((int) $background->reddesign_background_id == $selectedDesignType)
			{
				$checked = 'checked="checked"';
			}
			else
			{
				$checked = '';
			}

			$dropdownHtml .= '<input type="radio"
								name="attribute[' . $property->k . '][property][' . $property->g . '][redDesignBackground]"
								value="' . $background->reddesign_background_id . '"' . $checked . ' />' . "&nbsp;&nbsp;";
			$dropdownHtml .= $background->name . "&nbsp;&nbsp;";
			$dropdownHtml .= "<img src='" . FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $background->thumbnail .
								"' alt='" . $background->name . "'/>&nbsp;&nbsp;&nbsp;";
		}

		$dropdownHtml .= '</div>';

		$dropdownHtml .= '</td></tr>';

		echo $dropdownHtml;
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
		$backgrounds = array();

		if ($product->product_type == 'redDESIGN')
		{
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'))
				->from($db->quoteName('#__reddesign_product_mapping'))
				->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
			$db->setQuery($query);
			$designTypeId = $db->loadResult();

			if (!empty($designTypeId))
			{
				// Get all the backgrounds that belongs to selected Design Type item.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('reddesign_background_id', 'name', 'thumbnail')))
					->from($db->quoteName('#__reddesign_backgrounds'))
					->where($db->quoteName('isProductionBg') . ' = ' . 0)
					->where($db->quoteName('id') . ' IN (' . $designTypeId . ')');
				$db->setQuery($query);
				$backgrounds = $db->loadObjectList();
			}

			$dropdownHtml = '';

			foreach ($backgrounds as $background)
			{
				$dropdownHtml .= "<input type='radio' name='attribute[{gh}][property][{total_g}][redDesignBackground]' value='" .
									$background->reddesign_background_id . "' />&nbsp;&nbsp;";
				$dropdownHtml .= $background->name . "&nbsp;&nbsp;";
				$dropdownHtml .= "<img src='" . FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $background->thumbnail .
									"' alt='" . $background->name . "'/>&nbsp;&nbsp;&nbsp;";
			}

			$addDropdownJs  = 'var backgroundsDropDownHtml = "' . $dropdownHtml . '";';
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
		$data              = new stdClass;
		$data->product_id  = $product->product_id;
		$data->property_id = $propertyAfterSave->property_id;

		// Clear mapping
		$this->removeReddesignPropertyMapping($data);

		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base insert statement.
		$query->insert($db->quoteName('#__reddesign_attribute_mapping'))
			->columns(array($db->quoteName('id'), $db->quoteName('product_id'), $db->quoteName('property_id')))
			->values((int) $property['redDesignBackground'] . ', ' . (int) $product->product_id . ', ' . (int) $propertyAfterSave->property_id);

		// Set the query and execute the insert.
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Remove redDESIGN backgrounds and redSHOP Attribute Property Mapping
	 *
	 * @param   object  $data  Contains product and property id
	 *
	 * @throws  RuntimeException
	 * @return  void
	 */
	private function removeReddesignPropertyMapping($data)
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base delete statement.
		$query->delete()
			->from($db->quoteName('#__reddesign_attribute_mapping'))
			->where($db->quoteName('product_id') . ' = ' . (int) $data->product_id)
			->where($db->quoteName('property_id') . ' = ' . (int) $data->property_id);

		// Set the query and execute the delete.
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}
	}
}
