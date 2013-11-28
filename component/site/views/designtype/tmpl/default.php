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

/*
{RedDesignBreakELEMENT} is a tag used in integration plugin to explode HTML string into smaller peaces. Those peaces are used in redSHOP templating.
*/
$input         = JFactory::getApplication()->input;
$productId     = $input->getInt('pid', 0);
?>

<?php // Part 0 - Title ?>
{RedDesignBreakTitle}
<h1><?php echo $this->item->title; ?></h1>
{RedDesignBreakTitle}

<?php // Part 1 - Begin Form ?>
{RedDesignBreakFormBegin}
<form id="designform" name="designform" method="post" action="index.php" class="row-fluid">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" id="task" value="">
	<input type="hidden" name="designAreas" id="designAreas" value="">
	<input type="hidden" id="autoSizeData" name="autoSizeData" value="" />
	<input type="hidden" id="reddesign_designtype_id" name="reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>">
{RedDesignBreakFormBegin}

<?php // Part 2 - Select Backgrounds ?>
{RedDesignBreakBackgrounds}
	<div class="row-fluid">
		<div class="well span12">
			<?php echo $this->loadTemplate('backgrounds'); ?>
		</div>
	</div>
{RedDesignBreakBackgrounds}

<?php // Part 3 - Design Image ?>
{RedDesignBreakDesignImage}
	<div id="background-container">
		<div id="backgroundImage">
			<img id="background"
				 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->defaultPreviewBg->image_path; ?>"
				 alt="<?php echo $this->defaultPreviewBg->title;?>" />
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
		<?php if (!empty($this->productionBackgroundAreas) && ($this->params->get('autoCustomize', 1) == 0 || $this->params->get('autoCustomize', 1) == 2) ) : ?>
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
			<?php echo $this->loadTemplate('areas'); ?>
		</div>
	</div>
{RedDesignBreakDesignAreas}

<?php // Part 6 - Form Ends ?>
{RedDesignBreakFormEndsAndJS}
</form>

<script type="text/javascript">
	var backgroundContainerWidth;
	var backgroundContainerHeight;

	/**
	 * Add click event to Customize button.
	 */
	akeeba.jQuery(document).ready(
		function () {
			// Correct radio button selection.
			akeeba.jQuery("#frame<?php echo $this->defaultPreviewBg->reddesign_background_id; ?>").attr("checked", "checked");

			// Customize function.
			akeeba.jQuery(document).on("click", "#customizeDesign",
				function () {
					// Add spinner to button.
					akeeba.jQuery(this).button("loadingo");
					setTimeout(
						function() {
							akeeba.jQuery(this).button("reset");
						},
						3000
					);

					customize(1);
				}
			);

			<?php foreach ($this->productionBackgroundAreas as  $area) : ?>

				// Set up default text at images.
				<?php if (!empty($area->default_text)) : ?>
					customize(3);
				<?php endif; ?>

				// Delay AJAX submit because of typing.
				var typingTimer;
				var doneTypingInterval = 400;

				<?php if ($this->item->fontsizer == "slider") : ?>
					akeeba.jQuery("#fontSize<?php echo $area->reddesign_area_id ?>").slider()
						.on("slide", function(ev){
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
						});
				<?php endif; ?>

				// Onkeyup, start the countdown.
				akeeba.jQuery("#textArea<?php echo $area->reddesign_area_id; ?>").keyup(function(){
					clearTimeout(typingTimer);
					typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
				});

				<?php if ($area->color_code == 1 || empty($area->color_code)) : ?>
					var colorPicker<?php echo $area->reddesign_area_id ?> = akeeba.jQuery.farbtastic("#colorPickerContainer<?php echo $area->reddesign_area_id ?>");
					colorPicker<?php echo $area->reddesign_area_id ?>.linkTo("#colorCode<?php echo $area->reddesign_area_id; ?>");

					akeeba.jQuery(document).on("keyup", "#C<?php echo $area->reddesign_area_id; ?>", function() {
						var newColor = getNewHexColor(parseInt("<?php echo $area->reddesign_area_id; ?>"));
						colorPicker<?php echo $area->reddesign_area_id ?>.setColor(newColor);
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
					});

					akeeba.jQuery(document).on("keyup", "#M<?php echo $area->reddesign_area_id; ?>", function() {
						var newColor = getNewHexColor(parseInt("<?php echo $area->reddesign_area_id; ?>"));
						colorPicker<?php echo $area->reddesign_area_id ?>.setColor(newColor);
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
					});

					akeeba.jQuery(document).on("keyup", "#Y<?php echo $area->reddesign_area_id; ?>", function() {
						var newColor = getNewHexColor(parseInt("<?php echo $area->reddesign_area_id; ?>"));
						colorPicker<?php echo $area->reddesign_area_id ?>.setColor(newColor);
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
					});

					akeeba.jQuery(document).on("keyup", "#K<?php echo $area->reddesign_area_id; ?>", function() {
						var newColor = getNewHexColor(parseInt("<?php echo $area->reddesign_area_id; ?>"));
						colorPicker<?php echo $area->reddesign_area_id ?>.setColor(newColor);
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
					});

					akeeba.jQuery(document).on("keyup", "#colorCode<?php echo $area->reddesign_area_id; ?>", function() {
						var hex = akeeba.jQuery("#colorCode<?php echo $area->reddesign_area_id; ?>").val();
						loadCMYKValues(hex, parseInt("<?php echo $area->reddesign_area_id; ?>"));
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
					});

					akeeba.jQuery(document).on("mouseup", "#colorPickerContainer<?php echo $area->reddesign_area_id; ?>", function() {
						var hex = akeeba.jQuery("#colorCode<?php echo $area->reddesign_area_id; ?>").val();
						loadCMYKValues(hex, parseInt("<?php echo $area->reddesign_area_id; ?>"));
						customize(0);
					});
				<?php endif; ?>
			<?php endforeach; ?>

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

		akeeba.jQuery("#C" + areaId).val(cmyk.c);
		akeeba.jQuery("#M" + areaId).val(cmyk.m);
		akeeba.jQuery("#Y" + areaId).val(cmyk.y);
		akeeba.jQuery("#K" + areaId).val(cmyk.k);
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
		var c = akeeba.jQuery("#C" + areaId).val();
		var m = akeeba.jQuery("#M" + areaId).val();
		var y = akeeba.jQuery("#Y" + areaId).val();
		var k = akeeba.jQuery("#K" + areaId).val();

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
		var autoCustomizeParam = <?php echo $this->params->get('autoCustomize', 1); ?>;

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
			var halfBackgroundHeight =  ((akeeba.jQuery("#background").height() / 2)-10);
			akeeba.jQuery("#background-container").height(akeeba.jQuery("#background").height());
			akeeba.jQuery("#progressBar").css("padding-top", halfBackgroundHeight + "px");
			akeeba.jQuery("#progressBar").css("padding-bottom", halfBackgroundHeight + "px");
			akeeba.jQuery("#backgroundImage").html("");
			akeeba.jQuery("#progressBar").show();


			var reddesign_designtype_id = akeeba.jQuery("#reddesign_designtype_id").val();
			var reddesign_background_id = akeeba.jQuery("#reddesign_background_id").val();
			var design = {
				areas: [],
				reddesign_designtype_id : reddesign_designtype_id,
				reddesign_background_id : reddesign_background_id
			};
			<?php foreach($this->productionBackgroundAreas as $area) : ?>

			var fontColor = akeeba.jQuery("#colorCode<?php echo $area->reddesign_area_id; ?>").val();
			fontColor = fontColor.replace("#", "");

			design.areas.push({
				"id" : 			"<?php echo $area->reddesign_area_id; ?>",
				"textArea" :	akeeba.jQuery("#textArea<?php echo $area->reddesign_area_id; ?>").val(),
				"fontArea" : 	akeeba.jQuery("#fontArea<?php echo $area->reddesign_area_id; ?>").val(),
				"fontColor" :	fontColor,
				"fontSize" :	akeeba.jQuery("#fontSize<?php echo $area->reddesign_area_id; ?>").val(),
				"fontTypeId" :	akeeba.jQuery("#fontArea<?php echo $area->reddesign_area_id; ?>").val(),
				"plg_dimention_base" :   akeeba.jQuery("#plg_dimention_base_<?php echo $productId;?>").val(),
				"plg_dimention_base_input" :   akeeba.jQuery("#plg_dimention_base_input_<?php echo $productId;?>").val()
			});

			var textareacount = akeeba.jQuery("#textArea<?php echo $area->reddesign_area_id; ?>").val().replace(/ /g,'').length;
			akeeba.jQuery("#rs_sticker_element_<?php echo $productId; ?>").html(textareacount);

			<?php endforeach; ?>

			design = JSON.stringify({Design: design });
			akeeba.jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=designtype&task=ajaxGetDesign&format=raw&<?php echo JFactory::getSession()->getFormToken(); ?>=1",
				data: { designarea : design },
				type: "post",
				success: function(data) {
					if (data == "Invalid Token")
					{
						alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_INVALID_TOKEN'); ?>");
					}
					else
					{
						var json = akeeba.jQuery.parseJSON(data);

						d = new Date();
						akeeba.jQuery("#backgroundImage").html('<img alt="' + json.imageTitle + '" src="' + json.image + '?' + d.getTime() + '" id="background" />');

						// Remove the progress bar
						akeeba.jQuery("#progressBar").hide();
						akeeba.jQuery("#background").show();
						// This timeout returns back to fluid design after 10 seconds in case user changes browser witdh
						setTimeout(function() {
							akeeba.jQuery("#background-container").height("auto");
						}, 5000);

						<?php if ($this->item->fontsizer == 'auto' || $this->item->fontsizer == 'auto_chars') : ?>
							akeeba.jQuery("#plg_dimention_base_input_<?php echo $productId;?>").attr({
								'default-height' : json.autoSizeData[0].canvasHeight,
								'default-width': json.autoSizeData[0].canvasWidth
							});

							akeeba.jQuery("#autoSizeData").val(JSON.stringify(json.autoSizeData));
						<?php endif; ?>
					}
				},
				error: function(errMsg) {
					alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AJAX_ERROR'); ?> " + errMsg);
				}
			});
		}
	}

	/**
	 * Set selected color for designarea.
	 *
	 * @param reddesign_area_id
	 * @param colorCode
	 */
	function setColorCode(reddesign_area_id, colorCode)
	{
		document.getElementById("colorCode" + reddesign_area_id).value = colorCode;
		akeeba.jQuery("#fontColor" + reddesign_area_id+ " div").css("backgroundColor", "#" + colorCode);
		akeeba.jQuery("#fontColor" + reddesign_area_id).show();

		customize(0);
	}

	/**
	 * Set selected background for designarea.
	 *
	 * @param reddesign_background_id
	 */
	function setBackground(reddesign_background_id)
	{
		document.getElementById("reddesign_background_id").value = reddesign_background_id;

		customize(3);
	}
</script>
{RedDesignBreakFormEndsAndJS}
