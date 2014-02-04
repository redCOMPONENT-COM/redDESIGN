<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHtml::_('behavior.modal');

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
$previewWidth = $config->getMaxSVGPreviewSiteWidth();
$unit = $config->getUnit();
$fontUnit = $config->getFontUnit();
$sourceDpi = $config->getSourceDpi();

$unitConversionRatio = ReddesignHelpersSvg::getUnitConversionRatio($unit, $sourceDpi);

/*
{RedDesignBreakELEMENT} is a tag used in integration plugin to explode HTML string into smaller peaces. Those peaces are used in redSHOP templating.
*/

$input = JFactory::getApplication()->input;
$productId = $input->getInt('pid', 0);
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
		<div class="well span12">
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
			<svg id="mainSvgImage"></svg>
			<div class="progressbar-holder" style="width: <?php echo $displayData->displayedBackground->width; ?>px; margin-top:20px;">
				<div class="progress progress-striped" style="display:none;">
					<div class="bar bar-success"></div>
				</div>
			</div>
		</div>

		<div id="progressBar" style="display: none;">
			<div class="progress progress-striped active">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>

	</div>
{RedDesignBreakDesignImage}

	<?php // Part 6 - Areas Begin ?>
{RedDesignBreakDesignAreas}
	<div class="row-fluid">
		<div class="well span12">
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

	/**
	 * Add click event to Customize button.
	 */
	jQuery(document).ready(function () {
			rootSnapSvgObject = Snap("#mainSvgImage");

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
						jQuery('#svgContainer .progress').show().addClass('active');
						jQuery('#svgContainer .progress .bar-success').css('width', '0%');
					},
					success: function (response) {
						jQuery('#svgContainer .progress').removeClass('active');
						if(typeof response === 'undefined' || response == false){
							jQuery('#svgContainer .progress').append(
								'<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#svgContainer .progress .bar-success').css('width'))) + '%;"></div>'
							);
						}
						else{
							jQuery('#svgContainer .progressbar-holder').fadeOut(3000);
						}

						jQuery("#mainSvgImage")
							.append('<defs><style type="text/css"><?php echo $this->selectedFontsDeclaration; ?></style></defs>')
							.append(response);

						// Set preview size at loaded file.
						var loadedSvgFromFile = jQuery("#mainSvgImage").find("svg")[0];
						loadedSvgFromFile.setAttribute("width", previewWidth);
						loadedSvgFromFile.setAttribute("height", previewHeight);
						loadedSvgFromFile.setAttribute("id", "svgCanvas");

						// Set preview size at svg container element.
						var rootElement = document.getElementById("mainSvgImage");
						rootElement.setAttribute("width", previewWidth);
						rootElement.setAttribute("height", previewHeight);
						rootElement.setAttribute("overflow", "hidden");

						rootSnapSvgObject.group().node.id = "areaBoxesLayer";

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

			jQuery(document).on("keyup", ".reddesign-form .textAreaClass", function() {
				var id = jQuery(this).attr('id').replace('textArea_', '');
				changeSVGTextElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-selection", function() {
				var id = jQuery(this).attr('id').replace('fontArea', '');
				changeSVGTextElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-size-selection", function() {
				var id = jQuery(this).attr('id').replace('fontSize', '');
				changeSVGTextElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textAlign button", function() {
				var id = jQuery(this).attr('name').replace('textAlignButton', '');
				jQuery('#textAlign' + id).val(jQuery(this).attr('value'));
				changeSVGTextElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textVerticalAlign button", function() {
				var id = jQuery(this).attr('name').replace('textVerticalAlignButton', '');
				jQuery('#verticalAlign' + id).val(jQuery(this).attr('value'));
				changeSVGTextElement(id);
			});

			if (jQuery('.fontSizeSlider').length > 0)
				jQuery('.fontSizeSlider').slider()
					.on('slide', function(ev){
						var id = jQuery(this).attr('id').replace('fontSizeSlider', '');
						jQuery('#fontSize' + id).val(ev.value);
						changeSVGTextElement(id);
					});

			jQuery(document).on("keyup, change", ".reddesign-form .colorCode", function() {
				var id = jQuery(this).attr('id').replace('colorCode', '');
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

		changeSVGTextElement(areaId);
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
	 * Changes value of textelement on SVG object
	 *
	 * @param   areaId  int  Area ID
	 */
	function changeSVGTextElement(areaId)
	{
		var text = jQuery('#textArea_' + areaId);
		var font = jQuery('#fontArea' + areaId);
		var color = jQuery('#colorCode' + areaId);
		var fontSize = jQuery('#fontSize' + areaId);
		var textAlign = jQuery('#textAlign' + areaId);
		var verticalAlign = jQuery('#verticalAlign' + areaId);
		var fontSizeValue = 12;

		text.css('text-align', jQuery(textAlign).val());

		var textElement = rootSnapSvgObject.select("#areaBoxesLayer #areaTextElement_" + areaId);

		if (textElement)
		{
			if (font)
			{
				textElement.attr('font-family', jQuery(font).find(':selected').text());
			}

			if (color)
			{
				textElement.attr('fill', '#' + jQuery(color).val().replace('#',''));
			}

			if (fontSize)
			{
				fontSize = jQuery(fontSize).val().split(":");

				if (fontSize.length > 1)
					fontSizeValue = fontSize[1];
				else
					fontSizeValue = fontSize[0];

				textElement.attr('font-size', fontSizeValue + fontUnit);
				textElement.attr('y', parseFloat(areasContainer[areaId]['y1']) + ((parseFloat(fontSizeValue) * scalingImageForPreviewRatio)));
			}

			if (textAlign)
			{
				var textAlignValue = jQuery(textAlign).val();
				textElement.attr('text-anchor', textAlignValue.replace('left', 'start').replace('center', 'middle').replace('right', 'end'));

				if (textAlignValue == 'left')
				{
					textElement.attr('x', areasContainer[areaId]['x1']);
				}
				else if (textAlignValue == 'center')
				{
					textElement.attr('x', parseFloat(areasContainer[areaId]['x1']) + (parseFloat(areasContainer[areaId]['width']) / 2));
				}
				else if (textAlignValue == 'right')
				{
					textElement.attr('x', parseFloat(areasContainer[areaId]['x2']));
				}
			}

			if (verticalAlign)
			{
				var verticalAlignValue = jQuery(verticalAlign).val();

				if (verticalAlignValue == 'top')
				{
					textElement.attr('y', areasContainer[areaId]['y1']);
				}
				else if (verticalAlignValue == 'middle')
				{
					var yPos = parseFloat(areasContainer[areaId]['y1']) + (parseFloat(areasContainer[areaId]['height']) / 2);
					if (fontSizeValue)
						yPos -= (parseFloat(fontSizeValue) / 2);
					textElement.attr('y', yPos);
				}
				else if (verticalAlignValue == 'bottom')
				{
					var yPos = parseFloat(areasContainer[areaId]['y1']) + (parseFloat(areasContainer[areaId]['height']));
					if (fontSizeValue)
						yPos -= fontSizeValue;
					textElement.attr('y', yPos);
				}
			}

			if (jQuery(text).is("textarea"))
			{
				createSVGTextMultiline(textElement, jQuery(text).val())
			}
			else
			{
				textElement.node.textContent = jQuery(text).val();
			}
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
		// Add the progress bar
		var halfBackgroundHeight =  ((jQuery("#background").height() / 2)-10);
		jQuery("#background-container").height(jQuery("#background").height());
		jQuery("#progressBar").css("padding-top", halfBackgroundHeight + "px").css("padding-bottom", halfBackgroundHeight + "px").show();

		var background_id = jQuery("#background_id").val();

		<?php foreach($this->displayedAreas as $area) :?>

			var fontColor = "000000";

			if (jQuery("#colorCode<?php echo $area->id; ?>").length > 0)
			{
				fontColor = jQuery("#colorCode<?php echo $area->id; ?>").val().replace("#", "");
			}

			var x1 = parseFloat(<?php echo $area->x1_pos; ?>) * scalingImageForPreviewRatio;
			var y1 = parseFloat(<?php echo $area->y1_pos; ?>) * scalingImageForPreviewRatio;
			var x2 = parseFloat(<?php echo $area->x2_pos; ?>) * scalingImageForPreviewRatio;
			var y2 = parseFloat(<?php echo $area->y2_pos; ?>) * scalingImageForPreviewRatio;
			var width = x2 - x1;
			var height = y2 - y1;

			areasContainer[<?php echo $area->id; ?>] = new Array();
			areasContainer[<?php echo $area->id; ?>]['x1'] = x1;
			areasContainer[<?php echo $area->id; ?>]['y1'] = y1;
			areasContainer[<?php echo $area->id; ?>]['x2'] = x2;
			areasContainer[<?php echo $area->id; ?>]['y2'] = y2;
			areasContainer[<?php echo $area->id; ?>]['width'] = width;
			areasContainer[<?php echo $area->id; ?>]['height'] = height;

			var fontSizeValue = 12;
			if (jQuery("#fontSize<?php echo $area->id; ?>").length > 0)
			{
				var fontSize = jQuery("#fontSize<?php echo $area->id; ?>").val().split(":");

				if (fontSize.length > 1)
				{
					fontSizeValue = fontSize[1];
				}
				else
				{
					fontSizeValue = fontSize[0];
				}
			}

			var textElement = Snap.parse(
				'<text id="areaTextElement_<?php echo $area->id; ?>" '
					+ ' x="' + x1 + '"'
					+ ' y="' + y1 + '"'
					+ '></text>'
			);

			rootSnapSvgObject.select("#areaBoxesLayer").append(textElement);

			changeSVGTextElement(<?php echo $area->id; ?>);

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
		changeSVGTextElement(id);
	}

	/**
	 * Navigate to selected background.
	 *
	 * @param propertyId
	 */
	function changeBackground(propertyId)
	{
		jQuery("#mainSvgImage").empty();
		jQuery("#areasContainer").empty();

		var areas;

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxLoadDesigntype",
			data: {'propertyId': propertyId},
			type: "post",
			success: function (data)
			{
				jQuery("#areasContainer").html(data);
			},
			error: function (data)
			{
				console.log("Error: " + data);
			}
		});

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxGetBackground",
			data: {'propertyId': propertyId},
			type: "post",
			success: function (data)
			{
				var background = jQuery.parseJSON(data);

				areas = background.areas;

				var canvasWidth  = parseFloat("<?php echo $previewWidth; ?>");
				var scalingRatio = canvasWidth / parseFloat(background.width);
				var canvasHeight = parseFloat(background.height) * scalingRatio;

				rootSnapSvgObject = Snap("#mainSvgImage");

				jQuery.ajax({
					url: "<?php echo JURI::base() . 'media/com_reddesign/backgrounds/'; ?>" + background.svg_file,
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
						jQuery('#svgContainer .progress').show().addClass('active');
						jQuery('#svgContainer .progress .bar-success').css('width', '0%');
					},
					success: function (response) {
						jQuery('#svgContainer .progress').removeClass('active');
						if(typeof response === 'undefined' || response == false){
							jQuery('#svgContainer .progress').append(
								'<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#svgContainer .progress .bar-success').css('width'))) + '%;"></div>'
							);
						}
						else{
							jQuery('#svgContainer .progressbar-holder').fadeOut(3000);
						}

						jQuery("#mainSvgImage")
							.append('<defs><style type="text/css">' +  background.selectedFontsDeclaration + '</style></defs>')
							.append(response);

						// Set preview size at loaded file.
						var loadedSvgFromFile = jQuery("#mainSvgImage").find("svg")[0];
						loadedSvgFromFile.setAttribute("width", canvasWidth);
						loadedSvgFromFile.setAttribute("height", canvasHeight);
						loadedSvgFromFile.setAttribute("id", "svgCanvas");

						// Set preview size at svg container element.
						var rootElement = document.getElementById("mainSvgImage");
						rootElement.setAttribute("width", canvasWidth);
						rootElement.setAttribute("height", canvasHeight);
						rootElement.setAttribute("overflow", "hidden");

						rootSnapSvgObject.group().node.id = "areaBoxesLayer";
					}
				});
			},
			error: function (data)
			{
				console.log("Error: " + data);
			}
		});

		customizeJS(areas);
	}

	/**
	 * Split text in multiline order by adding tspan elements
	 *
	 */
	function createSVGTextMultiline(svgTextElement, title)
	{
		var x = svgTextElement.attr('x');
		var y = parseFloat(svgTextElement.attr('y'));
		var lineHeight = 16;

		var sentences = title.split("\n");
		svgTextElement.node.textContent = '';

		var lines = '';

		for (var n = 0; n < sentences.length; n++)
		{
			var svgTSpan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
			svgTSpan.setAttributeNS(null, 'x', x);
			svgTSpan.setAttributeNS(null, 'y', y);

			var tSpanTextNode = document.createTextNode(sentences[n]);
			svgTSpan.appendChild(tSpanTextNode);
			svgTextElement.append(svgTSpan);

			y += lineHeight;
		}
	}

	function customizeJS(areas)
	{
		console.log(areas);
	}
</script>
{RedDesignBreakFormEndsAndJS}
