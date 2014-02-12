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
		<div id="fullScreenContainer" class="hide fade redcore" style="padding:0px;">
			<div class="row-fluid">
				<div class="span10 svg-container-element" id="fullScreenContainerSVG">
				</div>
				<div class="span2 right-modal-buttons">
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
					jQuery(svgElementInner).attr("height", <?php echo $clipartPreviewHeight; ?>);
					jQuery(svgElementInner).attr("width", <?php echo $clipartPreviewWidth; ?>);
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
							jQuery('#svgContainer .progress').fadeOut(3000);
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
				previewSVG.setAttribute("width", width);
				previewSVG.setAttribute("height", height);
				previewSVG.setAttribute("id", "fullScreenContainerSVG2");

				// first inner SVG
				var previewSVGInner = jQuery(previewSVG).find('svg:first')[0];
				previewSVGInner.setAttribute("width", width);
				previewSVGInner.setAttribute("height", height);

				jQuery(previewSVG).children('g').each(function () {
					/* @todo Second way, leave for now for test */
					/*jQuery(this).children().each(function () {
						jQuery(this).attr('x', ((parseFloat(jQuery(this).attr('x')) * (scale))));
						jQuery(this).attr('y', ((parseFloat(jQuery(this).attr('y')) * (scale))));

						if (typeof(jQuery(this).attr('width')) != "undefined")
						{
							var innerWidth = (parseFloat(jQuery(this).attr('width')) * (scale));
							jQuery(this).attr('width', innerWidth);

						}
						if (typeof(jQuery(this).attr('height')) != "undefined")
						{
							var innerHeight = (parseFloat(jQuery(this).attr('height')) * (scale));
							jQuery(this).attr('height', innerHeight);
						}
						var previewSVGInnerSVG = jQuery(this).find('svg:first')[0];
						if (typeof(previewSVGInnerSVG) != "undefined")
						{
							previewSVGInnerSVG.setAttribute("width", innerWidth);
							previewSVGInnerSVG.setAttribute("height", innerHeight);
						}

						if (typeof(jQuery(this).css('font-size')) != "undefined")
						{
							jQuery(this).css('font-size', parseFloat((jQuery(this).css('font-size').replace('px', '')) * scale) + 'px');
						}
					});*/
					jQuery(this).attr('transform', 'scale(' + scale + ')');

				});

				var options = {
					size: {x: boxWidth, y: boxHeight},
					handler: 'clone',
					clone: 'fullScreenContainer'
				};

				SqueezeBox.fromElement(this, options);
			});

			jQuery(document).on("keyup", ".reddesign-form .textAreaClass", function() {
				var id = jQuery(this).attr('id').replace('textArea_', '');
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-selection", function() {
				var id = jQuery(this).attr('id').replace('fontArea', '');
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .reddesign-font-size-selection", function() {
				var id = jQuery(this).attr('id').replace('fontSize', '');
				changeSVGElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textAlign button", function() {
				var id = jQuery(this).attr('name').replace('textAlignButton', '');
				jQuery('#textAlign' + id).val(jQuery(this).attr('value'));
				changeSVGElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .btn-group-textVerticalAlign button", function() {
				var id = jQuery(this).attr('name').replace('textVerticalAlignButton', '');
				jQuery('#verticalAlign' + id).val(jQuery(this).attr('value'));
				changeSVGElement(id);
			});

			jQuery(document).on("click", ".reddesign-form .load-clipart-bank", function() {
				var id = jQuery(this).attr('id').replace('clipartBankButton', '');
				loadClipartBank(id, 'clipartBank', false);
			});

			jQuery(document).on("click", ".reddesign-form .upload-clipart-button", function() {
				var id = jQuery(this).attr('id').replace('clipartUploadButton', '');
				showClipartUpload(id);
			});

			jQuery(document).on("click", ".reddesign-form .upload-clipart-file", function() {
				var id = jQuery(this).attr('id').replace('uploadClipartFileSave', '');
				uploadCustomClipart(id);
			});

			jQuery(document).on("click", ".reddesign-form .featured-cliparts", function() {
				var id = jQuery(this).attr('id').replace('featuredClipartsButton', '');
				loadClipartBank(id, 'featuredCliparts', false);
			});

			jQuery(document).on("mousedown", ".reddesign-form .thumbnailSVG-pointer", function() {
				var id = jQuery(this).attr('name').replace('clipart', '');
				var clipartId = jQuery(this).parent().find('.change-selected-clipart').val();
				jQuery("#selectedClipart" + id).val(clipartId);
				changeSVGElement(id);
			});

			jQuery(document).on("change", ".reddesign-form .change-selected-clipart", function() {
				var id = jQuery(this).attr('name').replace('selectedClipart', '');
				changeSVGElement(id);
			});

			if (jQuery('.fontSizeSlider').length > 0)
			{
				jQuery('.fontSizeSlider').slider()
					.on('slide', function(ev){
						var id = jQuery(this).attr('id').replace('fontSizeSlider', '');
						jQuery('#fontSize' + id).val(ev.value);
						changeSVGElement(id);
					});
			}

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
	 * @param   areaId  int  Area ID
	 */
	function changeSVGElement(areaId)
	{
		var area = jQuery("#areaBoxesLayer #areaSVGElement_" + areaId);
		if (jQuery(area).is("text"))
		{
			changeSVGTextElement(areaId);
		}
		else if (jQuery(area).is("svg"))
		{
			changeSVGClipartElement(areaId);
		}
	}

	/**
	 * Changes attributes of text element on SVG object
	 *
	 * @param   areaId  int  Area ID
	 */
	function changeSVGClipartElement(areaId)
	{
		var horizontalAlign = jQuery('#textAlign' + areaId);
		var verticalAlign = jQuery('#verticalAlign' + areaId);
		var selectedClipart = jQuery('#selectedClipart' + areaId);
		var svgElement = rootSnapSvgObject.select("#areaBoxesLayer #areaSVGElement_" + areaId);
		if (svgElement)
		{
			var horizontalPosition = '';
			var verticalPosition = '';
			if (horizontalAlign && typeof(jQuery(horizontalAlign).val()) != "undefined")
			{
				horizontalPosition = jQuery(horizontalAlign).val().replace('left', 'xMin').replace('center', 'xMid').replace('right', 'xMax');
			}

			if (verticalAlign && typeof(jQuery(verticalAlign).val()) != "undefined")
			{
				verticalPosition = jQuery(verticalAlign).val().replace('top', 'YMin').replace('middle', 'YMid').replace('bottom', 'YMax');
			}

			if (horizontalPosition == '')
			{
				horizontalPosition = 'xMin';
			}

			if (verticalPosition == '')
			{
				verticalPosition = 'YMin';
			}

			if (selectedClipart && typeof(jQuery(selectedClipart).val()) != "undefined")
			{
				var selectedClipartSVG = jQuery('[name=clipart' + areaId + '_' + jQuery(selectedClipart).val() + ']:first');
				if (selectedClipartSVG && typeof(selectedClipartSVG) != "undefined")
				{
					var svgDocument = document.getElementById(jQuery(selectedClipartSVG).attr('id'));
					if (svgDocument && typeof(svgDocument) != "undefined")
					{
						var svgDocumentContent = svgDocument.contentDocument;
						if (svgDocumentContent && typeof(svgDocumentContent) != "undefined")
						{
							var svgElementInner = jQuery(svgDocumentContent.documentElement).clone();
							if (typeof(svgElementInner) != "undefined")
							{
								jQuery(svgElementInner).attr("height", areasContainer[areaId]['height']);
								jQuery(svgElementInner).attr("width", areasContainer[areaId]['width']);
								jQuery(svgElementInner).attr("preserveAspectRatio", horizontalPosition + verticalPosition + " meet");

								svgElement.clear();
								svgElement.append(Snap.parse(svgElementInner[0].outerHTML));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Changes attributes of text element on SVG object
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

		var svgElement = rootSnapSvgObject.select("#areaBoxesLayer #areaSVGElement_" + areaId);

		if (svgElement)
		{
			if (font)
			{
				svgElement.attr('font-family', jQuery(font).find(':selected').text());
			}

			if (color && typeof(jQuery(color).val()) != "undefined")
			{
				svgElement.attr('fill', '#' + jQuery(color).val().replace('#',''));
			}

			if (fontSize && typeof(jQuery(fontSize).val()) != "undefined")
			{
				fontSize = jQuery(fontSize).val().split(":");

				if (fontSize.length > 1)
					fontSizeValue = fontSize[1];
				else
					fontSizeValue = fontSize[0];

				svgElement.attr('font-size', fontSizeValue + fontUnit);
				svgElement.attr('y', parseFloat(areasContainer[areaId]['y1']) + ((parseFloat(fontSizeValue) * scalingImageForPreviewRatio)));
			}

			if (textAlign && typeof(jQuery(textAlign).val()) != "undefined")
			{
				var textAlignValue = jQuery(textAlign).val();
				svgElement.attr('text-anchor', textAlignValue.replace('left', 'start').replace('center', 'middle').replace('right', 'end'));

				if (textAlignValue == 'left')
				{
					svgElement.attr('x', areasContainer[areaId]['x1']);
				}
				else if (textAlignValue == 'center')
				{
					svgElement.attr('x', parseFloat(areasContainer[areaId]['x1']) + (parseFloat(areasContainer[areaId]['width']) / 2));
				}
				else if (textAlignValue == 'right')
				{
					svgElement.attr('x', parseFloat(areasContainer[areaId]['x2']));
				}
			}

			if (verticalAlign && typeof(jQuery(verticalAlign).val()) != "undefined")
			{
				var verticalAlignValue = jQuery(verticalAlign).val();

				if (verticalAlignValue == 'top')
				{
					svgElement.attr('y', areasContainer[areaId]['y1']);
				}
				else if (verticalAlignValue == 'middle')
				{
					var yPos = parseFloat(areasContainer[areaId]['y1']) + (parseFloat(areasContainer[areaId]['height']) / 2);
					if (fontSizeValue)
						yPos -= (parseFloat(fontSizeValue) / 2);
					svgElement.attr('y', yPos);
				}
				else if (verticalAlignValue == 'bottom')
				{
					var yPos = parseFloat(areasContainer[areaId]['y1']) + (parseFloat(areasContainer[areaId]['height']));
					if (fontSizeValue)
						yPos -= fontSizeValue;
					svgElement.attr('y', yPos);
				}
			}

			if (jQuery(text).is("textarea") && typeof(jQuery(text).val()) != "undefined")
			{
				createSVGTextMultiline(svgElement, jQuery(text).val())
			}
			else if (typeof(jQuery(text).val()) != "undefined")
			{
				svgElement.node.textContent = jQuery(text).val();
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
		jQuery("#background-container").height(jQuery("#background").height());

		<?php foreach($this->displayedAreas as $area) :
			$areaType = ReddesignHelpersArea::getAreaType($area->areaType);
		?>

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
			areasContainer[<?php echo $area->id; ?>]['areaType'] = '<?php echo $areaType['name']; ?>';

			if ('<?php echo $areaType['name']; ?>' == 'text')
			{
				var svgElement = Snap.parse(
					'<text id="areaSVGElement_<?php echo $area->id; ?>" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ '></text>'
				);
			}
			else if ('<?php echo $areaType['name']; ?>' == 'clipart')
			{
				var svgElement = Snap.parse(
					'<svg id="areaSVGElement_<?php echo $area->id; ?>" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ ' width="' + width + '"'
						+ ' height="' + height + '"'
						+ '></svg>'
				);
			}

			rootSnapSvgObject.select("#areaBoxesLayer").append(svgElement);

			changeSVGElement(<?php echo $area->id; ?>);

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
		jQuery("#mainSvgImage").empty();
		jQuery("#areasContainer").empty();

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxLoadDesigntype",
			data: {'propertyId': propertyId},
			dataType: "text",
			type: "post",
			success: function (data)
			{
				jQuery("#areasContainer").html(data);
				thumbnailSVGLoader();
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
				loadBackgroundSVGandAreas(propertyId);
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
		jQuery('#svgContainer .progress').show().addClass('active');
		jQuery('#svgContainer .progress .bar-success').css('width', '0%');

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
								jQuery('#svgContainer .progress .bar-success')
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

						jQuery("#mainSvgImage")
							.append('<defs><style type="text/css">' +  background.selectedFontsDeclaration + '</style></defs>')
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

						customizeJS(areas);

						if (jQuery('.fontSizeSlider').length > 0)
						{
							jQuery('.fontSizeSlider').slider()
								.on('slide', function(ev){
									var id = jQuery(this).attr('id').replace('fontSizeSlider', '');
									jQuery('#fontSize' + id).val(ev.value);
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
		jQuery("#background-container").height(jQuery("#background").height());

		for (var i=0; i<areas.length; i++)
		{
			var x1 = parseFloat(areas[i].x1_pos) * scalingImageForPreviewRatio;
			var y1 = parseFloat(areas[i].y1_pos) * scalingImageForPreviewRatio;
			var x2 = parseFloat(areas[i].x2_pos) * scalingImageForPreviewRatio;
			var y2 = parseFloat(areas[i].y2_pos) * scalingImageForPreviewRatio;
			var width = x2 - x1;
			var height = y2 - y1;

			areasContainer[areas[i].id] = new Array();
			areasContainer[areas[i].id]['x1'] = x1;
			areasContainer[areas[i].id]['y1'] = y1;
			areasContainer[areas[i].id]['x2'] = x2;
			areasContainer[areas[i].id]['y2'] = y2;
			areasContainer[areas[i].id]['width'] = width;
			areasContainer[areas[i].id]['height'] = height;

			if (areas[i].areaType == '1') // text
			{
				var svgElement = Snap.parse(
					'<text id="areaSVGElement_'
						+ areas[i].id + '" '
						+ ' x="' + x1 + '"'
						+ ' y="' + y1 + '"'
						+ '></text>'
				);
			}
			else if (areas[i].areaType == '2') //clipart
			{
				var svgElement = Snap.parse(
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
		var url = '<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=designtype.ajaxUploadCustomClipart';

		var jQuerythis = jQuery('#uploadClipartFile' + areaId),
			buttonData = jQuerythis.data();

		jQuery('#clipartUpload' + areaId + ' .image-progress .bar').css('width','0%');

		if (jQuery.isEmptyObject(buttonData) == false) {
			jQuery('#clipartUpload' + areaId + ' .fileinput-button input').fileupload({
				url: url,
				dataType: 'text',
				autoUpload: false,
				cache: false,
				formData: dataVar,
				acceptFileTypes: /(\.|\/)(svg)$/i
			}).on('fileuploadprogressall', function (e, data) {
				jQuery(jQuerythis).prop('disabled', true);
				var progress = parseInt(data.loaded / data.total * 100, 10);
				jQuery('#clipartUpload' + areaId + ' .image-progress .bar').css(
					'width',
					progress + '%'
				);
			}).on('fileuploaddone', function (e, data) {
				if (data.result)
				{
					jQuery('#uploadedClipart' + areaId).html(data.result);
				}
				jQuery(jQuerythis).prop('disabled', false);
			}).on('fileuploadfail', function (e, data) {
				jQuery.each(data.files, function (index, file) {
					var error = jQuery('<span class="text-danger"/>');
					jQuery(data.context.children()[index])
						.append('<br>')
						.append(error);
				});
			}).prop('disabled', !jQuery.support.fileInput)
				.parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

			buttonData.submit();
		}
		else
		{
			alert('<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD_ERROR_SELECT_FILE', true); ?>');
		}
	}

	function addUploadButtonCall()
	{
		jQuery('.fileinput-button input').fileupload({
			autoUpload: false,
			acceptFileTypes: /(\.|\/)(svg)$/i
		}).on('fileuploadadd', function (e, data) {
			var id = (jQuery(this).attr('id').replace('uploadClipartFile', ''));
			data.context = jQuery('<div/>').appendTo('#uploadedClipart' + id);
			jQuery.each(data.files, function (index, file) {
				var node = jQuery('<p/>')
					.append(jQuery('<span/>').text(file.name));
				jQuery('#uploadClipartFile' + id).data(data);
				node.appendTo(data.context);
			});
		});
	}
</script>
{RedDesignBreakFormEndsAndJS}
