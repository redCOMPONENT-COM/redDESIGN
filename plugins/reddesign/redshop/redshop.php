<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
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
		$newProduct->product_price = $productPrice;
		$newProduct->product_full_image = $session->get('customizedImage') . '.jpg';
		$newProduct->product_template = $template_id;
		$newProduct->product_s_desc = $data['designType']->intro_description;
		$newProduct->product_desc = $productDescription;
		$newProduct->published = 1;
		$newProduct->manufacturer_id = $manufacturer_id;
		$result = $db->insertObject('#__redshop_product', $newProduct);
		$product_id = $db->insertid();

		// Update Product with ProductNumber
		$updateProduct = new stdClass;
		$updateProduct->product_id = $product_id;
		$updateProduct->product_number = "redDESIGN" . $product_id;
		$result = $db->updateObject('#__redshop_product', $updateProduct, 'product_id');

		// Add Category for new Product
		$productCategory = new stdClass;
		$productCategory->category_id  = $category_id;
		$productCategory->product_id  = $product_id;
		$productCategory = $db->insertObject('#__redshop_product_category_xref', $productCategory);

		// Generate PDF Production file
		$this->createPdfProductfile($data);

		// Make new redSHOP order for that new product and for the current user. And redirect to the redSHOP checkout process.
		// Add to cart
		$newProductData = array();
		$newProductData['product_id'] = $product_id;
		$newProductData['category_id'] = $category_id;
		$newProductData['quantity'] = 1;
		$newProductData['product_price'] = $newProduct->product_price;
		$rsCarthelper = new rsCarthelper;
		$rsCarthelper->addProductToCart($newProductData);
		$rsCarthelper->cartFinalCalculation();
		$session->set('customizedImage', "");

		return true;
	}

	/**
	 * Create ProductPDF file for redDesign
	 *
	 * @param   array  $data  An array that holds design information
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function createPdfProductfile($data)
	{
		$session = JFactory::getSession();
		$productionFileName = $session->get('customizedImage');

		$areas = $data['desingAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epstextfile = '';

		$backgroundImageFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->image_path;
		$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->eps_file;
		$pdfFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/';
		$epsFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/eps/';

		// Read EPS
		$im = new Imagick;
		$im->readImage($epsFileLocation);
		$dimensions = $im->getImageGeometry();
		$imageWidth = $dimensions['width'];
		$imageHeight = $dimensions['height'];

		$pdfLeftMargin = 28.35;
		$pdfTopMargin = 28.35;

		foreach ($areas as $area)
		{
			if ($area->fontTypeId)
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
				$this->fontType = $fontModel->getItem($area->fontTypeId);
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $this->fontType->font_file;
			}
			else
			{
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($data['designBackground']->reddesign_background_id);
			$this->areaItem = $areaModel->getItem($area->id);

			if ((int) $this->areaItem->textalign == 1)
			{
				$offsetLeft = $this->areaItem->x1_pos;
			}
			elseif ((int) $this->areaItem->textalign == 2)
			{
				$offsetLeft = $this->areaItem->x2_pos - ($this->areaItem->width / 2);
			}
			else
			{
				$offsetLeft = $this->areaItem->x1_pos + ($this->areaItem->width / 4);
			}

			$offsetTop = $imageHeight - $this->areaItem->y2_pos;

			$offsetLeft = $pdfLeftMargin + $offsetLeft;
			$offsetTop = $offsetTop + $pdfTopMargin;

			$epsText .= $epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $area->fontSize . "  scalefont setfont\n";

			$epsText .= "\n0 0 0 setrgbcolor";
			$epsText .= "\ngsave\n";

			$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
			$epsAreaText .= "\n (" . $area->textArea . ") true charpath ";

			$epsAreaText .= "\n fill";
			$epsAreaText .= "\n showpage";

			$epsText .= "\n $offsetLeft $offsetTop moveto";
			$epsText .= "\n (" . $area->textArea . ")";
			$epsText .= "\n cshow";
		}

		$epsText .= "\ngrestore\n";

		$tmp_eps_file = $epsFilePath . "tmp_" . $productionFileName . ".eps";
		$tmp_texteps_file = $epsFilePath . "tmptext_" . $productionFileName . ".eps";

		$epsFileName = $epsFilePath . "reddesign" . $productionFileName . ".pdf";

		$tempfile = "%!PS";
		$tempfile .= "\n%%Creator:reddesign";
		$tempfile .= "\n%%Title:reddesign" . $productionFileName;
		$tempfile .= "\n%%LanguageLevel: 3";
		$tempfile .= "\n%%DocumentData: Clean7Bit";
		$tempfile .= "\n%%EndComments";
		$tempfile .= "\n";
		$tempfile .= "\n%%BeginProlog";
		$tempfile .= "\n/BeginEPSF {";
		$tempfile .= "\n/EPSFsave save def";
		$tempfile .= "\ncount /OpStackSize exch def";
		$tempfile .= "\n/DictStackSize countdictstack def";
		$tempfile .= "\n% turn off showpage";
		$tempfile .= "\n/showpage {} def";
		$tempfile .= "\n% set up default graphics state";
		$tempfile .= "\n0 setgray 0 setlinecap";
		$tempfile .= "\n1 setlinewidth 0 setlinejoin";
		$tempfile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$tempfile .= "\n/languagelevel where";
		$tempfile .= "\n{pop languagelevel 1 ne";
		$tempfile .= "\n{false setstrokeadjust false setoverprint} if";
		$tempfile .= "\n} if";
		$tempfile .= "\n} bind def";
		$tempfile .= "\n";
		$tempfile .= "\n/EndEPSF {";
		$tempfile .= "\ncount OpStackSize sub";
		$tempfile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$tempfile .= "\ncountdictstack DictStackSize sub";
		$tempfile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$tempfile .= "\nEPSFsave restore";
		$tempfile .= "\n} bind def";
		$tempfile .= "\n";
		$tempfile .= "\n%%EndProlog";
		$tempfile .= "\n%%Page: 1 1";
		$tempfile .= "\n/pagesave save def";
		$tempfile .= "\n";
		$tempfile .= "\n 0 0 translate";

		if (file_exists($epsFileLocation))
		{
			$tempfile .= "\nBeginEPSF";
			$tempfile .= "\n 0 0 translate";
			$tempfile .= "\n%%BeginDocument: danske.eps";
			$tempfile .= "\n(" . $epsFileLocation . ") run";
			$tempfile .= "\n%%EndDocument";
			$tempfile .= "\nEndEPSF";
		}

		$tempfile .= "\npagesave restore showpage";

		// Create temp eps file for reading bounding box...
		$tempfile .= "\n%%EOF";

		$tmp_eps_image = $epsFilePath . "tmp_eps_" . $productionFileName . ".eps";
		$tmp_eps_pdf = $epsFilePath . "tmp_eps_" . $productionFileName . ".pdf";
		$tmp_bound = $epsFilePath . "tmp_bound_" . $productionFileName . ".ps";

		$fp = fopen($tmp_eps_image, "w");
			fwrite($fp, $tempfile);
			fclose($fp);

		$imageWidth = $imageWidth + 56.7;
		$imageHeight = $imageHeight + 56.7;

		$cmd = "gs -dBATCH -dNOPAUSE -sOutputFile=$tmp_bound -sDEVICE=ps2write  \-c '<< /PageSize [$imageWidth $imageHeight]  >> setpagedevice'  -f" . $tmp_eps_image;
		exec($cmd);

		$image_bound = $this->readBound($tmp_bound);
		$epsfile  = "%!PS-Adobe-3.1 EPSF-3.1";
		$epsfile .= "\n%%Creator:reddesign";
		$epsfile .= "\n%%Title:reddesign" . $productionFileName;
		$epsfile .= "\n%%LanguageLevel: 3";
		$epsfile .= "\n%%DocumentData: Clean7Bit";
		$epsfile .= "\n%%EndComments";
		$epsfile .= "\n";
		$epsfile .= "\n%%BeginProlog";
		$epsfile .= "\n/BeginEPSF {";
		$epsfile .= "\n/EPSFsave save def";
		$epsfile .= "\ncount /OpStackSize exch def";
		$epsfile .= "\n/DictStackSize countdictstack def";
		$epsfile .= "\n% turn off showpage";
		$epsfile .= "\n/showpage {} def";
		$epsfile .= "\n% set up default graphics state";
		$epsfile .= "\n0 setgray 0 setlinecap";
		$epsfile .= "\n1 setlinewidth 0 setlinejoin";
		$epsfile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$epsfile .= "\n/languagelevel where";
		$epsfile .= "\n{pop languagelevel 1 ne";
		$epsfile .= "\n{false setstrokeadjust false setoverprint} if";
		$epsfile .= "\n} if";
		$epsfile .= "\n} bind def";
		$epsfile .= "\n";
		$epsfile .= "\n/EndEPSF {";
		$epsfile .= "\ncount OpStackSize sub";
		$epsfile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$epsfile .= "\ncountdictstack DictStackSize sub";
		$epsfile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$epsfile .= "\nEPSFsave restore";
		$epsfile .= "\n} bind def";
		$epsfile .= "\n";

		$epsfile .= "\n/x 1 def";
		$epsfile .= "\n/cshow		%  (str)  =>  ---";
		$epsfile .= "\n{ dup stringwidth pop -2 div 0 rmoveto show } bind def";
		$epsfile .= "\n/alignshow		%  (str)  =>  ---";
		$epsfile .= "\n	{dup stringwidth pop neg 0 rmoveto show} bind def";
		$epsfile .= "\n/nl { x currentpoint exch pop 16 sub moveto } bind def";
		$epsfile .= "\n%%EndProlog";
		$epsfile .= "\n%%Page: 1 1";
		$epsfile .= "\n/pagesave save def";
		$epsfile .= "\n";

		if (file_exists($epsFileLocation))
		{
			$epsfile .= "\nBeginEPSF";
			$epsfile .= "\n 0 0 translate";

			if ($image_bound[0] > 100)
			{
				$epsfile .= "\n 0 0 translate";
			}
			elseif ($image_bound[3] == 0)
			{
				$epsfile .= "\n 0 0 translate";
			}
			else
			{
				$epsfile .= "\n " . $pdfLeftMargin . " " . $pdfTopMargin . " translate";
			}

			$epsfile .= "\n% 0 0 " . ($imageWidth) . " " . ($imageHeight);

			$epsfile .= "\n%%BeginDocument: danske.eps";
			$epsfile .= "\n(" . $epsFileLocation . ") run";
			$epsfile .= "\n%%EndDocument";
			$epsfile .= "\nEndEPSF";
		}

		$epsfile .= "\nBeginEPSF";
		$epsfile .= "\nclear";
		$bound_width = $image_bound[2] - $image_bound[0];
		$bound_height = $image_bound[3] - $image_bound[1];
		$final_y = $image_bound[1] + $imageHeight;
		$final_x = ($image_bound[0]);

		if ($image_bound[3] == 0)
		{
			$final_x = 201;
			$final_y = 405 + ($imageHeight);
		}
		elseif ($image_bound[0] == 0)
		{
			$final_x = 201;
			$final_y = 405 + ($imageHeight);
		}
		elseif ($image_bound[0] < 100)
		{
			$final_x = $pdfLeftMargin;
			$final_y = $pdfTopMargin + ($imageHeight);
		}

		$final_y = $image_bound[1] + $imageHeight;
		$final_x = ($image_bound[0]);

		if ($image_bound[0] < 100)
		{
			$final_x += $pdfLeftMargin;
			$final_y += $pdfTopMargin;
		}

		$epsfile .= "\n 0 0 translate";
		$epstextfile .= $epsfile;

		$epsfile .= "\n%%BeginDocument: text.eps";
		$epsfile .= "\n" . $epsAreaText;
		$epsfile .= "\n%%EndDocument";
		$epsfile .= "\nEndEPSF";

		// Create temp eps file for reading bounding box...
		$epsfile .= "\n%%EOF";

		$fp = fopen($tmp_eps_file, "w");
			fwrite($fp, $epsfile);
			fclose($fp);

		$fp = fopen($tmp_texteps_file, "w");
		fwrite($fp, $epstextfile);
		fclose($fp);

		// Create pdf ...
		ob_clean();

		$pdfFileName = $pdfFilePath . "reddesign" . $productionFileName . ".pdf";
		$cmd = "gs -dBATCH -dNOPAUSE -dNOEPS -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true  -dSubsetFonts=false -sOutputFile=$pdfFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]  >> setpagedevice'  -f" . $tmp_eps_file;
		exec($cmd);

		$cmd = "gs -dBATCH -dNOPAUSE  -dNOEPS -dEPSCrop -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true -dSubsetFonts=false -dOptimize=false -sOutputFile=$epsFileName -sDEVICE=pdfwrite  \-c '<< /PageSize [$imageWidth $imageHeight]  >> setpagedevice' -f" . $tmp_texteps_file;
		exec($cmd);

		if (file_exists($tmp_texteps_file))
		{
			unlink($tmp_texteps_file);
		}

		if (file_exists($tmp_eps_file))
		{
			unlink($tmp_eps_file);
		}

		if (file_exists($tmp_eps_image))
		{
			unlink($tmp_eps_image);
		}

		if (file_exists($tmp_bound))
		{
			unlink($tmp_bound);
		}
	}

	/**
	 * Read BoundingArea of Image
	 *
	 * @param   string  $fname  location of image
	 *
	 * @return array
	 *
	 * @access public
	 */

	private function readBound($fname)
	{
		$contents = array();
		$content_str = "";
		$boundingbox = array();
		$i = 0;

		if (!file_exists($fname))
		{
			return false;
		}

		$contents = file($fname);

		for ($f = 0; $f < count($contents); $f++)
		{
			if (strstr($contents[$f], "%%BoundingBox"))
			{
				$content_str = $contents[$f];
				break;
			}
		}

		$b = explode(":", $content_str);
		$boundingbox = explode(" ", trim($b[1]));

		return $boundingbox;
	}
}
