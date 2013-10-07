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
 * redDesign Integration Plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgRedshop_ProductReddesign extends JPlugin
{
	private $plugin;

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

		$this->plugin = JPluginHelper::getPlugin('redshop_product', 'reddesign');

		$this->loadLanguage();
	}

	/**
	 * On Prepare redSHOP Product
	 *
	 * @param   string  &$template  Product Template Data
	 * @param   array   &$params    redSHOP Params list
	 * @param   object  $product    Product Data Object
	 *
	 * @return  void
	 */
	public function onPrepareProduct(&$template, &$params, $product)
	{
		if ($product->product_type != 'redDESIGN')
		{
			return;
		}

		$input         = JFactory::getApplication()->input;
		$view          = $input->get('view');
		$document      = JFactory::getDocument();

		if ($view != 'product')
		{
			return;
		}

		// Settlement to load attribute.js after quantity_discount.js
		unset($document->_scripts[JURI::root(true) . '/components/com_redshop/assets/js/attribute.js']);

		// Adding script from Plugin as we need customization in attribute.js for property background relation.
		$document->addScript('plugins/redshop_product/reddesign/js/attribute.js');

		$results  = $this->getPropertyBackgroundRelation($product->product_id);
		$relation = array();

		for ($i = 0, $n = count($results); $i < $n; $i++)
		{
			$result = $results[$i];
			$relation[$result->property_id] = $result->reddesign_designtype_id;
		}

		$script = "
			var propertyBackgroundRelation = " . json_encode($relation) . ";
		";

		$document->addScriptDeclaration($script);
	}

	/**
	 * Get Attribute Property and redDESIGN Background Mapping
	 *
	 * @param   integer  $productId  Product Id
	 *
	 * @throws  RuntimeException
	 * @return  mixed               Relation Object List
	 */
	private function getPropertyBackgroundRelation($productId)
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('reddesign_designtype_id, property_id')
			->from($db->quoteName('#__reddesign_attribute_mapping'))
			->where($db->quoteName('product_id') . ' = ' . (int) $productId);

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		return $result;
	}

	/**
	 * Stops loading redSHOP's jQuery file if true is returned.
	 * This is used to prevent jQuery conflicts and multiple jQuery loads
	 * during integration.
	 *
	 * @param   object  $data    Product data object.
	 * @param   string  $layout  Layout of the redSHOP view.
	 *
	 * @return bool If true than redSHOP won't load its jQuery file
	 */
	public function stopProductRedshopJQuery($data, $layout)
	{
		if ($data->product_type == 'redDESIGN' && $layout == 'default')
		{
			return true;
		}

		return false;
	}

	/**
	 * This method loads redDESIGN frontend editor into redSHOP frontend product detail view.
	 * Use {redDESIGN} tag inside redSHOP product template to display redDESIGN frontend editor.
	 *
	 * @param   string  &$template_desc  The string which contains all of the product view HTML.
	 * @param   object  $params          Menu item product view parameters.
	 * @param   object  $data            Product object.
	 *
	 * @return  void
	 */
	public function onAfterDisplayProduct(&$template_desc, $params, $data)
	{
		if ($data->product_type == 'redDESIGN')
		{
			$db = JFactory::getDbo();

			// Get design type ID.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_designtype_id'));
			$query->from($db->quoteName('#__reddesign_product_mapping'));
			$query->where($db->quoteName('product_id') . ' = ' . $data->product_id);
			$db->setQuery($query);
			$reddesignDesigntypeId = $db->loadResult();

			// Get background ID so you can get areas.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_background_id'))
				->from($db->quoteName('#__reddesign_backgrounds'))
				->where($db->quoteName('isPDFbgimage') . ' = ' . 1)
				->where($db->quoteName('reddesign_designtype_id') . ' = ' . $reddesignDesigntypeId);
			$db->setQuery($query);
			$backgroundId = $db->loadResult();

			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_area_id'))
				->from($db->quoteName('#__reddesign_areas'))
				->where($db->quoteName('reddesign_background_id') . ' = ' . $backgroundId);
			$db->setQuery($query);
			$areas = $db->loadColumn();

			// Get redDESIGN frontend HTML.
			$inputvars = array(
				'id'	=> $reddesignDesigntypeId
			);
			$input = new FOFInput($inputvars);

			ob_start();
			FOFDispatcher::getTmpInstance('com_reddesign', 'designtype', array('input' => $input))->dispatch();
			$html = ob_get_contents();
			ob_end_clean();

			// Get title.
			$htmlElement = explode('{RedDesignBreakTitle}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:Title}", $htmlElement, $template_desc);

			// Get form begin.
			$htmlElement = explode('{RedDesignBreakFormBegin}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:FormBegin}", $htmlElement, $template_desc);

			// Get backgrounds selection.
			$htmlElement = explode('{RedDesignBreakBackgrounds}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:BackgroundsSelect}", $htmlElement, $template_desc);

			// Get main image.
			$htmlElement = explode('{RedDesignBreakDesignImage}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:DesignImage}", $htmlElement, $template_desc);

			// Get button "Customize it!", this button can be turned on at the configuration.
			$htmlElement = explode('{RedDesignBreakButtonCustomizeIt}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:ButtonCustomizeIt}", $htmlElement, $template_desc);

			// Here's where it gets crazy. - Design Areas
			$htmlAreas = explode('{RedDesignBreakDesignAreas}', $html);
			$htmlAreas = $htmlAreas[1];

			// Get areas global title.
			$htmlElement = explode('{RedDesignBreakDesignAreasTitle}', $htmlAreas);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:AreasTitle}", $htmlElement, $template_desc);

			// Get middle tags of {redDESIGN:AreasLoop} enclosure.
			$areasLoopTemplate = explode('{redDESIGN:AreasLoopStart}', $template_desc);
			$areasLoopTemplate = explode('{redDESIGN:AreasLoopEnd}', $areasLoopTemplate[1]);
			$areasLoopTemplate = $areasLoopTemplate[0];

			$areasFinshedOutput = '';

			foreach ($areas as $areaId)
			{
				$areasLoopTemplateInstance = $areasLoopTemplate;

				// Get area specific content.
				$areaHtml = explode('{RedDesignBreakDesignArea' . $areaId . '}', $htmlAreas);
				$areaHtml = $areaHtml[1];

				// Get specific area title.
				$htmlElement = explode('{RedDesignBreakDesignAreaTitle}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:AreaTitle}", $htmlElement, $areasLoopTemplateInstance);

				// Get input text label.
				$htmlElement = explode('{RedDesignBreakDesignAreaInputTextLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:InputTextLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get input text.
				$htmlElement = explode('{RedDesignBreakDesignAreaInputText}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:InputText}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font input.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFont}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFont}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font size label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontSizeLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontSizeLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font size input.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontSize}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontSize}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColorLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColorLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColor}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColor}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColor}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColor}", $htmlElement, $areasLoopTemplateInstance);

				$areasFinshedOutput .= $areasLoopTemplateInstance;
			}

			$start = '{redDESIGN:AreasLoopStart}';
			$end = '{redDESIGN:AreasLoopEnd}';
			$template_desc = preg_replace('#(' . $start . ')(.*)(' . $end . ')#si', $areasFinshedOutput, $template_desc);

			// Get form end.
			$htmlElement = explode('{RedDesignBreakFormEndsAndJS}', $html);
			$htmlElement = $htmlElement[1];
			$template_desc = str_replace("{redDESIGN:FormEnd}", $htmlElement, $template_desc);

			$redDesignData = "<input type='hidden' name='task' value='add'><input type='hidden' id='redDesignData' name='redDesignData' value='' />";

			$template_desc = str_replace("<input type='hidden' name='task' value='add'>", $redDesignData, $template_desc);
		}
	}

	/**
	 * Update cart session variable with redDESIGN data.
	 * Method is called by the view and the results are imploded and displayed in a placeholder.
	 *
	 * @param   object  &$cart  The Product Template Data.
	 * @param   object  $data   The product params.
	 *
	 * @return  void
	 */
	public function onBeforeSetCartSession(&$cart, $data)
	{
		$idx = $cart['idx'];

		$cart[$idx]['redDesignData'] = $data['redDesignData'];
	}

	/**
	 * Can change sameProduct variable in addToCart function.
	 * That means if you return true as a value than the product will be added to a separate order line.
	 *
	 * @param   array  &$cart         Cart array.
	 * @param   array  $data          Data about product being added.
	 * @param   bool   &$sameProduct  Same product or not.
	 *
	 * @return  bool   $notSame True if you want new order line for the product in cart.
	 */
	public function checkSameCartProduct(&$cart, $data, &$sameProduct)
	{
		// Get product type
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('product_type'));
		$query->from($db->quoteName('#__redshop_product'));
		$query->where($db->quoteName('product_id') . ' = ' . $data['product_id']);
		$db->setQuery($query);
		$productType = $db->loadResult();

		if ($productType == 'redDESIGN')
		{
			$sameProduct = false;
		}
	}

	/**
	 * This is an event that is called when cart template replacement is started.
	 * ToDo: Images are coming from media/assets/designtypes/forcart and they stey there forever.
	 * ToDo: Delete them upon order complete to free server resources.
	 *
	 * @param   array   &$cart           Cart array.
	 * @param   string  &$product_image  Product image string.
	 * @param   object  $product         Product object.
	 * @param   int     $i               Position in the cart.
	 *
	 * @return  void
	 */
	public function changeCartOrderItemImage(&$cart, &$product_image, $product, $i)
	{
		if ($product->product_type = 'redDESIGN')
		{
			$redDesignData = json_decode($cart[$i]['redDesignData']);

			$product_image = "<div  class='product_image'><img src='" . $redDesignData->backgroundImgSrc . "'></div>";
		}
	}

	/**
	 * When adding same product it needs to update data from this different
	 * place because onBeforeSetCartSession works only once for one session.
	 *
	 * @param   object  &$cart  The Product Template Data.
	 * @param   object  $data   The product params.
	 * @param   int     $i      The product params.
	 *
	 * @return  void
	 */
	public function onSameCartProduct(&$cart, $data, $i)
	{
		$idx = $cart['idx'];

		$cart[$idx]['redDesignData'] = $data['redDesignData'];
	}

	/**
	 * Adds a javascript function call to the Add to cart click
	 *
	 * @param   object  $product  Product object.
	 * @param   array   $cart     Cart session object.
	 *
	 * @return  string
	 */
	public function onAddToCartClickJS($product, $cart)
	{
		$result = '';

		if ($product->product_type == 'redDESIGN')
		{
			$document = JFactory::getDocument();

			$js = '
					function generateRedDesignData() {
						var values = {};
						var inputs = akeeba.jQuery("#designform :input");

						inputs.each(function() {
							values[this.name] = akeeba.jQuery(this).val();
						});

						values["backgroundImgSrc"] = akeeba.jQuery("#background").attr("src");

						var jsonString = JSON.stringify(values);

						akeeba.jQuery("#redDesignData").val(jsonString);
					}

					function getExtraParams(frm) {
						return "&redDesignData=" + frm.redDesignData.value;
					}
			';
			$document->addScriptDeclaration($js);

			$result = 'generateRedDesignData();';
		}

		return $result;
	}

	/**
	 * This method should create PDF production file. It would be more suitable to
	 * have afterUpdateOrderStatus event and to create PDF production files on that
	 * event. But current redSHOP architecture updates order status from hundreds
	 * of different places instead of one.
	 * ToDo: Make a control inside redDESIGN which will trigger fuction for deleting
	 * ToDo: all PDFs related to not paid orders.
	 *
	 * @param   object  $cart     Cart object.
	 * @param   object  $rowitem  Order item object.
	 * @param   int     $i        Some kind of index or maybe even Order item ID. ToDo: Check use of this var and remove it from the trigger.
	 *
	 * @return void
	 */
	public function afterOrderItemSave($cart, $rowitem, $i)
	{
		$db = JFactory::getDbo();

		// Get product type
		$query = $db->getQuery(true);
		$query->select($db->quoteName('product_type'));
		$query->from($db->quoteName('#__redshop_product'));
		$query->where($db->quoteName('product_id') . ' = ' . $rowitem->product_id);
		$db->setQuery($query);
		$productType = $db->loadResult();

		if ($productType == 'redDESIGN' && !empty($cart[0]['redDesignData']))
		{
			// Get redDESIGN relevant data.
			$redDesignData = json_decode($cart[0]['redDesignData']);
			$preparedDesignData = $this->prepareDesignTypeData($redDesignData);
			$productionFileName = $this->createProductionFiles($preparedDesignData);

			// Insert record to the mapping table #__reddesign_orderitem_mapping.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('order_item_id', 'productionPdf', 'productionEps')));
			$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
			$query->where($db->quoteName('order_item_id') . ' = ' . $rowitem->order_item_id);
			$db->setQuery($query);
			$orderItem = $db->loadObject();

			$orderItemProductionFiles = new stdClass;
			$orderItemProductionFiles->order_item_id = $rowitem->order_item_id;
			$orderItemProductionFiles->productionPdf = $productionFileName;
			$orderItemProductionFiles->productionEps = $productionFileName;

			if (empty($orderItem))
			{
				$db->insertObject('#__reddesign_orderitem_mapping', $orderItemProductionFiles);
			}
			else
			{
				$db->updateObject('#__reddesign_orderitem_mapping', $orderItemProductionFiles, 'order_item_id');
			}
		}
	}

	/**
	 * Displays note to the order item at order detail backend view
	 *
	 * @param   object  $orderItem  Order item object.
	 *
	 * @return void
	 */
	public function onDisplayOrderItemNote($orderItem)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('order_item_id', 'productionPdf', 'productionEps')));
		$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
		$query->where($db->quoteName('order_item_id') . ' = ' . $orderItem->order_item_id);
		$db->setQuery($query);
		$orderItemMapping = $db->loadObject();

		if (!empty($orderItemMapping->productionPdf))
		{
			$productionPdf = FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/orders/pdf/' . $orderItemMapping->productionPdf . '.pdf');
			echo '<a href="' . $productionPdf . '" download="productionFile.pdf">PDF:<br/>' . $productionPdf . '</a><br/><br/>';

			$productionEps = FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/orders/eps/' . $orderItemMapping->productionEps . '.eps');
			echo '<a href="' . $productionEps . '" download="productionFile.eps">EPS:<br/>' . $productionEps . '</a>';
		}
	}

	/**
	 * Prepares Design Type data in JSON format.
	 *
	 * @param   array  $redDesignData  Data from the request.
	 *
	 * @return string $designTypeJSON Design type data JSON encoded.
	 */
	public function prepareDesignTypeData($redDesignData)
	{
		// Get design type data.
		$designTypeModel = FOFModel::getTmpInstance('Designtype', 'ReddesignModel')->reddesign_designtype_id($redDesignData->reddesign_designtype_id);
		$designType      = $designTypeModel->getItem($redDesignData->reddesign_designtype_id);

		$data = array();
		$data['designType'] = $designType;

		// Get Background Data
		$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($redDesignData->production_background_id);
		$data['designBackground'] = $backgroundModel->getItem($redDesignData->production_background_id);

		// Get designAreas
		$data['designAreas'] = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('reddesign_area_id'));
		$query->from($db->quoteName('#__reddesign_areas'));
		$query->where($db->quoteName('reddesign_background_id') . ' = ' . $redDesignData->production_background_id);
		$db->setQuery($query);
		$areaIds = $db->loadColumn();

		foreach ($areaIds as $areaId)
		{
			$area = array();
			$area['id'] = $areaId;

			$key = 'fontArea' . $areaId;
			$area['fontTypeId'] = $redDesignData->$key;

			$key = 'colorCode' . $areaId;
			$area['fontColor'] = $redDesignData->$key;

			$key = 'fontSize' . $areaId;

			if (!empty($redDesignData->$key))
			{
				$area['fontSize'] = $redDesignData->$key;
			}

			$key = 'textArea' . $areaId;
			$area['textArea'] = $redDesignData->$key;

			$data['designAreas'][] = $area;
		}

		$data['autoSizeData'] = json_decode($redDesignData->autoSizeData);

		return $data;
	}

	/**
	 * Create ProductPDF file for redDesign
	 *
	 * @param   array  $data  An array that holds design information
	 *
	 * @return   string  $pdfFileName  Newly generate PDF file name.
	 */
	public function createProductionFiles($data)
	{
		// Create production PDF file name
		$userId = JFactory::getUser()->id;
		$mangledname  = explode('.', $data['designBackground']->eps_file);
		$mangledname  = $mangledname[0];

		// Get a (very!) randomised name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverkey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverkey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $mangledname . microtime() . $serverkey;

		if (function_exists('sha256'))
		{
			$mangledname = sha256($sig);
		}
		elseif (function_exists('sha1'))
		{
			$mangledname = sha1($sig);
		}
		else
		{
			$mangledname = md5($sig);
		}

		$productionFileName = $userId . '-' . date('d-m-y-H-i-s') . '-' . $mangledname;

		$areas = $data['designAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epsTextFile = '';

		$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->eps_file;
		$previewFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->image_path;
		$pdfFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/';
		$epsFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/eps/';

		// Read EPS.
		$im = new Imagick;
		$im->readImage($epsFileLocation);
		$dimensions = $im->getImageGeometry();
		$imageWidth = $dimensions['width'];
		$imageHeight = $dimensions['height'];

		// Read preview size, for scaling.
		$previewImageSize = getimagesize($previewFileLocation);

		// Scaling ratio
		$ratio = $previewImageSize[0] / $imageWidth;

		$pdfLeftMargin = 28.35;
		$pdfTopMargin = 28.35;

		foreach ($areas as $area)
		{
			if ($area['fontTypeId'])
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area['id']);
				$fontType = $fontModel->getItem($area['fontTypeId']);
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $fontType->font_file;
			}
			else
			{
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($data['designBackground']->reddesign_background_id);
			$areaItem = $areaModel->getItem($area['id']);

			/*
			 * Scale back all used measures by using calculated ratio.
			 * PHP_ROUND_HALF_DOWN is used because test show that measures are scaled in that way.
			 */
			$areaItem->width = round($areaItem->width / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->height = round($areaItem->height / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->x1_pos = round($areaItem->x1_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->y1_pos = round($areaItem->y1_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->x2_pos = round($areaItem->x2_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->y2_pos = round($areaItem->y2_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);

			$rgbTextColorBuffer = $this->hex2RGB('#' . $area['fontColor']);
			$rgbTextColor = round($rgbTextColorBuffer['red'] * (1 / 255), 2);
			$rgbTextColor .= ' ';
			$rgbTextColor .= round($rgbTextColorBuffer['green'] * (1 / 255), 2);
			$rgbTextColor .= ' ';
			$rgbTextColor .= round($rgbTextColorBuffer['blue'] * (1 / 255), 2);

			if ($data['designType']->fontsizer == 'auto')
			{
				$autoSizeDataArray = $data['autoSizeData'];
				$autoSizeData = null;

				foreach ($autoSizeDataArray as $autoSizeDataElement)
				{
					if ($autoSizeDataElement->reddesign_area_id == $areaItem->reddesign_area_id)
					{
						$autoSizeData = $autoSizeDataElement;
					}
				}

				$fontSize = 0;

				if ($autoSizeData->fontSize)
				{
					$fontSize = $autoSizeData->fontSize / $ratio;
				}

				$noOfLines = count($autoSizeData->stringLines);

				$bottomoffset = $imageHeight - $areaItem->y2_pos + $pdfTopMargin;

				$maxHeight = $autoSizeData->maxHeight;
				$offsetOverallArea = (($areaItem->height - ($fontSize * $maxHeight * $noOfLines)) / 2);
				$offsetOverallArea = $offsetOverallArea + $bottomoffset;

				/*
				 * In the POSTSCRIPT default coordinate system, the origin is in the lower left hand corner of the current page.
				 * As usual, x increases to the right. But, y increases upward!
				 */

				$offsetLeft = $areaItem->x1_pos + $pdfLeftMargin + ($areaItem->width / 2);

				for ($h = 0;$h < $noOfLines;$h++)
				{
					if ($noOfLines == 1)
					{
						// Because other parts of the algorithm doesn't respect em-square we have to take a constant like 0.15 (15%) of the font size
						// because there is some empty space between chars and em-square's border.
						$offsetTop = (($imageHeight - $areaItem->y2_pos) + $pdfTopMargin + ($areaItem->height / 2)) - (($fontSize / 2) - ($fontSize * 0.15));
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

					$epsText .= "\n$rgbTextColor setrgbcolor";
					$epsText .= "\ngsave\n";

					$epsAreaText .= "\n$rgbTextColor setrgbcolor";
					$epsAreaText .= "\ngsave\n";

					$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
					$epsAreaText .= "\n (" . $autoSizeData->stringLines[$h] . ") dup stringwidth pop 2 div neg 0 rmoveto true charpath ";
					$epsAreaText .= "\n fill";
					$epsAreaText .= "\n showpage";

					$epsText .= "\n $offsetLeft $offsetTop moveto";
					$epsText .= "\n (" . $autoSizeData->stringLines[$h] . ")";
					$epsText .= "\n cshow";
				}
			}
			else
			{
				$area['fontSize'] = round($area['fontSize'] / $ratio, 0);

				/*
				 * Calculate offset.
				 * In the POSTSCRIPT default coordinate system, the origin is in the lower left hand corner of the current page.
				 * As usual, x increases to the right. But, y increases upward!
				 */
				$alignmentPostScript = '';

				if ((int) $areaItem->textalign == 1)
				{
					// Left.
					$offsetLeft = $areaItem->x1_pos;
				}
				elseif ((int) $areaItem->textalign == 2)
				{
					// Right.
					$offsetLeft = $areaItem->x2_pos;
					$alignmentPostScript = "\n (" . $area['textArea'] . ") dup stringwidth pop neg 0 rmoveto";
				}
				else
				{
					// Center.
					$offsetLeft = ($areaItem->x1_pos + $areaItem->width + (2 * $pdfLeftMargin)) / 2;
					$alignmentPostScript = "\n (" . $area['textArea'] . ") dup stringwidth pop 2 div neg 0 rmoveto";
				}

				$offsetTop = (($imageHeight - $areaItem->y2_pos) + $pdfTopMargin + ($areaItem->height / 2));
				$offsetTop -= (($area['fontSize'] / 2) - ($area['fontSize'] * 0.15));
				$offsetLeft += $pdfLeftMargin;

				$epsText .= $epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $area['fontSize'] . "  scalefont setfont\n";

				$epsText .= "\n$rgbTextColor setrgbcolor";
				$epsText .= "\ngsave\n";

				$epsAreaText .= "\n$rgbTextColor setrgbcolor";
				$epsAreaText .= "\ngsave\n";

				$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
				$epsAreaText .= $alignmentPostScript;
				$epsAreaText .= "\n (" . $area['textArea'] . ") true charpath ";

				$epsAreaText .= "\n fill";
				$epsAreaText .= "\n showpage";

				$epsText .= "\n $offsetLeft $offsetTop moveto";
				$epsText .= "\n (" . $area['textArea'] . ")";
				$epsText .= "\n cshow";
			}
		}

		$epsText .= "\ngrestore\n";

		$tmpEpsFile = $epsFilePath . "tmp_" . $productionFileName . ".eps";
		$tmpTextEpsFile = $epsFilePath . "tmptext_" . $productionFileName . ".eps";

		$epsFileName = $epsFilePath . $productionFileName . ".eps";

		$tempFile = "%!PS";
		$tempFile .= "\n%%Creator:redDESIGN";
		$tempFile .= "\n%%Title:" . $productionFileName . ".pdf";
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
		$epsFile .= "\n%%Creator:redDESIGN";
		$epsFile .= "\n%%Title:" . $productionFileName . ".pdf";
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

		// Create pdf.
		ob_clean();

		$pdfFileName = $pdfFilePath . $productionFileName . ".pdf";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOEPS -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true  -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$pdfFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpEpsFile;
		exec($cmd);

		$epsFileName = $epsFilePath . $productionFileName . ".eps";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOEPS -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true  -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$epsFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpTextEpsFile;
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

		return $productionFileName;
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
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param   string   $hexStr          (hexadecimal color value)
	 * @param   boolean  $returnAsString  (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param   string   $seperator       (to separate RGB values. Applicable only if second parameter is true.)
	 *
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	public function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
	{
		// Gets a proper hex string
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
		$rgbArray = array();

		if (strlen($hexStr) == 6)
		{
			// If a proper hex code, convert using bitwise operation. No overhead... faster.
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		elseif (strlen($hexStr) == 3)
		{
			// If shorthand notation, need some string manipulations.
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		}
		else
		{
			// Invalid hex color code.
			return false;
		}

		// Returns the rgb string or the associative array.
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
	}
}
