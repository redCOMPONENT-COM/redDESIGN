<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE . '/components/com_redshop/helpers/cart.php';
/**
 * redSHOP Plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgReddesignRedshop extends JPlugin
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
	 * @param   array  $data  An array that holds design information
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function onOrderButtonClick($data)
	{
		$app    = JFactory::getApplication();
		$db     = JFactory::getDbo();
		$params = $this->params;
		$session 			= JFactory::getSession();

		// Check if redSHOP is there.
		$prefix = $db->getPrefix();
		$tableName = $prefix . 'redshop_product';
		$query = 'SHOW TABLES LIKE \'' . $tableName . '\'';
		$db->setQuery($query);
		$tables = $db->loadAssoc();

		if (!count($tables))
		{
			$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_REDSHOP_IS_NOT_INSTALLED'), 'notice');

			return false;
		}

		// Check category.
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__redshop_category');
		$query->where('category_name = ' . $db->quote($params->get('defaultCategoryName', 'redDESIGN Products')));
		$query->order('category_id ASC');
		$db->setQuery($query);
		$category = $db->loadObject();
		$category_id = $category->category_id;

		// If there is no category with name taken from the plugin's parameter then create one.
		if (empty($category))
		{
			$newCategory = new stdClass;
			$newCategory->category_name = $params->get('defaultCategoryName', 'redDESIGN Products');
			$result = $db->insertObject('#__redshop_category', $newCategory);
			$category_id = $db->insertid();

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
		$manufacturer_id = $manufacturer->manufacturer_id;

		// If there is no default category, create one.
		if (empty($manufacturer))
		{
			$newManufacturer = new stdClass;
			$newManufacturer->manufacturer_name = $params->get('defaultManufacturerName', 'redCOMPONENT');
			$result = $db->insertObject('#__redshop_manufacturer', $newManufacturer);
			$manufacturer_id = $db->insertid();

			if (!$result)
			{
				$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_MANUFACTURER'), 'notice');

				return false;
			}
		}

		// Make new redSHOP product with data given from redDESIGN.
		// Upload product image
		$src = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $session->get('customizedImage') . '.jpg';
		chmod($src, 0777);
		$dest = JPATH_ROOT . '/components/com_redshop/assets/images/product/' . $session->get('customizedImage') . '.jpg';
		JFile::copy($src, $dest);

		// Count product price and Accessory data
		$productAccessory = array();
		$productPrice = $data['designBackground']->price;

		foreach ($data['desingAccessories'] as $accessory)
		{
			$productPrice += $accessory->price;
			$productAccessory[] = $accessory->title;
		}

		// Add Accessory data in product description
		$productAccessory = implode(",", $productAccessory);
		$productDescription = $data['designType']->description . "<br/>" . JText::_('PLG_REDDESIGN_REDSHOP_ACCESSORIES') . " : " . $productAccessory;

		// Get product Template
		$template_id = $params->get('defaultTemplate');

		if (!$template_id)
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__redshop_template');
			$query->where('template_section = "product"');
			$query->order('template_id ASC');
			$db->setQuery($query);
			$productTemplate = $db->loadObject();
			$template_id = $productTemplate->template_id;
		}

		// Insert new Product
		$newProduct = new stdClass;
		$newProduct->product_name = $data['designType']->title;
		$newProduct->product_number = "design" . rand(9999, 999999);
		$newProduct->product_price = $productPrice;
		$newProduct->product_full_image = $session->get('customizedImage') . '.jpg';
		$newProduct->product_template = $template_id;
		$newProduct->product_s_desc = $data['designType']->intro_description;
		$newProduct->product_desc = $productDescription;
		$newProduct->published = 1;
		$newProduct->manufacturer_id = $manufacturer_id;
		$result = $db->insertObject('#__redshop_product', $newProduct);
		$product_id = $db->insertid();

		// Add Category for new Product
		$productCategory = new stdClass;
		$productCategory->category_id  = $category_id;
		$productCategory->product_id  = $product_id;
		$productCategory = $db->insertObject('#__redshop_product_category_xref', $productCategory);

		// Make new redSHOP order for that new product and for the current user. And redirect to the redSHOP checkout process.
		// Add to cart
		$newProductData = array();
		$newProductData['product_id'] = $product_id;
		$newProductData['category_id'] = $category_id;
		$newProductData['quantity'] = 1;
		$newProductData['product_price'] = $newProduct->product_price;
		$rsCarthelper = new rsCarthelper;
		$rsCarthelper->addProductToCart($newProductData);
		$session->set('customizedImage', "");

		return true;
	}
}
