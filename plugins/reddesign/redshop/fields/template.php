<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_redesign
 * @since          1.6
 */

class JFormFieldTemplate extends JFormField
{
	/**
	 * Template list from redshop products
	 *
	 * @var        string
	 * @since    1.6
	 */
	public $type = 'Template';

	/**
	 * Method to get a list of redshop product templates
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected function getInput()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('template_id,template_name ');
		$query->from('#__redshop_template');
		$query->where('published = 1 and template_section="product"');
		$query->order('template_id ASC');
		$db->setQuery($query);
		$productTemplates = $db->loadObjectList();

		return JHTML::_('select.genericlist', $productTemplates, $this->name, 'class="inputbox"', 'template_id', 'template_name', $this->value, $this->id);
		
	}
}