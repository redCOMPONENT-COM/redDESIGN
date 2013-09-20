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
	 *  @param   object  $row    Product object.
	 *  @param   bool    $isNew  Is this newly saved product.
	 *
	 *  @return  bool
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
}
