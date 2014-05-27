<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHtml::_('rbootstrap.modal');

RHelperAsset::load('snap.svg-min.js', 'com_reddesign');


if (isset($displayData))
{
	$this->displayedBackground = $displayData->displayedBackground;
	$this->backgrounds = $displayData->backgrounds;
	$this->designType = $displayData->designType;
	$this->displayedProductionBackground = $displayData->displayedProductionBackground;
	$this->displayedAreas = $displayData->displayedAreas;
	$this->selectedFontsDeclaration = $displayData->selectedFontsDeclaration;
}

$imageUrl = JURI::base() . 'media/com_reddesign/backgrounds/' . $this->displayedBackground->svg_file;
$config = ReddesignEntityConfig::getInstance();
$preserveData = $config->getPreserveDataBetweenDesignTypes();
$previewWidth = $config->getMaxSVGPreviewSiteWidth();
$clipartPreviewWidth = $config->getMaxClipartPreviewWidth();
$clipartPreviewHeight = $config->getMaxClipartPreviewHeight();
$unit = $config->getUnit();
$fontUnit = $config->getFontUnit();
$sourceDpi = $config->getSourceDpi();

$unitConversionRatio = ReddesignHelpersSvg::getUnitConversionRatio($unit, $sourceDpi);

/*
{RedDesignBreakELEMENT} is a tag used in integration plugin to explode HTML string into smaller peaces. Those peaces are used in redSHOP templating.
*/

?>

<?php // Part 0 - Title ?>
{RedDesignBreakTitle}
<h1><?php echo $this->designType->name; ?></h1>
{RedDesignBreakTitle}

<?php // Part 1 - Begin Form ?>
{RedDesignBreakFormBegin}
<form id="designform" name="designform" method="post" action="index.php" class="row-fluid reddesign-form">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" id="task" value="">
	<input type="hidden" name="designAreas" id="designAreas" value="">
	<input type="hidden" id="autoSizeData" name="autoSizeData" value="" />
	<input type="hidden" id="designtype_id" name="designtype_id" value="<?php echo $this->designType->id; ?>">
{RedDesignBreakFormBegin}

	<?php // Part 2 - Select Backgrounds ?>
{RedDesignBreakBackgrounds}
	<div class="row-fluid">
		<div class="well span12 col-md12">
			<?php
				echo RLayoutHelper::render('default_backgrounds', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl');
			?>
		</div>
	</div>
{RedDesignBreakBackgrounds}

	<?php // Part 3 - Design Image ?>
{RedDesignBreakDesignImage}
	<div id="background-container">

		<div id="svgContainer">
			<div>
				<svg id="mainSvgImage"></svg>
			</div>
			<div class="progressbar-holder" style="width: <?php echo $previewWidth; ?>px; margin-top:20px;">
				<div class="progress progress-striped" style="display:none;">
					<div class="bar bar-success"></div>
				</div>
			</div>
		</div>

		<button id="fullScreenButton"
		        type="button"
		        class="btn btn-success full-screen-popup"
				href="#fullScreenContainer">
			<i class="icon-fullscreen"></i>
			<span>
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_ENLARGE'); ?>
			</span>
		</button>
		<div id="fullScreenContainer" class="redcore" style="padding:0;">
			<div class="row-fluid">
				<div class="span10 col-md10 svg-container-element" id="fullScreenContainerSVG">
				</div>
				<div class="span2 col-md2 right-modal-buttons">
					<button id="fullScreenCloseButton"
					        type="button"
					        class="btn btn-danger btn-large full-screen-close"
						onclick="SqueezeBox.close()">
						<i class="icon-remove"></i>
						<span>
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLOSE'); ?>
						</span>
					</button>
					<br /><br />
					<button id="fullScreenPrintButton"
					        type="button"
					        class="btn btn-info btn-large full-screen-print"
						onclick="printSVGPreview()">
						<i class="icon-print"></i>
						<span>
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PRINT'); ?>
						</span>
					</button>
					<br /><br />
					<div>
						<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_ENLARGE_POPUP_TITLE'); ?></h3>
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_ENLARGE_POPUP_DESC'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
{RedDesignBreakDesignImage}

	<?php // Part 6 - Areas Begin ?>
{RedDesignBreakDesignAreas}
	<div class="row-fluid">
		<div class="well span12 col-md12">
			<?php echo RLayoutHelper::render('default_areas', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl'); ?>
		</div>
	</div>
{RedDesignBreakDesignAreas}

	<?php // Part 7 - Form Ends ?>
{RedDesignBreakFormEndsAndJS}
</form>

<script type="text/javascript">
	var rootSnapSvgObject;

	/**
	 * Initiate PX to Unit conversation variables
	 */
	var unit = "<?php echo $unit;?>";
	var fontUnit = "<?php echo $fontUnit;?>";
	var imageWidth  = parseFloat("<?php echo (!empty($displayData->displayedBackground->width) ? $displayData->displayedBackground->width : ''); ?>");
	var imageHeight = parseFloat("<?php echo (!empty($displayData->displayedBackground->height) ? $displayData->displayedBackground->height : ''); ?>");
	var previewWidth  = parseFloat("<?php echo $previewWidth; ?>");

	var unitConversionRatio = parseFloat("<?php echo $unitConversionRatio;?>");
	var scalingImageForPreviewRatio = previewWidth / imageWidth;
	var previewHeight = imageHeight * scalingImageForPreviewRatio;
	var areasContainer = [];

	function thumbnailSVGLoader()
	{
		jQuery('.thumbnailSVG').each(function () {

			var svgThumbnail = document.getElementById(jQuery(this).attr('id'));
			svgThumbnail.addEventListener("load", function() {
				setSVGElementScale(this);
			});

			// Some elements are already loaded
			setSVGElementScale(svgThumbnail);
		});
	}
	thumbnailSVGLoader();

	function setSVGElementScale(svgDocument)
	{
		if (svgDocument && typeof(svgDocument) != "undefined")
		{
			var svgDocumentContent = svgDocument.contentDocument;
			if (svgDocumentContent && typeof(svgDocumentContent) != "undefined")
			{
				var svgElementInner = jQuery(svgDocumentContent.documentElement);
				if (typeof(svgElementInner) != "undefined")
				{
					jQuery(svgElementInner).attr("height", "<?php echo $clipartPreviewHeight; ?>");
					jQuery(svgElementInner).attr("width", "<?php echo $clipartPreviewWidth; ?>");
					jQuery(svgElementInner).attr("preserveAspectRatio", "xMidYMid meet");
				}
			}
		}
	}

	function printSVGPreview()
	{
		var $container = jQuery("#fullScreenContainerSVG2");

		var hiddenIFrame = jQuery('<iframe></iframe>').attr({
			width: '1px',
			height: '1px',
			display: 'none'
		}).appendTo($container.parent());

		var myIframe = hiddenIFrame.get(0);
		myIframe.contentWindow.document.body.innerHTML = $container.parent().html();
		myIframe.contentWindow.print();
		hiddenIFrame.remove();
	}

	/**
	 * Add click event to Customize button.
	 */
	jQuery(document).ready(function () {
			addUploadButtonCall();
			rootSnapSvgObject = Snap("#mainSvgImage");

			jQuery(".color-wheel").hide();
			jQuery(".cmyk-inputs").hide();

			jQuery(".colorPickerSelectedColor").click(function(){
				jQuery(".color-wheel").hide();
				jQuery(".cmyk-inputs").hide();

				if(jQuery(this).parent().parent().parent().parent().parent().parent().parent().hasClass("wheel-clicked")) {
					jQuery(this).parent().parent().parent().parent().find(".color-wheel").hide();
					jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").hide();
					jQuery(this).parent().parent().parent().parent().parent().parent().parent().removeClass("wheel-clicked")
				}
				else {
					jQuery("#areasContainer li").removeClass("wheel-clicked")
					jQuery(this).parent().parent().parent().parent().find(".color-wheel").show();
					jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").show();
					jQuery(this).parent().parent().parent().parent().parent().parent().parent().addClass("wheel-clicked");
				}
			});

			<?php if (!empty($this->displayedBackground->svg_file)) : ?>
				jQuery.ajax({
					url: "<?php echo $imageUrl; ?>",
					dataType: "text",
					cache: true,
					xhrFields: {
						onprogress: function (e) {
							if (e.lengthComputable) {
								var loadedPercentage = parseInt(e.loaded / e.total * 100);
								jQuery('#svgContainer .progress .bar-success')
									.css('width', '' + (loadedPercentage) + '%')
									.html(loadedPercentage + '% <?php echo JText::_('COM_REDDESIGN_COMMON_PROGRESS_LOADED', true); ?>');
							}
						}
					},
					beforeSend: function (xhr) {
						jQuery('#svgContainer .progress .bar-success').css('width', '0%');
						jQuery('#svgContainer .progress').show().addClass('active');
					},
					success: function (response) {
						jQuery('#svgContainer .progress').removeClass('active');
						if(typeof response === 'undefined' || response == false){
							jQuery('#svgContainer .progress').append(
								'<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#svgContainer .progress .bar-success').css('width'))) + '%;"></div>'
							);
						}
						else{
							jQuery('#svgContainer .progress').fadeOut(3000);
						}

						if ('<?php echo $this->displayedBackground->useCheckerboard ?>' == '1')
						{
							jQuery('#mainSvgImage').parent().width(previewWidth).height(previewHeight).addClass('checkerboard');
						}

						jQuery("#mainSvgImage")
							.append('<defs><style type="text/css"><?php echo $this->selectedFontsDeclaration; ?></style></defs>')
							.append(response);

						// Set preview size at loaded file.
						var loadedSvgFromFile = jQuery("#mainSvgImage").find("svg")[0];
						loadedSvgFromFile.setAttribute("width", previewWidth.toString() + "px");
						loadedSvgFromFile.setAttribute("height", previewHeight.toString() + "px");
						loadedSvgFromFile.setAttribute("id", "svgCanvas");

						// Set preview size at svg container element.
						var rootElement = document.getElementById("mainSvgImage");
						rootElement.setAttribute("width", previewWidth.toString() + "px");
						rootElement.setAttribute("height", previewHeight.toString() + "px");
						rootElement.setAttribute("overflow", "hidden");

						var groupObject = rootSnapSvgObject.group();
						groupObject.node.id = "areaBoxesLayer";

						customize();
					}
				});
			<?php endif; ?>

			// Correct radio button selection.
			jQuery("#background<?php echo $this->displayedBackground->id; ?>").attr("checked", "checked");

			// Customize function.
			jQuery(document).on("click", "#customizeDesign", function () {
				// Add spinner to button.
				jQuery(this).button("loading");
				setTimeout(function() {
						jQuery(this).button("reset");
					},
					3000
				);
				customize();
			});

			jQuery(document).on("click", "#fullScreenButton", function () {
				var previewSVG = jQuery("#fullScreenContainerSVG")
					.empty()
					.html(jQuery("#mainSvgImage").clone())
					.find('svg:first')[0];

				var boxWidth = jQuery(window).width() - 120;
				var width = (boxWidth * 0.8290598290598291);
				var boxHeight = jQuery(window).height() - 120;
				var height = boxHeight;

				// Calculating scale
				var scale = 1;
				if ((width - parseFloat(previewSVG.getAttribute('width'))) < (height - parseFloat(previewSVG.getAttribute('height'))))
				{
					scale = boxWidth / parseFloat(previewSVG.getAttribute('width'));
					height = parseFloat(previewSVG.getAttribute('height')) * scale;
					boxHeight = height;
				}
				else
				{
					scale = boxHeight / parseFloat(previewSVG.getAttribute('height'));
					var boxWidth20 = boxWidth - width;
					width = parseFloat(previewSVG.getAttribute('width')) * scale;
					boxWidth = boxWidth20 + width;
				}

				// first top SVG
				previewSVG.setAttribute("width", width.toString() + "px");
				previewSVG.setAttribute("height", height.toString() + "px");
				previewSVG.setAttribute("id", "fullScreenContainerSVG2");

				// first inner SVG
				var previewSVGInner = jQuery(previewSVG).find('svg:first')[0];
				previewSVGInner.setAttribute("width", width.toString() + "px");
				previewSVGInner.setAttribute("height", height.toString() + "px");

				jQuery(previewSVG).children("g").each(function () {
					jQuery(this).attr("transform", "scale(" + scale + ")");
				});

				var options = {
					size: {x: boxWidth, y: boxHeight},
					handler: "clone",
					clone: "fullScreenContainer"
				};

				SqueezeBox.fromElement(this, options);
			});

			jQuery(document).on("keyup", ".reddesign-form .textAreaClass", function() {
				var id = jQuery(this).attr("id").replace("textArea_", "");
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-selection", function() {
				var id = jQuery(this).attr("id").replace("fontArea", "");
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-size-selection", function() {
				var id = jQuery(this).attr("id").replace("fontSize", "");
				changeSVGElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textAlign button", function() {
				var id = jQuery(this).attr("name").replace("textAlignButton", "");
				jQuery("#textAlign" + id).val(jQuery(this).attr("value"));
				changeSVGElement(id, 1);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textVerticalAlign button", function() {
				var id = jQuery(this).attr("name").replace("textVerticalAlignButton", "");
				jQuery("#verticalAlign" + id).val(jQuery(this).attr("value"));
				changeSVGElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .load-clipart-bank", function() {
				var id = jQuery(this).attr("id").replace("clipartBankButton", "");
				loadClipartBank(id, "clipartBank", false);
			});

			jQuery(document).on("click", ".reddesign-form .upload-clipart-button", function() {
				var id = jQuery(this).attr("id").replace("clipartUploadButton", "");
				showClipartUpload(id);
			});

			jQuery(document).on("click", ".reddesign-form .upload-clipart-file", function() {
				var id = jQuery(this).attr("id").replace("uploadClipartFileSave", "");
				uploadCustomClipart(id);
			});

			jQuery(document).on("click", ".reddesign-form .featured-cliparts", function() {
				var id = jQuery(this).attr("id").replace("featuredClipartsButton", "");
				loadClipartBank(id, "featuredCliparts", false);
			});

			window.globalVar = 1;
			jQuery(document).on("mousedown", ".reddesign-form .thumbnailSVG-pointer", function() {
				if(jQuery(this).parent().parent().parent().hasClass("clickedArea"))
				{
					if(jQuery(this).hasClass("delete"))
					{
						jQuery(this).removeClass("delete");
					}
					else
					{
						jQuery(this).parent().parent().parent().find('.thumbnailSVG-pointer').removeClass("delete");
						jQuery(this).addClass("delete");
					}
				}
				else
				{
					if(jQuery(this).hasClass("delete"))
					{
						jQuery(this).removeClass("delete");
					}
					else
					{
						jQuery(this).parent().parent().parent().find('.thumbnailSVG-pointer').removeClass("delete");
						jQuery(this).addClass("delete");
						jQuery(this).parent().parent().parent().addClass("clickedArea clickArea_" + window.globalVar);
						window.globalVar = window.globalVar + 1;
					}
				}

				var id = jQuery(this).attr("name").replace("clipart", "");
				var clipartId = jQuery(this).parent().find(".change-selected-clipart").val();

				// Store old value
				jQuery("#selectedClipart" + id).attr("rel", jQuery("#selectedClipart" + id).val());
				jQuery("#selectedClipart" + id).val(clipartId);
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .change-selected-clipart", function() {
				var id = jQuery(this).attr("name").replace("selectedClipart", "");
				changeSVGElement(id);
			});

			if (jQuery(".fontSizeSlider").length > 0)
			{
				jQuery(".fontSizeSlider").slider()
					.on("slide", function(ev){
						var id = jQuery(this).attr("id").replace("fontSizeSlider", "");
						jQuery("#fontSize" + id).val(ev.value);
						changeSVGElement(id);
					});
			}

			jQuery(document).on("keyup, change", ".reddesign-form .colorCode", function() {
				var id = jQuery(this).attr("id").replace("colorCode", "");
				var hex = jQuery("#colorCode" + id).val();
				loadCMYKValues(hex, parseInt(id));
			});
		}
	);

	/**
	 * Load CMYK values into related CMYK area fields.
	 * Gets them from a hexadecimal value.
	 *
	 * @param hex int Hexadecimal value.
	 * @param areaId int Area ID.
	 */
	function loadCMYKValues(hex, areaId) {
		var colorObject = new RGB(hexToRgb(hex).r, hexToRgb(hex).g, hexToRgb(hex).b);
		var cmyk = ColorConverter.toCMYK(colorObject);

		jQuery("#C" + areaId).val(cmyk.c);
		jQuery("#M" + areaId).val(cmyk.m);
		jQuery("#Y" + areaId).val(cmyk.y);
		jQuery("#K" + areaId).val(cmyk.k);

		changeSVGElement(areaId);
	}

	/**
	 * Converts hexadecimal value to RGB value.
	 *
	 * @param hex string Hexadecimal value.
	 *
	 * @return object with r,g and b values.
	 */
	function hexToRgb(hex) {
		// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : null;
	}

	/**
	 * Gets hexadecimal value of the color generated by entering values in CMYK fields.
	 *
	 * @param areaId int Area ID
	 *
	 * @return string hexadecimal value
	 */
	function getNewHexColor(areaId)
	{
		var c = jQuery("#C" + areaId).val();
		var m = jQuery("#M" + areaId).val();
		var y = jQuery("#Y" + areaId).val();
		var k = jQuery("#K" + areaId).val();

		var colorObject = new CMYK(c, m, y, k);
		var rgb = ColorConverter.toRGB(colorObject);

		return rgbToHex(rgb.r, rgb.g, rgb.b);
	}

	/**
	 * Changes attributes of element on SVG object
	 *
	 * @param   areaId             int  Area ID
	 * @param   isAlignmentButton  int  Tells if caller of the function is alignment button.
	 */
	function changeSVGElement(areaId, isAlignmentButton)
	{
		if(typeof(isAlignmentButton) === 'undefined')
		{
			isAlignmentButton = 0;
		}

		var area = jQuery("#areaBoxesLayer #areaSVGElement_" + areaId);

		if (jQuery(area).is("svg"))
		{
			changeSVGClipartElement(areaId, isAlignmentButton);
		}
		else
		{
			changeSVGTextElement(areaId);
		}
	}

	/**
	 * Changes attributes of clipart element on SVG object
	 *
	 * @param   areaId             int  Area ID
	 * @param   isAlignmentButton  int  Tells if caller of the function is alignment button.
	 */
	function changeSVGClipartElement(areaId, isAlignmentButton)
	{
		var selectedClipart = jQuery("#selectedClipart" + areaId);
		var svgElement = rootSnapSvgObject.select("#areaSVGElement_" + areaId);
		var insertedElement;
		var oldSvgElementValue = jQuery("#selectedClipart" + areaId).attr("rel");

		if (svgElement)
		{
			var addedSvgImage = rootSnapSvgObject.select("#addedSvgImage" + areaId);

			if ((addedSvgImage) && (selectedClipart.val() == oldSvgElementValue) && isAlignmentButton == 0)
			{
				addedSvgImage.remove();
				jQuery("#selectedClipart" + areaId).attr("rel", "0");
			}
			else
			{
				var horizontalPosition = "";
				var verticalPosition = "YMid";
				var textAlignElemVal = jQuery("#textAlign" + areaId).val();

				if (typeof(textAlignElemVal) !== "undefined")
				{
					horizontalPosition = textAlignElemVal.replace("left", "xMin").replace("center", "xMid").replace("right", "xMax");
				}

				if (horizontalPosition == "")
				{
					horizontalPosition = "xMin";
				}

				if (selectedClipart && typeof(jQuery(selectedClipart).val()) != "undefined")
				{
					var selectedClipartSVG = jQuery("[name=clipart" + areaId + "_" + jQuery(selectedClipart).val() + "]:first");

					if (selectedClipartSVG && typeof(selectedClipartSVG) != "undefined")
					{
						var clipartType = jQuery(selectedClipartSVG).attr("cliparttype");

						if (typeof(clipartType) != "undefined" && clipartType != "svg+xml")
						{
							var imageSVG = '<image ' +
												'id="addedSvgImage' + areaId + '" ' +
												'width="' + areasContainer[areaId]['width'] + '" ' +
												'height="' + areasContainer[areaId]['height'] + '" ' +
												'x="0" y="0" ' +
												'xlink:href="' + jQuery(selectedClipartSVG).attr('data') +
												'"></image>';

							svgElement.clear();

							svgElement.append(Snap.parse(imageSVG));
							insertedElement = svgElement.select("image");
							insertedElement.click(removeSnapElement);
							insertedElement.mouseover(mouseIsIn);
							insertedElement.mouseout(mouseIsOut);
						}
						else
						{
							var svgDocument = document.getElementById(jQuery(selectedClipartSVG).attr("id"));

							if (svgDocument && typeof(svgDocument) != "undefined")
							{
								var svgDocumentContent = svgDocument.contentDocument;

								if (svgDocumentContent && typeof(svgDocumentContent) != "undefined")
								{
									var svgElementInner = jQuery(svgDocumentContent.documentElement).clone();

									if (typeof(svgElementInner) != "undefined")
									{
										jQuery(svgElementInner).attr("height", areasContainer[areaId]["height"]);
										jQuery(svgElementInner).attr("width", areasContainer[areaId]["width"]);
										jQuery(svgElementInner).attr("preserveAspectRatio", horizontalPosition + verticalPosition + " meet");

										var userAgent = window.navigator.userAgent;
										var msie = userAgent.indexOf("MSIE ");
										var innerCode;

										if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv[ :]?11\./))
										{
											innerCode = XMLSerializer().serializeToString(svgElementInner[0]);
											innerCode = Snap.parse(innerCode);
										}
										else
										{
											innerCode = Snap.parse(svgElementInner[0].outerHTML);
										}

										svgElement.clear();
										svgElement.append(innerCode);
										insertedElement = svgElement.select("svg");
										insertedElement.node.id = "addedSvgImage" + areaId;
										insertedElement.click(removeSnapElement);
										insertedElement.mouseover(mouseIsIn);
										insertedElement.mouseout(mouseIsOut);
									}
								}
							}
						}
					}

					jQuery("#selectedClipart" + areaId).attr("rel", selectedClipart.val());
				}
			}
		}
	}

	/**
	 * Removes element linked to the event invoked.
	 *
	 * @param   e  object  Handler
	 */
	function removeSnapElement(e)
	{
		this.remove();
	}

	function mouseIsIn(e)
	{
		this.attr("cursor", "pointer");
	}

	function mouseIsOut(e)
	{
		this.attr("cursor", "auto");
	}

	/**
	 * Changes attributes of text element on SVG object
	 *
	 * @param   areaId  int  Area ID
	 */
	function changeSVGTextElement(areaId)
	{
		var text = jQuery("#textArea_" + areaId);
		var isTextarea = jQuery(text).is("textarea");
		var font = jQuery("#fontArea" + areaId);
		var color = jQuery("#colorCode" + areaId);
		var textAlign = jQuery("#textAlign" + areaId);
		var textAlignValue = jQuery(textAlign).val();
		var verticalAlign = jQuery("#verticalAlign" + areaId);

		var userAgent = window.navigator.userAgent;
		var firefox = userAgent.indexOf("Firefox");
		var msie = userAgent.indexOf("MSIE ");

		var areaX1 = parseFloat(areasContainer[areaId]["x1"]);
		var areaY1 = parseFloat(areasContainer[areaId]["y1"]);
		var areaX2 = parseFloat(areasContainer[areaId]["x2"]);
		var areaWidth = parseFloat(areasContainer[areaId]["width"]);
		var areaHeight = parseFloat(areasContainer[areaId]["height"]);
		var fontSizeValue = 12;
		var fontSize;
		var txt;
		var yPos;
		var textHoldingBox;
		var scale;
		var textBBox;

		text.css("text-align", jQuery(textAlign).val());

		var svgElement = rootSnapSvgObject.select("#areaSVGElement_" + areaId);

		if (svgElement)
		{
			if (isTextarea && typeof(jQuery(text).val()) != "undefined")
			{
				var content = jQuery(text).val();
				var x;
				var y;
				svgElement.remove();

				<?php if ($this->designType->fontsizer == 'auto') : ?>
					x = areaX1 + (areaWidth / 2);
				<?php else : ?>
					if (textAlignValue == "left")
					{
						x = areaX1;
					}
					else if (textAlignValue == "center")
					{
						x = areaX1 + (areaWidth / 2);
					}
					else if (textAlignValue == "right")
					{
						x = areaX2;
					}
				<?php endif; ?>

				txt = content.split("\n");
				svgElement = rootSnapSvgObject.text(x, areaY1, txt);
				svgElement.node.id = "areaSVGElement_" + areaId;
				svgElement.selectAll("tspan:nth-child(n+2)").attr({
					dy: "1.2em",
					x: x
				});

				rootSnapSvgObject.select("#areaBoxesLayer").append(svgElement);
			}
			else if (typeof(jQuery(text).val()) != "undefined")
			{
				svgElement.attr("text", jQuery(text).val());
			}

			if (font)
			{
				svgElement.attr("font-family", jQuery(font).find(":selected").text());
			}

			if (color && typeof(jQuery(color).val()) != "undefined")
			{
				svgElement.attr("fill", "#" + jQuery(color).val().replace("#",""));
			}

			<?php if ($this->designType->fontsizer == 'auto') : ?>
				if (isTextarea)
				{
					fontSize = 0;
					textHoldingBox = document.getElementById("areaSVGElement_" + areaId);
					textBBox = textHoldingBox.getBBox();

					while (textBBox.width < areaWidth && textBBox.height < areaHeight)
					{
						fontSize++;
						textHoldingBox = document.getElementById("areaSVGElement_" + areaId);
						textHoldingBox.setAttribute("font-size", fontSize.toString() + fontUnit);

						textBBox = textHoldingBox.getBBox();
					}

					svgElement.attr("text-anchor", "middle");

					textHoldingBox = document.getElementById("areaSVGElement_" + areaId);
					textBBox = textHoldingBox.getBBox();
					rowHeight = textBBox.height / txt.length;
					yPos = areaY1 + (areaHeight / 2);
					yPos -= (textBBox.height / 2) - (rowHeight / 2);
					svgElement.attr("y", yPos);

					if (msie > 0)
					{
						svgElement.attr("dy", ".65em");
					}
					else
					{
						svgElement.attr("dy", ".35em");
					}
				}
				else
				{
					fontSize = 1;
					svgElement.attr("font-size", fontSize + fontUnit);

					while (fontSize < areaHeight)
					{
						fontSize++;
						svgElement.attr("font-size", fontSize + fontUnit);
					}

					svgElement.attr("text-anchor", "middle");
					svgElement.attr("x", areaX1 + (areaWidth / 2));

					var verticalAlginY;

					if (firefox > 0)
					{
						verticalAlginY = areaY1 + (areaHeight / 2);

						svgElement.attr("y", verticalAlginY);
						svgElement.attr("dominant-baseline", "middle");
					}
					else
					{
						verticalAlginY = areaY1 + (areaHeight / 2);

						svgElement.attr("y", verticalAlginY);
						svgElement.attr("dy", ".35em");
					}

					yPos = areaY1 + (areaHeight / 2);

					textHoldingBox = document.getElementById("areaSVGElement_" + areaId);
					textBBox = textHoldingBox.getBBox();

					if (textBBox.width > areaWidth)
					{
						scale = areaWidth / textBBox.width;
						svgElement.transform("s" + scale.toString());
					}
					else if (textBBox.width < areaWidth)
					{
						scale = areaHeight / textBBox.height;
						svgElement.transform("s" + scale.toString());
					}
				}
			<?php else : ?>
				fontSize = jQuery("#fontSize" + areaId);

				if (fontSize && typeof(jQuery(fontSize).val()) != "undefined")
				{
					fontSize = jQuery(fontSize).val().split(":");

					if (fontSize.length > 1)
					{
						fontSizeValue = fontSize[1];
					}
					else
					{
						fontSizeValue = fontSize[0];
					}

					svgElement.attr("font-size", fontSizeValue + fontUnit);
					svgElement.attr("y", areaY1 + ((parseFloat(fontSizeValue) * scalingImageForPreviewRatio)));
				}
			<?php endif; ?>

			<?php if ($this->designType->fontsizer != 'auto') : ?>
				svgElement.attr("text-anchor", textAlignValue.replace("left", "start").replace("center", "middle").replace("right", "end"));

				if (textAlignValue == "left")
				{
					svgElement.attr("x", areaX1);
				}
				else if (textAlignValue == "center")
				{
					svgElement.attr("x", areaX1 + (areaWidth / 2));
				}
				else if (textAlignValue == "right")
				{
					svgElement.attr("x", areaX2);
				}

				var rowHeight;

				if (verticalAlign && typeof(jQuery(verticalAlign).val()) != "undefined")
				{
					var verticalAlignValue = jQuery(verticalAlign).val();

					if (verticalAlignValue == "top")
					{
						svgElement.attr("y", areaY1);
						svgElement.attr("dy", "0.7em");
					}
					else if (verticalAlignValue == "middle")
					{
						yPos = areaY1 + (areaHeight / 2);

						if (isTextarea)
						{
							rowHeight = svgElement.getBBox().h / txt.length;

							yPos -= (svgElement.getBBox().h / 2) - (rowHeight / 2);
							svgElement.attr("y", yPos);

							if (msie > 0)
							{
								svgElement.attr("dy", ".45em");
							}
							else
							{
								svgElement.attr("dy", ".35em");
							}
						}
						else
						{
							svgElement.attr("y", yPos);
							svgElement.attr("dy", ".35em");
						}
					}
					else if (verticalAlignValue == "bottom")
					{
						yPos = areaY1 + areaHeight;

						if (isTextarea)
						{
							rowHeight = svgElement.getBBox().h / txt.length;
							yPos -= (svgElement.getBBox().h - rowHeight);

							svgElement.attr("y", yPos);
							svgElement.attr("dy", "0");
						}
						else
						{
							svgElement.attr("y", yPos);
							svgElement.attr("dy", "0");
						}
					}
				}
			<?php endif; ?>
		}
	}

	/**
	 * Converts hexadecimal value into RGB value
	 *
	 * @param r int Red value
	 * @param g int Green value
	 * @param b int Blue value
	 *
	 * @return string hexadecimal value
	 */
	function rgbToHex(r, g, b) {
		return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
	}


	/**
	 * Sends customize data to server and retreives the resulting image.
	 *
	 */
	function customize() {
		jQuery("#background-container").height(jQuery("#background").height());

		<?php foreach($this->displayedAreas as $area) :
				$areaType = ReddesignHelpersArea::getAreaType($area->areaType)
		?>

			var x1 = parseFloat("<?php echo $area->x1_pos; ?>") * scalingImageForPreviewRatio;
			var y1 = parseFloat("<?php echo $area->y1_pos; ?>") * scalingImageForPreviewRatio;
			var x2 = parseFloat("<?php echo $area->x2_pos; ?>") * scalingImageForPreviewRatio;
			var y2 = parseFloat("<?php echo $area->y2_pos; ?>") * scalingImageForPreviewRatio;
			var width = parseFloat("<?php echo $area->width; ?>") * scalingImageForPreviewRatio;
			var height = parseFloat("<?php echo $area->height; ?>") * scalingImageForPreviewRatio;
			var svgElement;
			var areaIdIndex = parseInt("<?php echo $area->id; ?>");

			areasContainer[areaIdIndex] = new Array();
			areasContainer[areaIdIndex]['x1'] = x1;
			areasContainer[areaIdIndex]['y1'] = y1;
			areasContainer[areaIdIndex]['x2'] = x2;
			areasContainer[areaIdIndex]['y2'] = y2;
			areasContainer[areaIdIndex]['width'] = width;
			areasContainer[areaIdIndex]['height'] = height;
			areasContainer[areaIdIndex]['areaType'] = '<?php echo $areaType['name']; ?>';

			if (areasContainer[areaIdIndex]['areaType'] == 'text')
			{
				svgElement = Snap.parse(
					'<text id="areaSVGElement_' + areaIdIndex + '" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ '></text>'
				);
			}
			else if (areasContainer[<?php echo $area->id ?>]['areaType'] == 'clipart')
			{
				svgElement = Snap.parse(
					'<svg id="areaSVGElement_' + areaIdIndex + '" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ ' width="' + width + '"'
						+ ' height="' + height + '"'
						+ '></svg>'
				);
			}

			rootSnapSvgObject.select("#areaBoxesLayer").append(svgElement);

			changeSVGElement(areaIdIndex);

		<?php endforeach; ?>
	}

	/**
	 * Set selected color for designarea.
	 *
	 * @param id
	 * @param colorCode
	 */
	function setColorCode(id, colorCode)
	{
		document.getElementById("colorCode" + id).value = colorCode;
		jQuery("#fontColor" + id+ " div").css("backgroundColor", "#" + colorCode);
		jQuery("#fontColor" + id).show();
		changeSVGElement(id);
	}

	/**
	 * Navigate to selected background.
	 *
	 * @param propertyId
	 */
	function changeBackground(propertyId)
	{
		<?php if ($preserveData) : ?>
			var textInputsBeforeLoad = jQuery(".textAreaClass");
			var fontSelectionsBeforeLoad = jQuery(".reddesign-font-selection");

			<?php if ($this->designType->fontsizer != 'auto') : ?>
				var fontSizesBeforeLoad = jQuery(".reddesign-font-size-selection");
				var horizontalAlignsBeforeLoad = jQuery(".horizontal-text-alignment");
				var verticalAlignsBeforeLoad = jQuery(".vertical-text-alignment");
			<?php endif; ?>

			var colorCodeBeforeLoad = jQuery(".color-code");
		<?php endif; ?>

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxLoadDesigntype",
			data: {'propertyId': propertyId},
			dataType: "text",
			type: "post",
			success: function (data)
			{
				jQuery("#mainSvgImage").empty();
				jQuery("#areasContainer").empty();
				jQuery("#areasContainer").html(data);
				jQuery('#svgContainer .progress .bar-success').css('width', '0%');
				jQuery('#svgContainer .progress').show().addClass('active');
				jQuery("#areasContainer .flexslider").flexslider({
					'slideshow': false,
					'directionNav': true,
					'minItems': 4,
					'itemWidth': 95,
					'maxItems': 4,
					'prevText': '',
					'nextText': '',
					'animation': 'slide',
					'animationLoop': false
				});

				<?php if ($preserveData) : ?>

					jQuery.each(textInputsBeforeLoad, function(index, value) {
						var textBoxElement = jQuery("[name='" + textInputsBeforeLoad[index].name + "']");

						if(typeof textBoxElement !== 'undefined')
						{
							textBoxElement.val(value.value);
						}
					});

					jQuery.each(fontSelectionsBeforeLoad, function(index, value) {
						var fontSelectionElement = jQuery("[name='" + fontSelectionsBeforeLoad[index].name + "']");

						if(typeof fontSelectionElement !== 'undefined')
						{
							fontSelectionElement.val(value.value);
						}
					});

					<?php if ($this->designType->fontsizer != 'auto') : ?>

						jQuery.each(fontSizesBeforeLoad, function(index, value) {
							var fontSizeElement = jQuery("[name='" + fontSizesBeforeLoad[index].name + "']");

							if(typeof fontSizeElement !== 'undefined')
							{
								fontSizeElement.val(value.value);
							}
						});

						jQuery.each(horizontalAlignsBeforeLoad, function(index, value) {
							var horizontalAlignElement = jQuery("[name='" + horizontalAlignsBeforeLoad[index].name + "']");

							if(typeof horizontalAlignElement !== 'undefined')
							{
								horizontalAlignElement.val(value.value);
							}
						});

						jQuery.each(verticalAlignsBeforeLoad, function(index, value) {
							var verticalAlignElement = jQuery("[name='" + verticalAlignsBeforeLoad[index].name + "']");

							if(typeof verticalAlignElement !== 'undefined')
							{
								verticalAlignElement.val(value.value);
							}
						});

					<?php endif; ?>

					jQuery.each(colorCodeBeforeLoad, function(index, value) {
						var colorCodeElement = jQuery("[name='" + colorCodeBeforeLoad[index].name + "']");

						if(typeof colorCodeElement !== 'undefined')
						{
							colorCodeElement.val(value.value);
						}
					});

				<?php endif; ?>

				thumbnailSVGLoader();
				loadBackgroundSVGandAreas(propertyId);
				addUploadButtonCall();

				jQuery(".colorPickerSelectedColor").click(function(){
					jQuery(".color-wheel").hide();
					jQuery(".cmyk-inputs").hide();

					if(jQuery(this).parent().parent().parent().parent().parent().parent().parent().hasClass("wheel-clicked")) {
						jQuery(this).parent().parent().parent().parent().find(".color-wheel").hide();
						jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").hide();
						jQuery(this).parent().parent().parent().parent().parent().parent().parent().removeClass("wheel-clicked")
					}
					else {
						jQuery("#areasContainer li").removeClass("wheel-clicked")
						jQuery(this).parent().parent().parent().parent().find(".color-wheel").show();
						jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").show();
						jQuery(this).parent().parent().parent().parent().parent().parent().parent().addClass("wheel-clicked");
					}
				});
			},
			error: function (data)
			{
				console.log("Error: " + data);
			}
		});
	}

	function loadBackgroundSVGandAreas(propertyId)
	{
		var areas;

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxGetBackground",
			data: {'propertyId': propertyId},
			type: "post",
			success: function (data)
			{
				var background = jQuery.parseJSON(data);
				areas = background.areas;

				jQuery("#designtype_id").val(background.designtype_id);
				jQuery("#background_id").val(background.id);
				jQuery("#production_background_id").val(background.productionBackgroundId);
				jQuery(".number_product h1").html(background.designtype_name);

				scalingImageForPreviewRatio = previewWidth / parseFloat(background.width);
				previewHeight = parseFloat(background.height) * scalingImageForPreviewRatio;

				rootSnapSvgObject = Snap("#mainSvgImage");

				jQuery.ajax({
					url: "<?php echo JURI::base() . 'media/com_reddesign/backgrounds/'; ?>" + background.svg_file,
					dataType: "text",
					cache: true,
					xhrFields: {
						onprogress: function (e) {
							if (e.lengthComputable) {
								var loadedPercentage = parseInt(e.loaded / e.total * 100);
								jQuery("#svgContainer .progress .bar-success")
									.css('width', '' + (loadedPercentage) + '%')
									.html(loadedPercentage + '% <?php echo JText::_('COM_REDDESIGN_COMMON_PROGRESS_LOADED', true); ?>');
							}
						}
					},
					success: function (response) {
						jQuery('#svgContainer .progress').removeClass('active');
						if(typeof response === 'undefined' || response == false){
							jQuery('#svgContainer .progress').append(
								'<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#svgContainer .progress .bar-success').css('width'))) + '%;"></div>'
							);
						}
						else{
							jQuery('#svgContainer .progress').fadeOut(3000);
						}

						if (background.useCheckerboard == '1')
						{
							jQuery('#mainSvgImage').parent().width(previewWidth).height(previewHeight).addClass('checkerboard');
						}
						jQuery("#mainSvgImage")
							.append('<defs><style type="text/css">' +  background.selectedFontsDeclaration + '</style></defs>')
							.append(response);

						// Set preview size at loaded file.
						var loadedSvgFromFile = jQuery("#mainSvgImage").find("svg")[0];
						loadedSvgFromFile.setAttribute("width", previewWidth.toString() + "px");
						loadedSvgFromFile.setAttribute("height", previewHeight.toString() + "px");
						loadedSvgFromFile.setAttribute("id", "svgCanvas");

						// Set preview size at svg container element.
						var rootElement = document.getElementById("mainSvgImage");
						rootElement.setAttribute("width", previewWidth.toString() + "px");
						rootElement.setAttribute("height", previewHeight.toString() + "px");
						rootElement.setAttribute("overflow", "hidden");

						var groupObject = rootSnapSvgObject.group();
						groupObject.node.id = "areaBoxesLayer";

						customizeJS(areas);

						if (jQuery(".fontSizeSlider").length > 0)
						{
							jQuery(".fontSizeSlider").slider()
								.on("slide", function(ev){
									var id = jQuery(this).attr("id").replace("fontSizeSlider", "");
									jQuery("#fontSize" + id).val(ev.value);
									changeSVGElement(id);
								});
						}

					}
				});
			},
			error: function (data)
			{
				console.log("Error: " + data);
			}
		});
	}

	function customizeJS(areas)
	{
		jQuery("#background-container").height(jQuery("#background").height());

		for (var i=0; i<areas.length; i++)
		{
			var x1 = parseFloat(areas[i].x1_pos) * scalingImageForPreviewRatio;
			var y1 = parseFloat(areas[i].y1_pos) * scalingImageForPreviewRatio;
			var x2 = parseFloat(areas[i].x2_pos) * scalingImageForPreviewRatio;
			var y2 = parseFloat(areas[i].y2_pos) * scalingImageForPreviewRatio;
			var width = parseFloat(areas[i].width) * scalingImageForPreviewRatio;
			var height = parseFloat(areas[i].height) * scalingImageForPreviewRatio;
			var svgElement;

			areasContainer[areas[i].id] = new Array();
			areasContainer[areas[i].id]["x1"] = x1;
			areasContainer[areas[i].id]["y1"] = y1;
			areasContainer[areas[i].id]["x2"] = x2;
			areasContainer[areas[i].id]["y2"] = y2;
			areasContainer[areas[i].id]["width"] = width;
			areasContainer[areas[i].id]["height"] = height;

			if (areas[i].areaType == "1") // text
			{
				svgElement = Snap.parse(
					'<text id="areaSVGElement_'
						+ areas[i].id + '" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ '></text>'
				);
			}
			else if (areas[i].areaType == '2') //clipart
			{
				svgElement = Snap.parse(
					'<svg id="areaSVGElement_'
						+ areas[i].id + '" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ ' width="' + width + '"'
						+ ' height="' + height + '"'
						+ '></svg>'
				);
			}
			rootSnapSvgObject.select("#areaBoxesLayer").append(svgElement);

			changeSVGElement(areas[i].id);
		}
	}

	function showClipartUpload(areaId)
	{
		toggleClipartContainers('clipartUpload', areaId);

	}

	function toggleClipartContainers(active, areaId)
	{
		jQuery("#featuredCliparts" + areaId).slideUp("slow");
		jQuery("#clipartBank" + areaId).slideUp("slow");
		jQuery("#clipartUpload" + areaId).slideUp("slow");

		jQuery("#" + active + areaId).slideDown("slow");

		jQuery("#featuredClipartsButton" + areaId).removeClass('btn-success');
		jQuery("#clipartBankButton" + areaId).removeClass('btn-success');
		jQuery("#clipartUploadButton" + areaId).removeClass('btn-success');

		jQuery("#" + active + "Button" + areaId).addClass('btn-success');
	}

	function loadClipartBank(areaId, active, filterApplied)
	{
		if ((!filterApplied && jQuery("#clipartBank" + areaId).html().trim() != '') || active == 'featuredCliparts')
		{
			toggleClipartContainers(active, areaId);
		}
		else
		{
			var clipartCategoryId = jQuery("#clipartCategoryId" + areaId).val();
			var clipartSearch = jQuery("#clipartSearch" + areaId).val();
			jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxLoadClipartBank",
				data: {
					'categoryId': clipartCategoryId,
					'search': clipartSearch,
					'areaId': areaId
				},
				type: "get",
				dataType: 'text',
				success: function (data)
				{
					toggleClipartContainers('clipartBank', areaId);
					jQuery("#clipartBank" + areaId).html(data);
					jQuery("#clipartBank" + areaId + " .flexslider2").removeClass("flexslider2").addClass("flexslider");
					jQuery("#clipartBank" + areaId + " .flexslider").flexslider({
						'slideshow': false,
						'directionNav': true,
						'minItems': 4,
						'itemWidth': 95,
						'maxItems': 4,
						'prevText': '',
						'nextText': '',
						'animation': 'slide',
						'animationLoop': false
					});
				},
				error: function (data)
				{
					console.log("Error: " + data);
				}
			});
		}
	}

	function uploadCustomClipart(areaId) {
		var dataVar = {
			'areaId' : areaId
		};
		var url = "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxUploadCustomClipart";

		var inputFileNode = jQuery("#uploadClipartFile" + areaId);
		var buttonData = inputFileNode.data();

		jQuery("#clipartUpload" + areaId + " .image-progress .bar").css("width","0%");

		if (jQuery.isEmptyObject(buttonData) == false) {
			jQuery("#clipartUpload" + areaId + " .fileinput-button input").fileupload({
				url: url,
				dataType: "text",
				autoUpload: false,
				cache: false,
				formData: dataVar,
				acceptFileTypes: /(\.|\/)(svg,jpg,png,gif)$/i
			}).on("fileuploadprogressall", function (e, data) {
				jQuery(inputFileNode).prop("disabled", true);

				var progress = parseInt(data.loaded / data.total * 100, 10);
				jQuery("#clipartUpload" + areaId + " .image-progress .bar").css("width", progress + "%");
			}).on("fileuploaddone", function (e, data) {

				if (data.result)
				{
					var returnedData = jQuery.parseJSON(data.result);

					if (returnedData.message == "")
					{
						jQuery("#uploadedClipart" + areaId).html(returnedData.result);
						jQuery("#fileNameUpload" + areaId).remove();
					}
					else
					{
						jQuery("#fileNameUpload" + areaId).remove();
						jQuery("#clipartUpload" + areaId + " .image-progress .bar").css("width", "0");
						alert(returnedData.message);
					}
				}

				jQuery(inputFileNode).prop("disabled", false);
			}).on("fileuploadfail", function (e, data) {
				jQuery.each(data.files, function (index, file) {
					var error = jQuery('<span class="text-danger"/>');

					jQuery(data.context.children()[index])
						.append("<br>")
						.append(error);
				});
			}).prop("disabled", !jQuery.support.fileInput)
			  .parent().addClass(jQuery.support.fileInput ? undefined : "disabled");

			buttonData.submit();
		}
		else
		{
			alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD_ERROR_SELECT_FILE', true); ?>");
		}
	}

	function addUploadButtonCall()
	{
		jQuery(".fileinput-button input").fileupload({
			autoUpload: false,
			acceptFileTypes: /(\.|\/)(svg,jpg,png,gif)$/i
		}).on("fileuploadadd", function (e, data) {
			var id = (jQuery(this).attr("id").replace("uploadClipartFile", ""));
			data.context = jQuery("<div/>").appendTo("#uploadedClipart" + id);

			jQuery.each(data.files, function (index, file) {
				var node = jQuery("<p/>").append("<span id='fileNameUpload" + id + "'>" + file.name + "</span>");
				jQuery("#uploadClipartFile" + id).data(data);
				node.appendTo(data.context);
			});
		});
	}
</script>
{RedDesignBreakFormEndsAndJS}
