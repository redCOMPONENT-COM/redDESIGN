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
		$newProduct->published = 1;
		$newProduct->manufacturer_id = $manufacturer_id;
		$db->insertObject('#__redshop_product', $newProduct);
		$product_id = $db->insertid();

		// Update Product with ProductNumber
		$updateProduct = new stdClass;
		$updateProduct->product_id = $product_id;
		$updateProduct->product_number = "redDESIGN" . $product_id;
		$db->updateObject('#__redshop_product', $updateProduct, 'product_id');

		// Add Category for new Product
		$productCategory = new stdClass;
		$productCategory->category_id  = $category_id;
		$productCategory->product_id  = $product_id;
		$db->insertObject('#__redshop_product_category_xref', $productCategory);

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
	 */
	public function createPdfProductfile($data)
	{
		$session = JFactory::getSession();
		$productionFileName = $session->get('customizedImage');

		$areas = $data['designAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epsTextFile = '';

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

			if ($data['designType']->fontsizer == 'auto')
			{
				$AutoSizeData = $session->get('AutoSizeData');

				if ($AutoSizeData['FontSize'])
				{
					$fontSize = $AutoSizeData['FontSize'];
				}

				$noOfLines = count($AutoSizeData['perLineCharArr']);

				$bottomoffset = $imageHeight - $this->areaItem->y2_pos + 28.35;

				$maxHeight = $AutoSizeData['maxHeight'];
				$offsetOverallArea = (($this->areaItem->height - ($fontSize * $maxHeight * $noOfLines)) / 2);
				$offsetOverallArea = $offsetOverallArea + $bottomoffset;

				$offsetLeft = $this->areaItem->x1_pos + 28.35 + ($this->areaItem->width / 2);

				for ($h = 0;$h < $noOfLines;$h++)
				{
					if ($noOfLines == 1)
					{
						$offsetTop = 28.35 + $AutoSizeData['PDFoffsetTop'];
					}
					else
					{
						$gap = 1;

						if ($noOfLines > 3)
						{
							$gap = ($fontSize * 1.1) / ($noOfLines - 1);
						}

						$gap = $gap / 2;

						$offsetTop = $offsetOverallArea - $gap + ($fontSize * $maxHeight * (($noOfLines - 1) - $h)) * 1.1;
					}

					$epsText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $fontSize . "  scalefont setfont\n";
					$epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $fontSize . "  scalefont setfont\n";

					$epsText .= "\n0 0 0 setrgbcolor";
					$epsText .= "\ngsave\n";

					$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
					$epsAreaText .= "\n (" . $AutoSizeData['perLineCharArr'][$h] . ") dup stringwidth pop 2 div neg 0 rmoveto true charpath ";
					$epsAreaText .= "\n fill";
					$epsAreaText .= "\n showpage";

					$epsText .= "\n $offsetLeft $offsetTop moveto";
					$epsText .= "\n (" . $AutoSizeData['perLineCharArr'][$h] . ")";
					$epsText .= "\n cshow";
				}
			}
			else
			{
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
		}

		$epsText .= "\ngrestore\n";

		$tmpEpsFile = $epsFilePath . "tmp_" . $productionFileName . ".eps";
		$tmpTextEpsFile = $epsFilePath . "tmptext_" . $productionFileName . ".eps";

		$epsFileName = $epsFilePath . "reddesign" . $productionFileName . ".pdf";

		$tempFile = "%!PS";
		$tempFile .= "\n%%Creator:reddesign";
		$tempFile .= "\n%%Title:reddesign" . $productionFileName;
		$tempFile .= "\n%%LanguageLevel: 3";
		$tempFile .= "\n%%DocumentData: Clean7Bit";
		$tempFile .= "\n%%EndComments";
		$tempFile .= "\n";
		$tempFile .= "\n%%BeginProlog";
		$tempFile .= "\n/BeginEPSF {";
		$tempFile .= "\n/EPSFsave save def";
		$tempFile .= "\ncount /OpStackSize exch def";
		$tempFile .= "\n/DictStackSize countdictstack def";
		$tempFile .= "\n% turn off showpage";
		$tempFile .= "\n/showpage {} def";
		$tempFile .= "\n% set up default graphics state";
		$tempFile .= "\n0 setgray 0 setlinecap";
		$tempFile .= "\n1 setlinewidth 0 setlinejoin";
		$tempFile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$tempFile .= "\n/languagelevel where";
		$tempFile .= "\n{pop languagelevel 1 ne";
		$tempFile .= "\n{false setstrokeadjust false setoverprint} if";
		$tempFile .= "\n} if";
		$tempFile .= "\n} bind def";
		$tempFile .= "\n";
		$tempFile .= "\n/EndEPSF {";
		$tempFile .= "\ncount OpStackSize sub";
		$tempFile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$tempFile .= "\ncountdictstack DictStackSize sub";
		$tempFile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$tempFile .= "\nEPSFsave restore";
		$tempFile .= "\n} bind def";
		$tempFile .= "\n";
		$tempFile .= "\n%%EndProlog";
		$tempFile .= "\n%%Page: 1 1";
		$tempFile .= "\n/pagesave save def";
		$tempFile .= "\n";
		$tempFile .= "\n 0 0 translate";

		if (file_exists($epsFileLocation))
		{
			$tempFile .= "\nBeginEPSF";
			$tempFile .= "\n 0 0 translate";
			$tempFile .= "\n%%BeginDocument: danske.eps";
			$tempFile .= "\n(" . $epsFileLocation . ") run";
			$tempFile .= "\n%%EndDocument";
			$tempFile .= "\nEndEPSF";
		}

		$tempFile .= "\npagesave restore showpage";

		// Create temp eps file for reading bounding box...
		$tempFile .= "\n%%EOF";

		$tmpEpsImage = $epsFilePath . "tmp_eps_" . $productionFileName . ".eps";
		$tmpBound = $epsFilePath . "tmp_bound_" . $productionFileName . ".ps";

		$fp = fopen($tmpEpsImage, "w");
			fwrite($fp, $tempFile);
			fclose($fp);

		$imageWidth = $imageWidth + 56.7;
		$imageHeight = $imageHeight + 56.7;

		$cmd = "gs -dBATCH -dNOPAUSE -sOutputFile=$tmpBound -sDEVICE=ps2write  \-c '<< /PageSize
				[$imageWidth $imageHeight]  >> setpagedevice'  -f" . $tmpEpsImage;
		exec($cmd);

		$imageBound = $this->readBound($tmpBound);
		$epsFile  = "%!PS-Adobe-3.1 EPSF-3.1";
		$epsFile .= "\n%%Creator:reddesign";
		$epsFile .= "\n%%Title:reddesign" . $productionFileName;
		$epsFile .= "\n%%LanguageLevel: 3";
		$epsFile .= "\n%%DocumentData: Clean7Bit";
		$epsFile .= "\n%%EndComments";
		$epsFile .= "\n";
		$epsFile .= "\n%%BeginProlog";
		$epsFile .= "\n/BeginEPSF {";
		$epsFile .= "\n/EPSFsave save def";
		$epsFile .= "\ncount /OpStackSize exch def";
		$epsFile .= "\n/DictStackSize countdictstack def";
		$epsFile .= "\n% turn off showpage";
		$epsFile .= "\n/showpage {} def";
		$epsFile .= "\n% set up default graphics state";
		$epsFile .= "\n0 setgray 0 setlinecap";
		$epsFile .= "\n1 setlinewidth 0 setlinejoin";
		$epsFile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$epsFile .= "\n/languagelevel where";
		$epsFile .= "\n{pop languagelevel 1 ne";
		$epsFile .= "\n{false setstrokeadjust false setoverprint} if";
		$epsFile .= "\n} if";
		$epsFile .= "\n} bind def";
		$epsFile .= "\n";
		$epsFile .= "\n/EndEPSF {";
		$epsFile .= "\ncount OpStackSize sub";
		$epsFile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$epsFile .= "\ncountdictstack DictStackSize sub";
		$epsFile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$epsFile .= "\nEPSFsave restore";
		$epsFile .= "\n} bind def";
		$epsFile .= "\n";

		$epsFile .= "\n/x 1 def";
		$epsFile .= "\n/cshow		%  (str)  =>  ---";
		$epsFile .= "\n{ dup stringwidth pop -2 div 0 rmoveto show } bind def";
		$epsFile .= "\n/alignshow		%  (str)  =>  ---";
		$epsFile .= "\n	{dup stringwidth pop neg 0 rmoveto show} bind def";
		$epsFile .= "\n/nl { x currentpoint exch pop 16 sub moveto } bind def";
		$epsFile .= "\n%%EndProlog";
		$epsFile .= "\n%%Page: 1 1";
		$epsFile .= "\n/pagesave save def";
		$epsFile .= "\n";

		if (file_exists($epsFileLocation))
		{
			$epsFile .= "\nBeginEPSF";
			$epsFile .= "\n 0 0 translate";

			if ($imageBound[0] > 100)
			{
				$epsFile .= "\n 0 0 translate";
			}
			elseif ($imageBound[3] == 0)
			{
				$epsFile .= "\n 0 0 translate";
			}
			else
			{
				$epsFile .= "\n " . $pdfLeftMargin . " " . $pdfTopMargin . " translate";
			}

			$epsFile .= "\n% 0 0 " . ($imageWidth) . " " . ($imageHeight);

			$epsFile .= "\n%%BeginDocument: danske.eps";
			$epsFile .= "\n(" . $epsFileLocation . ") run";
			$epsFile .= "\n%%EndDocument";
			$epsFile .= "\nEndEPSF";
		}

		$epsFile .= "\nBeginEPSF";
		$epsFile .= "\nclear";
		$epsFile .= "\n 0 0 translate";
		$epsTextFile .= $epsFile;
		$epsFile .= "\n%%BeginDocument: text.eps";
		$epsFile .= "\n" . $epsAreaText;
		$epsFile .= "\n%%EndDocument";
		$epsFile .= "\nEndEPSF";

		// Create temp eps file for reading bounding box...
		$epsFile .= "\n%%EOF";

		$epsTextFile .= "\n%%BeginDocument: text.eps";
		$epsTextFile .= "\n" . $epsText;
		$epsTextFile .= "\n%%EndDocument";
		$epsTextFile .= "\nEndEPSF";
		$epsTextFile .= "\n%%EOF";

		$fp = fopen($tmpEpsFile, "w");
			fwrite($fp, $epsFile);
			fclose($fp);

		$fp = fopen($tmpTextEpsFile, "w");
		fwrite($fp, $epsTextFile);
		fclose($fp);

		// Create pdf ...
		ob_clean();

		$pdfFileName = $pdfFilePath . "reddesign" . $productionFileName . ".pdf";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOEPS -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true  -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$pdfFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpEpsFile;
		exec($cmd);

		$cmd = "gs -dBATCH -dNOPAUSE  -dNOEPS -dEPSCrop -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true -dSubsetFonts=false ";
		$cmd .= "-dOptimize=false -sOutputFile=$epsFileName -sDEVICE=pdfwrite  \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice' -f" . $tmpTextEpsFile;
		exec($cmd);

		if (file_exists($tmpTextEpsFile))
		{
			unlink($tmpTextEpsFile);
		}

		if (file_exists($tmpEpsFile))
		{
			unlink($tmpEpsFile);
		}

		if (file_exists($tmpEpsImage))
		{
			unlink($tmpEpsImage);
		}

		if (file_exists($tmpBound))
		{
			unlink($tmpBound);
		}
	}

	/**
	 * Read BoundingArea of Image
	 *
	 * @param   string  $fname  location of image
	 *
	 * @return array
	 */
	private function readBound($fname)
	{
		$contentStr = "";

		if (!file_exists($fname))
		{
			return false;
		}

		$contents = file($fname);

		for ($f = 0; $f < count($contents); $f++)
		{
			if (strstr($contents[$f], "%%BoundingBox"))
			{
				$contentStr = $contents[$f];
				break;
			}
		}

		$box = explode(":", $contentStr);
		$boundingBox = explode(" ", trim($box[1]));

		return $boundingBox;
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
}
