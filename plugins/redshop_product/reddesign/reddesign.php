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

		$input    = JFactory::getApplication()->input;
		$view     = $input->get('view');
		$document = JFactory::getDocument();

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
			$relation[$result->property_id] = $result->designtype_id;
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
		$query->select('designtype_id, property_id')
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
			$app = JFactory::getApplication();
			$db = JFactory::getDbo();

			$designTypesModel = RModel::getAdminInstance('Designtypes', array(), 'com_reddesign');
			$designTypesProductMapping = $designTypesModel->getProductDesignTypesMapping($data->product_id);

			$designTypeId = $app->input->getInt('designtype_id', null);

			if (empty($designTypeId))
			{
				$designTypeId = $designTypesProductMapping->default_designtype_id;
			}

			$displayData = new stdClass;
			$displayData->config = ReddesignEntityConfig::getInstance();
			$displayData->relatedDesignTypes = explode(',', $designTypesProductMapping->related_designtype_ids);

			$designTypeModel = RModel::getAdminInstance('Designtype', array(), 'com_reddesign');
			$designTypeModel->setState('id', $designTypeId);
			$displayData->item = $designTypeModel->getItem();

			$backgroundModel = RModel::getAdminInstance('Backgrounds', array(), 'com_reddesign');
			$backgroundModel->setState('designtype_id', $designTypeId);
			$displayData->backgrounds = $backgroundModel->getItems();

			foreach ($displayData->backgrounds as $background)
			{
				if ($background->isDefaultPreview)
				{
					$displayData->defaultPreviewBg = $background;
				}

				if ($background->isProductionBg)
				{
					$displayData->productionBackground = $background;
				}
			}

			// Default background measures.
			$xml = simplexml_load_file(JURI::root() . 'media/com_reddesign/backgrounds/' . $displayData->defaultPreviewBg->svg_file);
			$displayData->defaultPreviewBgAttributes = $xml->attributes();
			$displayData->defaultPreviewBgAttributes->width  = str_replace('px', '', $displayData->defaultPreviewBgAttributes->width);
			$displayData->defaultPreviewBgAttributes->height = str_replace('px', '', $displayData->defaultPreviewBgAttributes->height);

			/** @var ReddesignModelFonts $fontsModel */
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');
			$displayData->fonts = $fontsModel->getItems();

			if (empty($displayData->imageSize))
			{
				$displayData->imageSize = array(0, 0);
			}

			if (empty($displayData->defaultPreviewBg) || empty($displayData->productionBackground))
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_DESIGNTYPE_NO_BACKGROUNDS'), 'notice');
			}
			else
			{
				/** @var ReddesignModelAreas $areasModel */
				$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
				$areasModel->setState('background_id', $displayData->productionBackground->id);
				$displayData->productionBackgroundAreas = $areasModel->getItems();

				$displayData->imageSize = getimagesize(JURI::root() . 'media/com_reddesign/backgrounds/' . $displayData->defaultPreviewBg->image_path);

				$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayData->productionBackgroundAreas);
				$displayData->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);
			}

			if (empty($displayData->productionBackgroundAreas))
			{
				$app->enqueueMessage(JText::_('COM_REDDESIGN_DESIGNTYPE_NO_DESIGN_AREAS'), 'notice');
			}

			$html = RLayoutHelper::render('default', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl');

			// Get background ID so you can get areas.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'))
				->from($db->quoteName('#__reddesign_backgrounds'))
				->where($db->quoteName('isProductionBg') . ' = ' . 1)
				->where($db->quoteName('designtype_id') . ' = ' . $designTypeId);
			$db->setQuery($query);
			$backgroundId = $db->loadResult();

			// Get areas for the template tags replacement.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'))
				->from($db->quoteName('#__reddesign_areas'))
				->where($db->quoteName('background_id') . ' = ' . $backgroundId);
			$db->setQuery($query);
			$areas = $db->loadColumn();

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

			if (!empty($areas))
			{
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
		if (!empty($data['order_item_id']))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('redDesignData'));
			$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
			$query->where($db->quoteName('order_item_id') . ' = ' . $data['order_item_id']);
			$db->setQuery($query);
			$redDesignData = $db->loadResult();

			$idx = $cart['idx'];

			$cart[$idx]['redDesignData'] = $redDesignData;
		}
		else
		{
			$idx = $cart['idx'];
			$cart[$idx]['redDesignData'] = $data['redDesignData'];
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
		$db = JFactory::getDbo();
		$config = ReddesignEntityConfig::getInstance();
		$fontUnit = $config->getFontUnit();

		// Get product type
		$query = $db->getQuery(true);
		$query->select($db->quoteName('product_type'));
		$query->from($db->quoteName('#__redshop_product'));
		$query->where($db->quoteName('product_id') . ' = ' . $product->product_id);
		$db->setQuery($query);
		$productType = $db->loadResult();

		$query->select($db->quoteName('name'));
		$query->from($db->quoteName('#__reddesign_fonts'));
		$db->setQuery($query);
		$fonts = $db->loadObjectList();

		// $fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true));
		// $fonts = $fontsModel->getItems();

		if ($productType == 'redDESIGN')
		{
			RHelperAsset::load('snap.svg-min.js', 'com_reddesign');

			if (!empty($product->order_item_id))
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName('redDesignData'));
				$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
				$query->where($db->quoteName('order_item_id') . ' = ' . (int) $product->order_item_id);
				$db->setQuery($query);
				$redDesignData = json_decode($db->loadResult());
			}
			else
			{
				$redDesignData = json_decode($cart[$i]['redDesignData']);
			}

			if (isset($redDesignData->svgImags))
			{
				$document = JFactory::getDocument();

				// Get plugin param for width & height
				$defaultPreviewWidth = $this->params->get('defaultCartPreviewWidth', 0);
				$defaultPreviewHeight = $this->params->get('defaultPreviewHeight', 0);
				$jsWidth = 0.0;
				$jsHeight = 0.0;
				$jsRatio = 1;

				if (($defaultPreviewWidth == 0) && ($defaultPreviewHeight == 0))
				{
					// No input of width && height, get the original size
					$jsWidth = (float) $redDesignData->svgWidth;
					$jsHeight = (float) $redDesignData->svgHeight;
				}
				elseif ($defaultPreviewWidth == 0)
				{
					// Only height has input
					$jsHeight = (float) $defaultPreviewHeight;
					$jsRatio = $jsHeight / (float) $redDesignData->svgHeight;
					$jsWidth = $jsRatio * (float) $redDesignData->svgWidth;
				}
				elseif ($defaultPreviewHeight == 0)
				{
					// Only width has input
					$jsWidth = (float) $defaultPreviewWidth;
					$jsRatio = $jsWidth / (float) $redDesignData->svgWidth;
					$jsHeight = $jsRatio * (float) $redDesignData->svgHeight;
				}
				else
				{
					// All width & height has input
					$jsWidth = (float) $defaultPreviewWidth;
					$jsHeight = (float) $defaultPreviewHeight;
					$widthRatio = $jsWidth / (float) $redDesignData->svgWidth;
					$heightRatio = $jsHeight / (float) $redDesignData->svgHeight;
					$jsRatio = ($widthRatio > $heightRatio) ? $heightRatio : $widthRatio;
				}

				foreach ($fonts as $font => $f)
				{
					$fontFile = 'fonts/' . $f->name . '.js';
					RHelperAsset::load($fontFile, 'com_reddesign');
				}

				$backgroundsModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
				$backgroundsModel->setState('designtype_id', $redDesignData->designtype_id);
				$backgrounds = $backgroundsModel->getItems();

				foreach ($backgrounds as $background)
				{
					if ($background->isDefaultPreview)
					{
						$defaultPreviewBg = $background;

						break;
					}
				}

				$imageUrl = JURI::base() . 'media/com_reddesign/backgrounds/' . $defaultPreviewBg->svg_file;

				// $product_image 	= "<div  class='product_image'><img width='" . CART_THUMB_WIDTH . "' src='" . $redDesignData->backgroundImgSrc . "'></div>";
				$product_image  = "<div id='product_image_" . $i . "' style='position: relative;'>";
				$product_image 	.= "<svg id='svg_image_" . $i . "' style='position: absolute;width: 100%; height: 100%; top: 0; left: 0;'></svg>";
				$product_image 	.= "</div>";

				$js = '
					jQuery(document).ready(function () {
						var previewWidth = ' . $jsWidth . ';
						var scalingImageForPreviewRatio = ' . $jsRatio . ';
						var previewHeight = ' . $jsHeight . ';
						var svg_' . $i . ' = Snap("#svg_image_' . $i . '");
						Snap.load(
							"' . $imageUrl . '",
							function (f) {
								jQuery("#product_image_' . $i . '").css("width", previewWidth + "px");
								jQuery("#product_image_' . $i . '").css("height", previewHeight + "px");
								svg_' . $i . '.append(f);
								var group_' . $i . ' = Snap.parse(\'<g id="areaBoxesLayer' . $i . '">' . urldecode($redDesignData->svgImags) . '</g>\');

								// Set preview size at loaded file.
								var loadedSvgFromFile = jQuery("#svg_image_' . $i . '").find("svg")[0];
								loadedSvgFromFile.setAttribute("width", "");
								loadedSvgFromFile.setAttribute("height", "");

								// Set preview size at svg container element.
								var rootElement = document.getElementById("svg_image_' . $i . '");
								rootElement.setAttribute("width", "");
								rootElement.setAttribute("height", "");
								rootElement.setAttribute("overflow", "hidden");

								svg_' . $i . '.add(group_' . $i . ');

								// Resize text elements
								jQuery("#areaBoxesLayer' . $i . ' text").each(function (index){
									var fontSize = parseFloat(jQuery(this).attr("font-size"));
									fontSize = fontSize * scalingImageForPreviewRatio;
									jQuery(this).attr("font-size", fontSize + "' . $fontUnit . '");

									var xPos = parseFloat(jQuery(this).attr("x"));
									xPos = xPos * scalingImageForPreviewRatio;
									jQuery(this).attr("x", xPos);

									var yPos = parseFloat(jQuery(this).attr("y"));
									yPos = yPos * scalingImageForPreviewRatio;
									jQuery(this).attr("y", yPos);
								});
							}
						);
    				});
				';

				$document->addScriptDeclaration($js);
			}
		}
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
						var inputs = jQuery("#designform :input");

						inputs.each(function() {
							values[this.name] = jQuery(this).val();
						});

						values["enteredDimensionunit"] = "cm";

						var areas = rootSnapSvgObject.select("#areaBoxesLayer");
						values["svgImags"] = encodeURIComponent(areas.innerSVG());
						values["designtype_id"] = jQuery("#designtype_id").val();
						values["svgWidth"] = rootSnapSvgObject.attr("width");
						values["svgHeight"] = rootSnapSvgObject.attr("height");

						var jsonString = JSON.stringify(values);

						jQuery("#redDesignData").val(jsonString);

						//getExtraParamsArray.redDesignData = encodeURIComponent(jsonString);
					}
			';
			$document->addScriptDeclaration($js);

			$result = 'generateRedDesignData();';
		}

		return $result;
	}

	/**
	 * This method saves redDESIGN customization data which is later used for production files creation.
	 *
	 * @param   object  $cart     Cart object.
	 * @param   object  $rowitem  Order item object.
	 * @param   int     $i        Some kind of index or maybe even Order item ID.
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

		if ($productType == 'redDESIGN' && !empty($cart[$i]['redDesignData']))
		{
			// Insert record to the mapping table #__reddesign_orderitem_mapping.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('redDesignData'));
			$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
			$query->where($db->quoteName('order_item_id') . ' = ' . $rowitem->order_item_id);
			$db->setQuery($query);
			$orderItem = $db->loadResult();

			$orderItemProductionFiles = new stdClass;
			$orderItemProductionFiles->order_item_id = $rowitem->order_item_id;
			$orderItemProductionFiles->productionPdf = '';
			$orderItemProductionFiles->productionEps = '';
			$orderItemProductionFiles->redDesignData = $cart[$i]['redDesignData'];

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
		$query->select($db->quoteName(array('order_item_id', 'productionPdf', 'productionEps', 'redDesignData')));
		$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
		$query->where($db->quoteName('order_item_id') . ' = ' . $orderItem->order_item_id);
		$db->setQuery($query);
		$orderItemMapping = $db->loadObject();

		$redDesignData = json_decode($orderItemMapping->redDesignData);
		$redDesignData = $this->prepareDesignTypeData($redDesignData);

		if (count($orderItemMapping) > 0)
		{
			echo '<div id="customDesignData' . $orderItem->order_item_id . '" style="margin: 15px 0 15px 0;">' .
					'<span><strong>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_DETAILS') . '</strong></span><br/>';

			foreach ($redDesignData['designAreas'] as $area)
			{
				// Get font name.
				if (empty($area['fontTypeId']))
				{
					$fontName = 'Arial';
				}
				else
				{
					$fontModel = RModel::getAdminInstance('Font', array('ignore_request' => true), 'com_reddesign');
					$fontName = $fontModel->getItem($area['fontTypeId']);
					$fontName = $fontName->title;
				}

				// Get text color
				if (strpos($area['fontColor'], '#') !== false)
				{
					$cmykCode = $this->rgb2cmyk($this->hex2rgb($area['fontColor']));
					$cmyk = 'C: ' . $cmykCode['c'] . ' ' . 'M: ' . $cmykCode['m'] . ' ' . 'Y: ' . $cmykCode['y'] . ' ' . 'K: ' . $cmykCode['k'];
					$fontColor = '<span style="margin-right:5px; background-color: ' . $area['fontColor'] . ';" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
					$fontColor .= '<span>' . $cmyk . '</span>';
				}
				else
				{
					$cmykCode = $this->rgb2cmyk($this->hex2rgb('#' . $area['fontColor']));
					$cmyk = 'C: ' . $cmykCode['c'] . ' ' . 'M: ' . $cmykCode['m'] . ' ' . 'Y: ' . $cmykCode['y'] . ' ' . 'K: ' . $cmykCode['k'];
					$fontColor  = '<span style="margin-right:5px; background-color:#' . $area['fontColor'] . ';" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
					$fontColor .= '<span>' . $cmyk . '</span>';
				}

				if (empty($area['fontSize']))
				{
					$area['fontSize'] = JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_AUTO_FONT_SIZE');
				}

				echo '<br/>' .
					'<div id="area' . $area['id'] . '">' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_AREA') . '</div>' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_TEXT') . $area['textArea'] . '</div>' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_FONT_NAME') . $fontName . '</div>' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_FONT_SIZE') . $area['fontSize'] . '</div>' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_TEXT_COLOR') . $fontColor . '</div>' .
					'</div>';
			}

			echo '</div>';

			if (!empty($orderItemMapping->productionPdf))
			{
				echo '<div><strong>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_PRODUCTION_FILES') . '</strong></div><br/>';

				$downloadFileName = 'production-file-' . $orderItem->order_id . '-' . $orderItem->order_item_id;

				$productionPdf = JURI::root() . 'media/com_reddesign/backgrounds/orders/pdf/' . $orderItemMapping->productionPdf . '.pdf';
				echo '<a href="' . $productionPdf . '" download="' . $downloadFileName . '.pdf">' .
					JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_DOWNLOAD') .
					' PDF</a><br/><br/>';

				$productionEps = JURI::root() . 'media/com_reddesign/backgrounds/orders/eps/' . $orderItemMapping->productionEps . '.eps';
				echo '<a href="' . $productionEps . '" download="' . $downloadFileName . '.eps">' .
					JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_DOWNLOAD') .
					' EPS</a>';
			}
			else
			{
				$button = '<button type="button" onclick="createProductionFiles(' . $orderItem->order_item_id . ',' . $orderItem->order_id . ')">'
					. JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CREATE_PRODUCTION_FILES')
					. '</button>';
				echo $button;

				echo '<div id="linksContainer' . $orderItem->order_item_id . '" style="margin: 15px 0 15px 0;"></div>';

				$document = JFactory::getDocument();
				$js = '
						function createProductionFiles(orderItemId, orderId) {
									var req = new Request.HTML({
										method: "get",
										url: "' . JURI::base() . 'index.php?option=com_reddesign&view=designtype&task=ajaxCustomizeDesign&format=raw",
										data: { "orderItemId" : orderItemId, "orderId" : orderId },
										onRequest: function(){
											$("linksContainer" + orderItemId).set("text", "' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CREATING_PRODUCTION_FILES') . '");
										},
										update: $("linksContainer" + orderItemId),
										onFailure: function(){
											$("linksContainer" + orderItemId).set("text", "' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CAN_NOT_CREATE_PRODUCTION_FILES') . '");
										}
									}).send();
					}
				';
				$document->addScriptDeclaration($js);
			}
		}
	}

	/**
	 * Converts hexadecimal color code to RGB color code. Need for conversion to CMYK.
	 *
	 * @param   string  $hex  Hexadecimal color code.
	 *
	 * @return  array   $rgb  RGB color code.
	 */
	public function hex2rgb($hex)
	{
		$color = str_replace('#', '', $hex);
		$rgb = array('r' => hexdec(substr($color, 0, 2)), 'g' => hexdec(substr($color, 2, 2)), 'b' => hexdec(substr($color, 4, 2)));

		return $rgb;
	}

	/**
	 * Converts RGB color code to CMYK color code.
	 *
	 * @param   mixed  $rOrArray  Color code in RGB or R part of color code.
	 * @param   int    $g         G part of the RGB color code.
	 * @param   int    $b         B part of the RGB color code.
	 *
	 * @return  array  array  Array of CMYK values.
	 */
	public function rgb2cmyk($rOrArray, $g=0, $b=0)
	{
		if (is_array($rOrArray))
		{
			$r = $rOrArray['r'];
			$g = $rOrArray['g'];
			$b = $rOrArray['b'];
		}
		else
		{
			$r = $rOrArray;
		}

		$result = array();

		$r /= 255;
		$g /= 255;
		$b /= 255;

		$result['k'] = min(1 - $r, 1 - $g, 1 - $b);
		$result['c'] = (1 - $r - $result['k']) / (1 - $result['k']);
		$result['m'] = (1 - $g - $result['k']) / (1 - $result['k']);
		$result['y'] = (1 - $b - $result['k']) / (1 - $result['k']);

		$result['c'] = round($result['c'] * 100);
		$result['m'] = round($result['m'] * 100);
		$result['y'] = round($result['y'] * 100);
		$result['k'] = round($result['k'] * 100);

		return $result;
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
		$designTypeModel = RModel::getAdminInstance('Designtype', array(), 'com_reddesign');
		$designTypeModel->setState('id', $redDesignData['id']);
		$designType = $designTypeModel->getItem();

		$data = array();
		$data['designType'] = $designType;

		// Get Background Data
		$data['designBackground'] = $designTypeModel->getProductionBackground($redDesignData['id']);

		// Get designAreas
		$data['designAreas'] = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('reddesign_area_id'));
		$query->from($db->quoteName('#__reddesign_areas'));
		$query->where($db->quoteName('background_id') . ' = ' . $redDesignData->production_background_id);
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

			$key = 'textArea_' . $areaId;
			$area['textArea'] = $redDesignData->$key;

			$data['designAreas'][] = $area;
		}

		$data['autoSizeData'] = json_decode($redDesignData->autoSizeData);

		return $data;
	}
}
