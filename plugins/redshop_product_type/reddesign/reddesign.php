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
			$query->select($db->quoteName(array('reddesign_designtype_id', 'title')));
			$query->from($db->quoteName('#__reddesign_designtypes'));
			$db->setQuery($query);
			$designTypes = $db->loadObjectList();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_designtype_id'));
			$query->from($db->quoteName('#__reddesign_product_mapping'));
			$query->where($db->quoteName('product_id') . ' = ' . $product_data->product_id);
			$db->setQuery($query);
			$selectedDesignType = $db->loadResult();

			$designTypeOptions = array();
			$designTypeOptions[] = JHtml::_('select.option', '0', JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_SELECT'));

			foreach ($designTypes as $designType)
			{
				$designTypeOptions[] = JHtml::_('select.option', $designType->reddesign_designtype_id, $designType->title);
			}

			$html = '<div>';
				$html .= '<label for="designType">' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_DESIGN_TYPE') . '</label>';
				$html .= '<div style="padding-top: 7px" >';
					$html .= JHtml::_('select.genericlist', $designTypeOptions, 'designType', ' class="inputbox" ', 'value', 'text', $selectedDesignType);
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
		$reddesignDesigntypeId = $app->input->getInt('designType', null);

		if (!empty($reddesignDesigntypeId))
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_designtype_id'));
			$query->from($db->quoteName('#__reddesign_product_mapping'));
			$query->where($db->quoteName('product_id') . ' = ' . $row->product_id);
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__reddesign_product_mapping'));
				$query->set($db->quoteName('reddesign_designtype_id') . '=' . $reddesignDesigntypeId);
				$query->where($db->quoteName('product_id') . ' = ' . $row->product_id);

				$db->setQuery($query);

				return $db->query();
			}
			else
			{
				$columns = array('reddesign_designtype_id', 'product_id');
				$values = array($reddesignDesigntypeId, $row->product_id);

				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__reddesign_product_mapping'));
				$query->columns($db->quoteName($columns));
				$query->values(implode(',', $values));

				$db->setQuery($query);

				return $db->query();
			}
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

		$db = JFactory::getDbo();

		// Get selected design type.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('reddesign_designtype_id'))
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
		$query->select($db->quoteName('reddesign_designtype_id'))
			->from($db->quoteName('#__reddesign_product_mapping'))
			->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
		$db->setQuery($query);
		$designTypeId = $db->loadResult();

		// Get all the backgrounds that belongs to selected Design Type item.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('reddesign_background_id', 'title', 'thumbnail')))
			->from($db->quoteName('#__reddesign_backgrounds'))
			->where($db->quoteName('isProductionBg') . ' = ' . 0)
			->where($db->quoteName('reddesign_designtype_id') . ' = ' . $designTypeId);
		$db->setQuery($query);
		$backgrounds = $db->loadObjectList();

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
			$dropdownHtml .= $background->title . "&nbsp;&nbsp;";
			$dropdownHtml .= "<img src='" . FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $background->thumbnail .
								"' alt='" . $background->title . "'/>&nbsp;&nbsp;&nbsp;";
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

		if ($product->product_type == 'redDESIGN')
		{
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_designtype_id'))
				->from($db->quoteName('#__reddesign_product_mapping'))
				->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
			$db->setQuery($query);
			$designTypeId = $db->loadResult();

			// Get all the backgrounds that belongs to selected Design Type item.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('reddesign_background_id', 'title', 'thumbnail')))
				->from($db->quoteName('#__reddesign_backgrounds'))
				->where($db->quoteName('isProductionBg') . ' = ' . 0)
				->where($db->quoteName('reddesign_designtype_id') . ' = ' . $designTypeId);
			$db->setQuery($query);
			$backgrounds = $db->loadObjectList();

			$dropdownHtml = '';

			foreach ($backgrounds as $background)
			{
				$dropdownHtml .= "<input type='radio' name='attribute[{gh}][property][{total_g}][redDesignBackground]' value='" .
									$background->reddesign_background_id . "' />&nbsp;&nbsp;";
				$dropdownHtml .= $background->title . "&nbsp;&nbsp;";
				$dropdownHtml .= "<img src='" . FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $background->thumbnail .
									"' alt='" . $background->title . "'/>&nbsp;&nbsp;&nbsp;";
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
			->columns(array($db->quoteName('reddesign_designtype_id'), $db->quoteName('product_id'), $db->quoteName('property_id')))
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
