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
	 * @return void
	 */
	public function onDisplayProductTypeData()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('reddesign_designtype_id', 'title')));
		$query->from($db->quoteName('#__reddesign_designtypes'));

		$db->setQuery($query);
		$designTypes = $db->loadObjectList();

		$designTypeOptions = array();
		$designTypeOptions[] = JHTML::_('select.option', 0, JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_SELECT'));

		foreach ($designTypes as $designType)
		{
			$designTypeOptions[] = JHTML::_('select.option', $designType->reddesign_designtype_id, $designType->title);
		}

		$html = '<div>';
			$html .= '<label for="designType">' . JText::_('PLG_REDSHOP_PRODUCT_TYPE_REDDESIGN_DESIGN_TYPE') . '</label>';
			$html .= '<div style="padding-top: 8px" >';
				$html .= JHtml::_('select.genericlist', $designTypeOptions, 'designType', 'class="inputbox" style=" ', 'value', 'text', 0);
			$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}
}
