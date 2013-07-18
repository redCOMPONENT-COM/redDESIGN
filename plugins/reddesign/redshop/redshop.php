<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

/**
 * redSHOP Plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgReddesignShopsRedshop extends JPlugin
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
	 * Plugin method for event on building new product for redSHOP.
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function onOrderButtonClick()
	{
		$app    = JFactory::getApplication();
		$db     = JFactory::getDbo();
		$params = $this->params;

		// Check if redSHOP is there.
		$query = 'SHOW TABLES LIKE ' . $db->quoteName('#__redshop_product');
		$db->setQuery($query);
		$tables = $db->loadAssoc();

		if (count($tables) > 0)
		{
			// Check category.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__redshop_category');
			$query->where('category_name = ' . $db->quote($params->get('defaultCategoryName', 'redDESIGN Products')));
			$query->order('category_id ASC');
			$db->setQuery($query);
			$category = $db->loadObject();

			// If there is no category with name taken from the plugin's parameter than create one.
			if (empty($category))
			{
				$newCategory = new stdClass;
				$newCategory->category_name = $params->get('defaultCategoryName', 'redDESIGN Products');
				$result = $db->insertObject('#__redshop_category', $newCategory);

				if (!$result)
				{
					$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_CATEGORY'), 'notice');

					return false;
				}
			}

			// Check manufacturer.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__redshop_manufacturer');
			$query->where('manufacturer_name = ' . $db->quote($params->get('defaultManufacturerName', 'redCOMPONENT')));
			$query->order('manufacturer_id ASC');
			$db->setQuery($query);
			$manufacturer = $db->loadObject();

			// If there is no default category, create one.
			if (empty($manufacturer))
			{
				$newManufacturer = new stdClass;
				$newManufacturer->category_name = $params->get('defaultManufacturerName', 'redCOMPONENT');
				$result = $db->insertObject('#__redshop_manufacturer', $newManufacturer);

				if (!$result)
				{
					$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_MANUFACTURER'), 'notice');

					return false;
				}
			}

			// Make new redSHOP product with data given from redDESIGN.

			// Make new redSHOP order for that new product and for the current user. And redirect to the redSHOP checkout process.
		}
		else
		{
			$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_REDSHOP_IS_NOT_INSTALLED'), 'notice');

			return false;
		}

		return true;
	}
}
