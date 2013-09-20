<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
}

if (JFile::exists(JPATH_SITE . '/components/com_redshop/helpers/cart.php'))
{
	require_once JPATH_SITE . '/components/com_redshop/helpers/cart.php';
}

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
	 */
	public function onOrderButtonClick($data)
	{
		$app     = JFactory::getApplication();
		$db      = JFactory::getDbo();
		$params  = $this->params;
		$session = JFactory::getSession();

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
		$categoryId = $category->categoryId;

		// If there is no category with name taken from the plugin's parameter then create one.
		if (empty($category))
		{
			$newCategory = new stdClass;
			$newCategory->category_name = $params->get('defaultCategoryName', 'redDESIGN Products');
			$newCategory->published = 1;
			$newCategory->products_per_page = 4;
			$result = $db->insertObject('#__redshop_category', $newCategory);
			$categoryId = $db->insertid();

			if (!$result)
			{
				$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_CATEGORY'), 'error');

				return false;
			}
			else
			{
				$newCategoryXref = new stdClass;
				$newCategoryXref->category_parent_id = 0;
				$newCategoryXref->category_child_id = $categoryId;
				$result = $db->insertObject('#__redshop_category_xref', $newCategoryXref);

				if (!$result)
				{
					$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_CATEGORY'), 'error');

					return false;
				}
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
		$manufacturerId = $manufacturer->manufacturerId;

		// If there is no default category, create one.
		if (empty($manufacturer))
		{
			$newManufacturer = new stdClass;
			$newManufacturer->manufacturer_name = $params->get('defaultManufacturerName', 'redCOMPONENT');
			$newManufacturer->product_per_page = 4;
			$newManufacturer->published = 1;
			$result = $db->insertObject('#__redshop_manufacturer', $newManufacturer);
			$manufacturerId = $db->insertid();

			if (!$result)
			{
				$app->enqueueMessage(JText::_('PLG_REDDESIGN_REDSHOP_CAN_NOT_CREATE_DEFAULT_MANUFACTURER'), 'error');

				return false;
			}
		}

		// Make new redSHOP product with data given from redDESIGN.
		// Upload product image
		$src = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/customized/' . $session->get('customizedImage') . '.jpg';
		$dest = JPATH_ROOT . '/components/com_redshop/assets/images/product/' . $session->get('customizedImage') . '.jpg';
		JFile::copy($src, $dest);

		// Count product price and Accessory data
		$productAccessory = array();
		$productPrice = $data['designBackground']->price;

		foreach ($data['designAccessories'] as $accessory)
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
		$newProduct->product_price = $productPrice;
		$newProduct->product_full_image = $session->get('customizedImage') . '.jpg';
		$newProduct->product_template = $template_id;
		$newProduct->product_s_desc = $data['designType']->intro_description;
		$newProduct->product_desc = $productDescription;
		$newProduct->metadesc = json_encode($data);
		$newProduct->published = 1;
		$newProduct->manufacturerId = $manufacturerId;
		$db->insertObject('#__redshop_product', $newProduct);
		$product_id = $db->insertid();

		// Update Product with ProductNumber
		$updateProduct = new stdClass;
		$updateProduct->product_id = $product_id;
		$updateProduct->product_number = "redDESIGN" . $product_id;
		$db->updateObject('#__redshop_product', $updateProduct, 'product_id');

		// Add Category for new Product
		$productCategory = new stdClass;
		$productCategory->category_id = $categoryId;
		$productCategory->product_id  = $product_id;
		$db->insertObject('#__redshop_product_category_xref', $productCategory);

		// Make new redSHOP order for that new product and for the current user. And redirect to the redSHOP checkout process.
		// Add to cart
		$newProductData = array();
		$newProductData['product_id'] = $product_id;
		$newProductData['category_id'] = $categoryId;
		$newProductData['quantity'] = 1;
		$newProductData['product_price'] = $newProduct->product_price;

		$rsCarthelper = new rsCarthelper;
		$rsCarthelper->addProductToCart($newProductData);
		$rsCarthelper->cartFinalCalculation();
		$session->set('customizedImage', "");

		return true;
	}

	/**
	 * Get redDESING component parameters and compare that currency is same in redDESIGN and redSHOP
	 *
	 * @return boolean
	 */
	public function onDesigntypeDisplayCheckCurrency()
	{
		// Get redDESING currency
		$params = JComponentHelper::getParams('com_reddesign');
		$reddesign_currency_symbol = $params->get('currency_symbol', null);

		// Get redSHOP currency
		$redshop_currency_symbol = defined('REDCURRENCY_SYMBOL') ? REDCURRENCY_SYMBOL : $reddesign_currency_symbol;

		$result = array(
			'reddesign_currency_symbol' => $reddesign_currency_symbol,
			'ecommerce_currency_symbol' => $redshop_currency_symbol,
			'wrongCurrency' => true
		);

		if (trim($redshop_currency_symbol) == trim($reddesign_currency_symbol))
		{
			$result['wrongCurrency'] = false;
		}

		return $result;
	}

	/**
	 * Gets Designtype JSON from #__redshop_product table from metadesc field.
	 *
	 * @param   array  $productId  Product ID.
	 *
	 * @return  array
	 */
	public function getDesigntypeJSON($productId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('metadesc');
		$query->from('#__redshop_product');
		$query->where('product_id = ' . $productId);

		$db->setQuery($query);

		return $db->loadResult();
	}
}
