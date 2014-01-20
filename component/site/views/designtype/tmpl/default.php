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

RHelperAsset::load('lib/jquery.min.js', 'redcore');
RHelperAsset::load('snap.svg-min.js', 'com_reddesign');

if (isset($displayData))
{
	$this->item = $displayData->item;
	$this->defaultPreviewBg = $displayData->defaultPreviewBg;
	$this->productionBackground = $displayData->productionBackground;
	$this->productionBackgroundAreas = $displayData->productionBackgroundAreas;
	$this->defaultPreviewBgAttributes = $displayData->defaultPreviewBgAttributes;
	$this->fonts = $displayData->fonts;
	$this->config = $displayData->config;
}

$imageUrl = JURI::base() . 'media/com_reddesign/backgrounds/' . $this->defaultPreviewBg->svg_file;
$autoCustomize = $this->config->getAutoCustomize();
$previewWidth = $this->config->getMaxSVGPreviewSiteWidth();
$unit = $this->config->getUnit();
$sourceDpi = $this->config->getSourceDpi();

$selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($this->fonts);

// Calculate width and height in the selected unit at the configuration. 1 inch = 25.4 mm
switch ($this->unit)
{
	case 'mm':
		$unitConversionRatio = $sourceDpi / 25.4;
		break;
	case 'cm':
		$unitConversionRatio = $sourceDpi / 2.54;
		break;
	case 'px':
	default:
		$unitConversionRatio = '1';
		break;
}

/*
{RedDesignBreakELEMENT} is a tag used in integration plugin to explode HTML string into smaller peaces. Those peaces are used in redSHOP templating.
*/
$input = JFactory::getApplication()->input;
$productId = $input->getInt('pid', 0);
?>

<?php // Part 0 - Title ?>
{RedDesignBreakTitle}
<h1><?php echo $this->item->name; ?></h1>
{RedDesignBreakTitle}

<?php // Part 1 - Begin Form ?>
{RedDesignBreakFormBegin}
<form id="designform" name="designform" method="post" action="index.php" class="row-fluid">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" id="task" value="">
	<input type="hidden" name="designAreas" id="designAreas" value="">
	<input type="hidden" id="autoSizeData" name="autoSizeData" value="" />
	<input type="hidden" id="reddesign_designtype_id" name="reddesign_designtype_id" value="<?php echo $this->item->id; ?>">
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
		</div>

		<div id="progressBar" style="display: none;">
			<div class="progress progress-striped active">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>

	</div>
{RedDesignBreakDesignImage}

<?php // Part 4 - "Customize it!" Button (Controlled by configuration parameters.) ?>
{RedDesignBreakButtonCustomizeIt}
	<div class="customize-it-btn row-fluid">
		<?php if (!empty($this->productionBackgroundAreas) && ($autoCustomize == 0 || $autoCustomize == 2) ) : ?>
			<button type="button"
					class="btn btn-success"
					data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE_LOADING') ?>"
					id="customizeDesign"
				>
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE'); ?>
			</button>
		<?php endif; ?>
	</div>
{RedDesignBreakButtonCustomizeIt}


<?php // Part 5 - Areas Begin ?>
{RedDesignBreakDesignAreas}
	<div class="row-fluid">
		<div class="well span12">
			<?php echo RLayoutHelper::render('default_areas', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl'); ?>
		</div>
	</div>
{RedDesignBreakDesignAreas}

<?php // Part 6 - Form Ends ?>
{RedDesignBreakFormEndsAndJS}
</form>

<script type="text/javascript">
	var rootSnapSvgObject;

	/**
	 * Initiate PX to Unit conversation variables
	 */
	var unit = "<?php echo $unit;?>";
	var imageWidth  = parseFloat("<?php echo (!empty($this->defaultPreviewBgAttributes->width) ? $this->defaultPreviewBgAttributes->width : ''); ?>");
	var imageHeight = parseFloat("<?php echo (!empty($this->defaultPreviewBgAttributes->height) ? $this->defaultPreviewBgAttributes->height : ''); ?>");
	var previewWidth  = parseFloat("<?php echo $previewWidth; ?>");

	var unitConversionRatio = parseFloat("<?php echo $unitConversionRatio;?>");
	var scalingImageForPreviewRatio = previewWidth / imageWidth;
	var previewHeight = imageHeight * scalingImageForPreviewRatio;

	/**
	 * Add click event to Customize button.
	 */
	jQuery(document).ready(function () {
			<?php if (!empty($this->defaultPreviewBg->svg_file)) : ?>
				rootSnapSvgObject = Snap("#mainSvgImage");

				Snap.load(
					"<?php echo $imageUrl; ?>",
					function (f) {
						var styleDeclaration = Snap.parse('<defs><style type="text/css"><?php echo $selectedFontsDeclaration; ?></style></defs>');
						rootSnapSvgObject.append(styleDeclaration);
						rootSnapSvgObject.append(f);

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
					}
				);
			<?php endif; ?>

			// Correct radio button selection.
			jQuery("#frame<?php echo $this->defaultPreviewBg->id; ?>").attr("checked", "checked");

			// Customize function.
			jQuery(document).on("click", "#customizeDesign", function () {
					// Add spinner to button.
					jQuery(this).button("loading");
					setTimeout(function() {
							jQuery(this).button("reset");
						},
						3000
					);
					customize(1);
			});



			jQuery(document).on("keyup", ".colorPickerSelectedColor", function() {
				var id = jQuery(this).attr('id').replace('colorCode','');
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
	 * @param button Determines whether the call comes from "Customize it!" button or not.
	 */
	function customize(button) {

		var customizeOrNot = 0;
		var autoCustomizeParam = parseInt("<?php echo $autoCustomize; ?>");
		/**
		 * 0 when customize function is called from an element different than button (textbox, font dropdown etc.)
		 * 1 when customize function is called from the button
		 * 3 when customize function is called from frames selection radio button
		 */

		// Turn off or on customization according to the settings in config.xml.
		if((button == 1 && autoCustomizeParam == 0) || (button == 1 && autoCustomizeParam == 2))
		{
			customizeOrNot = 1;
		}
		else if(button == 0 && (autoCustomizeParam == 1 || autoCustomizeParam == 2))
		{
			customizeOrNot = 1;
		}
		else if(button == 3)
		{
			// This is the case when setBackground function is called
			customizeOrNot = 1;
		}

		if(customizeOrNot == 1)
		{
			// Add the progress bar
			var halfBackgroundHeight =  ((jQuery("#background").height() / 2)-10);
			jQuery("#background-container").height(jQuery("#background").height());
			jQuery("#progressBar").css("padding-top", halfBackgroundHeight + "px").css("padding-bottom", halfBackgroundHeight + "px").show();


			var reddesign_designtype_id = jQuery("#reddesign_designtype_id").val();
			var background_id = jQuery("#background_id").val();
			var design = {
				areas: [],
				reddesign_designtype_id : reddesign_designtype_id,
				background_id : background_id
			};
			<?php foreach($this->productionBackgroundAreas as $area) : ?>

			var fontColor = jQuery("#colorCode<?php echo $area->id; ?>").val();
			fontColor = fontColor.replace("#", "");

			design.areas.push({
				"id" : 			"<?php echo $area->id; ?>",
				"textArea" :	jQuery("#textArea<?php echo $area->id; ?>").val(),
				"fontArea" : 	jQuery("#fontArea<?php echo $area->id; ?>").val(),
				"fontColor" :	fontColor,
				"fontSize" :	jQuery("#fontSize<?php echo $area->id; ?>").val(),
				"fontTypeId" :	jQuery("#fontArea<?php echo $area->id; ?>").val(),
				"plg_dimension_base" :   jQuery("#plg_dimension_base_<?php echo $productId;?>").val(),
				"plg_dimension_base_input" :   jQuery("#plg_dimension_base_input_<?php echo $productId;?>").val()
			});

			var textareacount = jQuery("#textArea<?php echo $area->id; ?>").val().replace(/ /g,'').length;
			jQuery("#rs_sticker_element_<?php echo $productId; ?>").html(textareacount);

			<?php endforeach; ?>
		}
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
		svgLoad();
		//customize(0);
	}

	/**
	 * Set selected background for designarea.
	 *
	 * @param background_id
	 */
	function setBackground(background_id)
	{
		document.getElementById("background_id").value = background_id;

		customize(3);
	}
</script>
{RedDesignBreakFormEndsAndJS}
