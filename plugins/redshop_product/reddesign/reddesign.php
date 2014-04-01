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

		$this->loadLanguage('com_reddesign', JPATH_SITE);
		$this->loadLanguage();

		// Register component prefix
		JLoader::registerPrefix('Reddesign', JPATH_ADMINISTRATOR . '/components/com_reddesign');

		// Register library prefix.
		JLoader::registerPrefix('Reddesign', JPATH_LIBRARIES . '/reddesign');

		JLoader::import('redcore.bootstrap');

		JFactory::getApplication()->input->set('redcore', true);

		if ($this->params->get('loadBootstrapRedcore', 1) == 1)
		{
			// Load bootstrap + fontawesome
			JHtml::_('rbootstrap.framework');

			RHelperAsset::load('component.js', 'redcore');
			RHelperAsset::load('component.min.css', 'redcore');
		}

		// Load the language files
		$jlang = JFactory::getLanguage();
		$jlang->load('plg_koparent_paymentgateway_paypal', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_koparent_paymentgateway_paypal', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_koparent_paymentgateway_paypal', JPATH_ADMINISTRATOR, null, true);
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
		$input = JFactory::getApplication()->input;
		$view = $input->get('view');

		if ($product->product_type == 'redDESIGN' && $view == 'product')
		{
			RForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_reddesign/models/forms');
			RForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_reddesign/models/fields');

			// Load CSS file
			RHelperAsset::load('site.css', 'com_reddesign');
		}
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
			$displayData = new JObject;
			$displayData->displayedBackground = null;
			$displayData->backgrounds = null;
			$displayData->designType = null;
			$displayData->displayedProductionBackground = null;
			$displayData->displayedAreas = null;
			$displayData->fonts = null;

			$displayedBackgroundId = $app->input->getInt('displayedBackgroundId', null);

			// Get models.
			$backgroundsModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true), 'com_reddesign');
			$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true), 'com_reddesign');
			$designTypeModel = RModel::getAdminInstance('Designtype', array('ignore_request' => true), 'com_reddesign');
			$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');

			$displayData->fonts = $fontsModel->getItems();

			if (!$displayedBackgroundId)
			{
				$displayedAttrPropBackground = $backgroundsModel->getDefaultBackground($data->product_id);
				$displayedBackgroundId = $displayedAttrPropBackground->background_id;
			}

			$displayData->displayedBackground = $backgroundModel->getItem($displayedBackgroundId);

			$xml = simplexml_load_file(JURI::root() . 'media/com_reddesign/backgrounds/' . $displayData->displayedBackground->svg_file);
			$xmlInfo = $xml->attributes();
			$displayData->displayedBackground->width  = str_replace('px', '', $xmlInfo->width);
			$displayData->displayedBackground->height = str_replace('px', '', $xmlInfo->height);

			$displayedDesignTypeId = $displayData->displayedBackground->designtype_id;

			// Get list of other backgrounds.
			$attributePropertiesBackgounds = $backgroundsModel->getBackgroundsFromAttributes($data->product_id);
			$backgrounds = array();

			foreach ($attributePropertiesBackgounds as $attrPropertyBackground)
			{
				$backgrounds[] = $backgroundModel->getItem($attrPropertyBackground->background_id);
			}

			$displayData->backgrounds = $backgrounds;
			$displayData->designType = $designTypeModel->getItem($displayedDesignTypeId);
			$displayData->displayedProductionBackground = $designTypeModel->getProductionBackground($displayedDesignTypeId);

			$areasModel->setState('filter.background_id', $displayedBackgroundId);
			$displayData->displayedAreas = $areasModel->getItems();

			$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayData->displayedAreas);
			$displayData->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);

			$displayData->product = $data;

			$html = RLayoutHelper::render('default', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl');

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

			if (!empty($displayData->displayedAreas))
			{
				foreach ($displayData->displayedAreas as $area)
				{
					$areasLoopTemplateInstance = $areasLoopTemplate;

					// Get area specific content.
					$areaHtml = $this->setContentOnTemplateTag('{RedDesignBreakDesignArea' . $area->id . '}', $htmlAreas);

					$areasLoopTemplateInstance = ReddesignHelpersArea::parseAreaTemplateTags($areaHtml, $areasLoopTemplateInstance);

					$areasFinshedOutput .= $areasLoopTemplateInstance;
				}
			}

			$start = '{redDESIGN:AreasLoopStart}';
			$end = '{redDESIGN:AreasLoopEnd}';
			$template_desc = preg_replace(
				'#(' . $start . ')(.*)(' . $end . ')#si',
				'<div id="areasContainer">' . $areasFinshedOutput . '</div>', $template_desc
			);

			// Get form end.
			$htmlElement = $this->setContentOnTemplateTag('{RedDesignBreakFormEndsAndJS}', $html);
			$template_desc = str_replace("{redDESIGN:FormEnd}", $htmlElement, $template_desc);

			$redDesignData = "<input type='hidden' name='task' value='add'><input type='hidden' id='redDesignData' name='redDesignData' value='' />";

			$template_desc = str_replace("<input type='hidden' name='task' value='add'>", $redDesignData, $template_desc);

			$template_desc = '<div class="redcore">' . $template_desc . '</div>';
		}
	}

	/**
	 * Method for easier management of splitting template tags
	 *
	 * @param   string  $templateTag    Template tag on which we split html string
	 * @param   string  $html           Full html string
	 * @param   int     $arrayPosition  Position pointer in array after splitting html string
	 *
	 * @return  string
	 */
	public function setContentOnTemplateTag($templateTag, $html, $arrayPosition = 1)
	{
		$arrayHtml = explode($templateTag, $html);

		if (!empty($arrayHtml[$arrayPosition]))
		{
			return $arrayHtml[$arrayPosition];
		}

		return '';
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
		$redDesign = false;

		if (empty($product->product_type))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('product_type'));
			$query->from($db->quoteName('#__redshop_product'));
			$query->where($db->quoteName('product_id') . ' = ' . $product->product_id);
			$db->setQuery($query);
			$productType = $db->loadResult();

			if ($productType == 'redDESIGN')
			{
				$redDesign = true;
			}
		}
		else
		{
			if ($product->product_type == 'redDESIGN')
			{
				$redDesign = true;
			}
		}

		if ($redDesign)
		{
			RHelperAsset::load('snap.svg-min.js', 'com_reddesign');

			$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true), 'com_reddesign');
			$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
			$designTypeModel = RModel::getAdminInstance('Designtype', array('ignore_request' => true), 'com_reddesign');

			$db = JFactory::getDbo();
			$document = JFactory::getDocument();
			$config = ReddesignEntityConfig::getInstance();
			$fontUnit = $config->getFontUnit();

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

			// If background_id is null, get the Production Background of Design Type
			if (empty($redDesignData->background_id))
			{
				$displayedBackground = $designTypeModel->getProductionBackground($redDesignData->designtype_id);
			}
			else
			{
				$displayedBackground = $backgroundModel->getItem($redDesignData->background_id);
			}

			$defaultPreviewWidth = $this->params->get('defaultCartPreviewWidth', 600);
			$defaultPreviewHeight = $this->params->get('defaultCartPreviewHeight', 450);

			if ($redDesignData->previewWidth > $redDesignData->previewHeight)
			{
				$previewWidth = $defaultPreviewWidth;
				$scalingImageForPreviewRatio = $defaultPreviewWidth / $redDesignData->previewWidth;
				$previewHeight = $redDesignData->previewHeight * $scalingImageForPreviewRatio;
			}
			else
			{
				$previewHeight = $defaultPreviewHeight;
				$scalingImageForPreviewRatio = $defaultPreviewHeight / $redDesignData->previewHeight;
				$previewWidth = $redDesignData->previewWidth * $scalingImageForPreviewRatio;
			}

			$areasModel->setState('filter.background_id', $displayedBackground->id);
			$displayedAreas = $areasModel->getItems();

			$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayedAreas);
			$selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);

			$oldProductImageLayout = $product_image;
			$product_image = '<div id="product_image_' . $i . '" >' .
								'<div class="pull-left">' .
									$oldProductImageLayout .
									'<a id="modalPreviewTrigger' . $i . '" class="btn btn-small modal-preview" href="#modalPreview' . $i . '">' .
										JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_PREVIEW') .
									'</a>' .
								'</div>' .
								'<div class="progressbar-holder progressbar-table">' .
									'<div class="progress progress-striped" style="display:none;">' .
										'<div class="bar bar-success"></div>' .
									'</div>' .
								'</div>' .
								'<div style="height: 100%;width: 100%;left: -2000px;position: absolute;">' .
									'<div id="modalPreview' . $i . '">' .
										'<svg id="previewSvg' . $i . '" width="' . $previewWidth . 'px" height="' . $previewHeight . 'px"></svg>' .
									'</div>' .
								'</div>' .
							'</div>';

			if (!empty($redDesignData->areasInnerSVG))
			{
				/*
					When we have some heavy cliparts added the limit is reached and then we get 500 error
					"PHP regular expression limit reached (pcre.backtrack_limit)"
					That's why limit has to be increased. This can make problems on servers.
				*/
				ini_set('pcre.backtrack_limit', 10000000);
				ini_set('pcre.recursion_limit', 10000000);

				$areasInnerSVG = str_replace("'", "\'", urldecode($redDesignData->areasInnerSVG));
				$areasInnerSVG = str_replace("\n", "", urldecode($areasInnerSVG));
				$useCheckerboard = ($displayedBackground->useCheckerboard) ? 'true' : 'false';

				$js = ' var backgroundPreviews' . $i . ' = [];
						backgroundPreviews' . $i . '["svgUrl"] = "' . JURI::root() . 'media/com_reddesign/backgrounds/' . $displayedBackground->svg_file . '";
						backgroundPreviews' . $i . '["useCheckerboard"] = "' . $useCheckerboard . '";

						jQuery(document).ready(function () {
							jQuery(document).on("mousedown", "#modalPreviewTrigger' . $i . '", function(e) {
								if (typeof backgroundPreviews' . $i . '["progress"] === "undefined")
								{
									loadPreviewBackground(this);
								}
								else if (backgroundPreviews' . $i . '["progress"] == false)
								{
									return false;
								}
								else
								{
									SqueezeBox.fromElement(this);
								}

								return false;
							});

							function loadPreviewBackground(modalButton)
						{
							if (typeof backgroundPreviews' . $i . ' === "undefined")
							{
								return false;
							}

							jQuery.ajax({
								url: backgroundPreviews' . $i . '["svgUrl"],
								dataType: "text",
								cache: true,
								xhrFields: {
									onprogress: function (e) {
										if (e.lengthComputable) {
											var loadedPercentage = parseInt(e.loaded / e.total * 100);
											jQuery("#product_image_' . $i . '" + " .progress .bar-success")
												.css("width", "" + (loadedPercentage) + "%")
												.html(loadedPercentage + "% ' . JText::_('COM_REDDESIGN_COMMON_PROGRESS_LOADED', true) . '");
										}
									}
								},
								beforeSend: function (xhr) {
									backgroundPreviews' . $i . '["progress"] = false;
									jQuery("#product_image_' . $i . '" + " .progress").show().addClass("active");
									jQuery("#product_image_' . $i . '" + " .progress .bar-success").css("width", "0%");
								},
								success: function (response) {
									jQuery("#product_image_' . $i . '" + " .progress").removeClass("active");
									backgroundPreviews' . $i . '["progress"] = true;

									if(typeof response === "undefined" || response == false)
									{
										jQuery("#product_image_' . $i . '" + " .progress")
										.append(\'<div class="bar bar-danger" style="width: \' + (100 - parseInt(jQuery(\'#product_image_' . $i . '\' + \' .progress .bar-success\').css(\'width\'))) + \'%;"></div>\');
									}
									else
									{
										jQuery("#product_image_' . $i . '" + " .progressbar-holder .progress .bar-success").css("width", "100%");
									}

									if (backgroundPreviews' . $i . '["useCheckerboard"])
									{
										jQuery("#previewSvg' . $i . '").parent().addClass("checkerboard");
									}

									rootSnapSvgObject' . $i . ' = Snap("#previewSvg' . $i . '");

									jQuery("#previewSvg' . $i . '")
										.append(\'<defs><style type="text/css">' . $selectedFontsDeclaration . '</style></defs>\')
										.append(response);

								var loadedSvgFromFile = jQuery("#previewSvg' . $i . '").find("svg")[0];
								loadedSvgFromFile.setAttribute("width", parseFloat("' . $previewWidth . '"));
								loadedSvgFromFile.setAttribute("height", parseFloat("' . $previewHeight . '"));
								loadedSvgFromFile.setAttribute("id", "previewSvgCanvas' . $i . '");

								var rootElement = document.getElementById("previewSvg' . $i . '");
								rootElement.setAttribute("width", parseFloat("' . $previewWidth . '"));
								rootElement.setAttribute("height", parseFloat("' . $previewHeight . '"));
								rootElement.setAttribute("overflow", "hidden");

								var group_' . $i . ' = Snap.parse(\'<g id="areaBoxesLayer' . $i . '">' . $areasInnerSVG . '</g>\');
								rootSnapSvgObject' . $i . '.add(group_' . $i . ');

								jQuery("#areaBoxesLayer' . $i . ' text").each(function (index, value){
									var xPos = parseFloat(jQuery(value).attr("x"));
									xPos = xPos * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("x", xPos);

									var yPos = parseFloat(jQuery(value).attr("y"));
									yPos = yPos * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("y", yPos);

									var fontSize = parseFloat(jQuery(value).css("font-size"));
									fontSize = fontSize * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).css("font-size", fontSize + "' . $fontUnit . '");

									var transformMatrix = jQuery("#" + this.id).attr("transform");

									if (typeof transformMatrix !== "undefined")
									{
										transformMatrix = transformMatrix.replace("matrix(", "");
										transformMatrix = transformMatrix.replace(")", "");

										var vectorCoordinates = transformMatrix.split(",");

										var xTransl = parseFloat(vectorCoordinates[4]) * parseFloat("' . $scalingImageForPreviewRatio . '");
										var yTransl = parseFloat(vectorCoordinates[5]) * parseFloat("' . $scalingImageForPreviewRatio . '");

										var newMatrix = "matrix(" +
														vectorCoordinates[0] + "," +
														vectorCoordinates[1] + "," +
														vectorCoordinates[2] + "," +
														vectorCoordinates[3] + "," +
														xTransl + "," +
														yTransl + ")";
										jQuery("#" + this.id).attr("transform", newMatrix);
									}
								});

								jQuery("#areaBoxesLayer' . $i . ' tspan").each(function (index, value){
									if (index > 0)
									{
										var xPos = parseFloat(jQuery(value).attr("x"));
										xPos = xPos * parseFloat("' . $scalingImageForPreviewRatio . '");
										jQuery(value).attr("x", xPos);
									}
								});

								jQuery("#areaBoxesLayer' . $i . ' svg").each(function (index, value){
									var xPos = parseFloat(jQuery(value).attr("x"));
									xPos = xPos * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("x", xPos);

									var yPos = parseFloat(jQuery(value).attr("y"));
									yPos = yPos * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("y", yPos);

									var width = parseFloat(jQuery(value).attr("width"));
									width = width * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("width", width);

									var height = parseFloat(jQuery(value).attr("height"));
									height = height * parseFloat("' . $scalingImageForPreviewRatio . '");
									jQuery(value).attr("height", height);
								});

									SqueezeBox.fromElement(modalButton);
								}
							});
						}
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

			// Load override redSHOP attribute.js
			$headerstuff = $document->getHeadData();
			$scripts = $headerstuff['scripts'];

			foreach ($scripts as $path => $val)
			{
				if (strpos($path, 'com_redshop/assets/js/attribute.js') !== false)
				{
					unset($scripts[$path]);
				}
			}

			$headerstuff['scripts'] = $scripts;
			$document->setHeadData($headerstuff);

			$document->addScript('media/com_reddesign/js/attribute.js');

			$js = 'function generateRedDesignData() {
						var values = {};
						var inputs = jQuery("#designform :input");

						inputs.each(function() {
							values[this.name] = jQuery(this).val();
						});

						values["designtype_id"] = jQuery("#designtype_id").val();
						values["previewWidth"] = rootSnapSvgObject.attr("width");
						values["previewHeight"] = rootSnapSvgObject.attr("height");

						var areas = rootSnapSvgObject.select("#areaBoxesLayer");
						values["areasInnerSVG"] = encodeURIComponent(areas.innerSVG());

						var jsonString = JSON.stringify(values);

						jQuery("#redDesignData").val(jsonString);
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
		$query->select($db->quoteName(array('order_item_id', 'redDesignData', 'productionSvg')));
		$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
		$query->where($db->quoteName('order_item_id') . ' = ' . $orderItem->order_item_id);
		$db->setQuery($query);
		$orderItemMapping = $db->loadObject();

		$redDesignData = json_decode($orderItemMapping->redDesignData);
		$redDesignDataPrepared = $this->prepareDesignTypeData($redDesignData);

		// Echo information.
		if (!empty($orderItemMapping))
		{
			$html = '<div id="customDesignData' . $orderItem->order_item_id . '" style="margin: 15px 0 15px 0;">' .
						'<span><strong>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_DETAILS') . '</strong></span><br/>' .
						'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_TYPE') . $redDesignDataPrepared['designType']->name . '</div>' .
					'</div>';

			foreach ($redDesignDataPrepared['designAreasCustomData'] as $customArea)
			{
				if (strpos($customArea->colorCode, '#') !== false)
				{
					$cmyk = '';

					if (!empty($customArea->colorCode))
					{
						$cmykCode = $this->rgb2cmyk($this->hex2rgb($customArea->colorCode));
						$cmyk = 'C: ' . $cmykCode['c'] . ' ' . 'M: ' . $cmykCode['m'] . ' ' . 'Y: ' . $cmykCode['y'] . ' ' . 'K: ' . $cmykCode['k'];
					}

					$fontColor = '<span style="margin-right:5px; background-color: ' . $customArea->colorCode . ';" >' .
									'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
								'</span>' .
								'<span>' . $cmyk . '</span>';
				}
				else
				{
					$cmyk = '';

					if (!empty($customArea->colorCode))
					{
						$cmykCode = $this->rgb2cmyk($this->hex2rgb('#' . $customArea->colorCode));
						$cmyk = 'C: ' . $cmykCode['c'] . ' ' . 'M: ' . $cmykCode['m'] . ' ' . 'Y: ' . $cmykCode['y'] . ' ' . 'K: ' . $cmykCode['k'];
					}

					$fontColor  = '<span style="margin-right:5px; background-color:#' . $customArea->colorCode . ';" >' .
									'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' .
								'<span>' . $cmyk . '</span>';
				}

				$html .= '<br/>' .
						'<div id="area' . $customArea->id . '">' .
							'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_AREA') . '</div>' .
							'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_TEXT') . $customArea->text . '</div>' .
							'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_FONT_NAME') . $customArea->fontName . '</div>' .
							'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_FONT_SIZE') . $customArea->fontSize . '</div>' .
							'<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CUSTOMIZED_DESIGN_TEXT_COLOR') . $fontColor . '</div>' .
						'</div>';
			}

			// Create files
			if (empty($orderItemMapping->productionSvg))
			{
				$doc = new DomDocument;
				$doc->validateOnParse = true;
				$doc->load(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $redDesignDataPrepared['designBackground']->svg_file);

				$svgElements = $doc->getElementsByTagName('svg');
				$svg = $svgElements->item(0);

				// Add font definitions.
				$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($redDesignDataPrepared['designAreasDefintions']);
				$selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);
				$fragment = $doc->createDocumentFragment();
				$fragment->appendXML('<defs><style type="text/css">' . $selectedFontsDeclaration . '</style></defs>');
				$svg->appendChild($fragment);

				$width = str_replace('px', '', $svg->getAttribute('width'));
				$scalingRatio = $width / $redDesignData->previewWidth;

				$fragment = $doc->createDocumentFragment();
				$fragment->appendXML('<g id="customizedAreas">' . urldecode($redDesignData->areasInnerSVG) . '</g>');

				$textNodes = $fragment->childNodes->item(0)->getElementsByTagName('text');

				for ($i = 0; $i < $textNodes->length; $i++)
				{
					$x = $textNodes->item($i)->getAttribute('x');
					$y = $textNodes->item($i)->getAttribute('y');
					$fontSizeStyle = $textNodes->item($i)->getAttribute('style');

					$matches = array();
					preg_match('/font-size: (.*?)px;/s', $fontSizeStyle, $matches);

					$x *= $scalingRatio;
					$y *= $scalingRatio;
					$textNodes->item($i)->setAttribute('x', $x);
					$textNodes->item($i)->setAttribute('y', $y);

					if (!empty($matches[1]))
					{
						$fontSize = $matches[1];
						$newfontSize = (float) $fontSize * $scalingRatio;
						$fontSizeStyle = str_replace($fontSize, $newfontSize, $fontSizeStyle);
						$textNodes->item($i)->setAttribute('style', $fontSizeStyle);
					}
					else
					{
						$fontSize = str_replace('px', '', $textNodes->item($i)->getAttribute('font-size'));
						$newfontSize = (float) $fontSize * $scalingRatio;
						$textNodes->item($i)->setAttribute('font-size', $newfontSize);
					}

					// Fix transform matrix
					$transformMatrix = $textNodes->item($i)->getAttribute('transform');

					if (!empty($transformMatrix))
					{
						$transformMatrix = str_replace('matrix(', '', $transformMatrix);
						$transformMatrix = str_replace(')', '', $transformMatrix);

						$vectorCoordinates = explode(',', $transformMatrix);
						$xTransl = (float) $vectorCoordinates[4] * $scalingRatio;
						$yTransl = (float) $vectorCoordinates[5] * $scalingRatio;

						$newMatrix = 'matrix(' .
							$vectorCoordinates[0] . ',' .
							$vectorCoordinates[1] . ',' .
							$vectorCoordinates[2] . ',' .
							$vectorCoordinates[3] . ',' .
							$xTransl . ',' .
							$yTransl .
						')';

						$textNodes->item($i)->setAttribute('transform', $newMatrix);
					}
				}

				$textNodes = $fragment->childNodes->item(0)->getElementsByTagName('tspan');

				for ($i = 1; $i < $textNodes->length; $i++)
				{
					$x = $textNodes->item($i)->getAttribute('x');
					$x *= $scalingRatio;
					$textNodes->item($i)->setAttribute('x', $x);
				}

				$clipartNodes = $fragment->childNodes->item(0)->getElementsByTagName('svg');

				for ($i = 0; $i < $clipartNodes->length; $i++)
				{
					$x      = $clipartNodes->item($i)->getAttribute('x');
					$y      = $clipartNodes->item($i)->getAttribute('y');
					$width  = $clipartNodes->item($i)->getAttribute('width');
					$height = $clipartNodes->item($i)->getAttribute('height');

					$x      *= $scalingRatio;
					$y      *= $scalingRatio;
					$width  *= $scalingRatio;
					$height *= $scalingRatio;
					$clipartNodes->item($i)->setAttribute('x', $x);
					$clipartNodes->item($i)->setAttribute('y', $y);
					$clipartNodes->item($i)->setAttribute('width', $width);
					$clipartNodes->item($i)->setAttribute('height', $height);
				}

				$svg->appendChild($fragment);

				$uniqueFileName = 'production-file-' . $orderItem->order_id . '-' . $orderItem->order_item_id;

				if ($doc->save(JPATH_SITE . '/media/com_reddesign/backgrounds/orders' . $uniqueFileName . '.svg'))
				{
					$html .= '<div>' .
								'<a href="' . JURI::root() . 'media/com_reddesign/backgrounds/orders' . $uniqueFileName . '.svg" target="_blank">SVG ' .
									JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_DOWNLOAD') .
								'</a>' .
							'</div>';

					$orderItemMapping->productionSvg = $uniqueFileName;
					$db->updateObject('#__reddesign_orderitem_mapping', $orderItemMapping, 'order_item_id');
				}
				else
				{
					$html .= '<div>' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_CAN_NOT_CREATE_PRODUCTION_FILES') . '</div>';
				}
			}
			else
			{
				$html .= '</br>' .
						'<div>' .
							'<a href="' . JURI::root() . 'media/com_reddesign/backgrounds/orders' . $orderItemMapping->productionSvg . '.svg" target="_blank">' .
								'SVG ' . JText::_('PLG_REDSHOP_PRODUCT_REDDESIGN_DOWNLOAD') .
							'</a>' .
						'</div>';
			}

			echo $html;
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
		$data = array();
		$data['designAreasDefintions'] = array();
		$data['designAreasCustomData'] = array();

		$designTypeModel = RModel::getAdminInstance('Designtype', array(), 'com_reddesign');
		$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
		$fontModel = RModel::getAdminInstance('Font', array('ignore_request' => true), 'com_reddesign');

		$designType = $designTypeModel->getItem($redDesignData->designtype_id);
		$data['designType'] = $designType;

		$data['designBackground'] = $designTypeModel->getProductionBackground($redDesignData->designtype_id);

		$areasModel->setState('filter.background_id', $data['designBackground']->id);
		$data['designAreasDefintions'] = $areasModel->getItems();

		$data['areasInnerSVG'] = $redDesignData->areasInnerSVG;

		foreach ($data['designAreasDefintions'] as $designArea)
		{
			$area = new JObject;

			$area->id = $designArea->id;

			$key = 'textArea_' . $area->id;

			if (!empty($redDesignData->$key))
			{
				$area->text = $redDesignData->$key;
			}
			else
			{
				$area->text = '';
			}

			$key = 'fontArea' . $area->id;

			if (!empty($redDesignData->$key))
			{
				$area->fontName = $fontModel->getItem($redDesignData->$key)->name;
			}
			else
			{
				$area->fontName = '';
			}

			$key = 'fontSize' . $area->id;

			if (!empty($redDesignData->$key))
			{
				$area->fontSize = $redDesignData->$key;
			}
			else
			{
				$area->fontSize = '';
			}

			$key = 'colorCode' . $area->id;

			if (!empty($redDesignData->$key))
			{
				$area->colorCode = $redDesignData->$key;
			}
			else
			{
				$area->colorCode = '';
			}

			$data['designAreasCustomData'][] = $area;
		}

		return $data;
	}
}
