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

			$html = '<div>';
				$html .= '<label for="designType">' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_DEFAULT_DESIGN_TYPE') . '</label>';
				$html .= '<div style="padding: 7px 0 7px 0" >';
					$html .= JHtml::_(
						'select.genericlist',
						$designTypeOptions,
						'defaultDesignType',
						' class="inputbox" ',
						'value',
						'text',
						$productDesignTypesMapping->default_designtype_id
					);
				$html .= '*</div>';
			$html .= '</div>';

			// Create multiple select list of related design types
			$html .= '<div>';
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
		$defaultDesigntypeId = $input->getInt('defaultDesignType', 0);
		$relatedDesigntypeIds = $input->get('relatedDesignTypes', array(), 'ARRAY');

		$designTypesModel = RModel::getAdminInstance('Designtypes', array('ignore_request' => true), 'com_reddesign');

		return $designTypesModel->saveProductDesignTypesMapping($row->product_id, $defaultDesigntypeId, $relatedDesigntypeIds);
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
		if (!empty($property->product))
		{
			$product = $property->product;
			$backgrounds = array();

			$db = JFactory::getDbo();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('designtype_id'))
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
			$query->select($db->quoteName('designtype_id'))
				->from($db->quoteName('#__reddesign_product_mapping'))
				->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
			$db->setQuery($query);
			$designTypeId = $db->loadResult();

			// Get all the backgrounds that belongs to selected Design Type item.
			if (!empty($designTypeId))
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('id', 'name', 'svg_file')))
					->from($db->quoteName('#__reddesign_backgrounds'))
					// ToDo Fix RD-144 ->where($db->quoteName('isProductionBg') . ' = ' . 0)
					->where($db->quoteName('designtype_id') . ' IN (' . $designTypeId . ')');
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
				if ((int) $background->id == $selectedDesignType)
				{
					$checked = 'checked="checked"';
				}
				else
				{
					$checked = '';
				}
				//$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $background->svg_file;

				$dropdownHtml .= '<input type="radio"
								name="attribute[' . $property->k . '][property][' . $property->g . '][redDesignBackground]"
								value="' . $background->id . '"' . $checked . ' />' . "&nbsp;&nbsp;";
				$dropdownHtml .= $background->name . "&nbsp;&nbsp;";
				$dropdownHtml .= "<img src='" . JURI::root() . "media/com_reddesign/backgrounds/" . $background->svg_file .
									"' alt='" . $background->name . "' style='width:100px;' />&nbsp;&nbsp;&nbsp;";
				//$dropdownHtml .= JHTML::_('image', 'media/com_reddesign/assets/backgrounds/' . $background->svg_file, $background->name)
				//	. "&nbsp;&nbsp;&nbsp;";
			}

			$dropdownHtml .= '</div>';

			$dropdownHtml .= '</td></tr>';

			echo $dropdownHtml;

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
		$backgrounds = array();

		if ($product->product_type == 'redDESIGN')
		{
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// Get selected design type.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('designtype_id'))
				->from($db->quoteName('#__reddesign_product_mapping'))
				->where($db->quoteName('product_id') . ' = ' . (int) $product->product_id);
			$db->setQuery($query);
			$designTypeId = $db->loadResult();

			if (!empty($designTypeId))
			{
				// Get all the backgrounds that belongs to selected Design Type item.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('id', 'name', 'svg_file')))
					->from($db->quoteName('#__reddesign_backgrounds'))
					->where($db->quoteName('isProductionBg') . ' = ' . 0)
					->where($db->quoteName('designtype_id') . ' IN (' . $designTypeId . ')');
				$db->setQuery($query);
				$backgrounds = $db->loadObjectList();
			}

			$dropdownHtml = '';

			foreach ($backgrounds as $background)
			{
				$dropdownHtml .= "<input type='radio' name='attribute[{gh}][property][{total_g}][redDesignBackground]' value='"
					. $background->id . "' />&nbsp;&nbsp;";
				$dropdownHtml .= $background->name . "&nbsp;&nbsp;";
				$dropdownHtml .= "<img src='" . JURI::root() . "media/com_reddesign/backgrounds/" . $background->svg_file .
									"' alt='" . $background->name . "' style='width:100px;' />&nbsp;&nbsp;&nbsp;";
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

		// Delete all current mapping in database
		$query->delete($db->quoteName('#__reddesign_attribute_mapping'))
			->where($db->quoteName('product_id') . "=" . (int) $product->product_id);

		if ($property['redDesignBackground'])
		{
			// Create the base insert statement.
			$query->insert($db->quoteName('#__reddesign_attribute_mapping'))
				->columns(array($db->quoteName('designtype_id'), $db->quoteName('product_id'), $db->quoteName('property_id')))
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
