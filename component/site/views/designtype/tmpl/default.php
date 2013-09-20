<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHTML::_('behavior.modal');
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/accounting.min.js');

?>

<h1><?php echo $this->item->title; ?></h1>
<form id="designform" name="designform" method="post" action="index.php" class="row-fluid">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" id="task" value="">
	<input type="hidden" name="designAreas" id="designAreas" value="">
	<input type="hidden" id="reddesign_designtype_id" name="reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1">

	<?php echo $this->loadTemplate('product'); ?>

	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#customize" id="customizeLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_CUSTOMIZE_TAB'); ?></a>
		</li>
		<?php if ($this->accessorytypes) : ?>
		<li>
			<a href="#accessories" id="accessoriesLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_ACCESSORIES_TAB'); ?></a>
		</li>
		<?php endif; ?>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="customize">
			<div class="row-fluid">
				<div class="row-fluid">
					<div class="well span12">
						<?php echo $this->loadTemplate('frames'); ?>
					</div>
				</div>
				<div id="background-container" class="row-fluid">
					<div id="backgroundImage">
						<img id="background"
							 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->previewBackground->image_path; ?>"
							 alt="<?php echo $this->previewBackground->title;?>" />
					</div>
					<div id="progressBar"
						 style="display: none;">
						<div class="progress progress-striped active">
							<div class="bar" style="width: 100%;"></div>
						</div>
					</div>
				</div>
				<div class="customize-it-btn row-fluid">
					<?php if (!empty($this->productionBackgroundAreas) && ($this->params->get('autoCustomize', 1) == 0 || $this->params->get('autoCustomize', 1) == 2) ) : ?>
						<button type="button" class="btn btn-success" data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE_LOADING') ?>" id="customizeDesign">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE'); ?>
						</button>
					<?php endif; ?>
				</div>
				<div class="row-fluid">
					<div class="well span12">
						<?php echo $this->loadTemplate('areas'); ?>
						<input type="hidden" id="autoSizeData" name="autoSizeData" value="" />
					</div>
				</div>
			</div>
		</div>
		<?php if ($this->accessorytypes) : ?>
		<div class="tab-pane" id="accessories">
			<?php echo $this->loadTemplate('accessories'); ?>
		</div>
		<?php endif; ?>
	</div>
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
			akeeba.jQuery("#frame<?php echo $this->previewBackground->reddesign_background_id; ?>").attr("checked", "checked");

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

			// Build Areas colors.
			<?php foreach ($this->productionBackgroundAreas as  $area) : ?>
				var reddesign_area_id = parseInt(<?php echo $area->reddesign_area_id;?>);
				akeeba.jQuery("#color-selector" + reddesign_area_id).ColorPicker({
					designId:reddesign_area_id,
					color: "#000000",
					onChange: function (hsb, hex, rgb, reddesign_area_id) {
						akeeba.jQuery("#color-selector" +reddesign_area_id+ " div").css("backgroundColor", "#" + hex);
						document.getElementById("colorCode"+reddesign_area_id).value = hex; // Edited
					}
				});

				// Setup before functions.
				var typingTimer;
				var doneTypingInterval = 400;

				<?php if ($this->item->fontsizer == "slider") : ?>
					akeeba.jQuery("#fontSize" + reddesign_area_id).slider()
						.on("slide", function(ev){
							clearTimeout(typingTimer);
				    			typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);
						});
				<?php endif; ?>

				// Onkeyup, start the countdown.
				akeeba.jQuery("#textArea"+reddesign_area_id).keyup(function(){
				    clearTimeout(typingTimer);
				    typingTimer = setTimeout(function() { customize(0); }, doneTypingInterval);

				});
			<?php endforeach; ?>

			// Define price settings.
			accounting.settings = {
				currency: {
					symbol : "<?php echo $this->params->get('currency_symbol', '$'); ?>",
					format: "<?php echo $this->params->get('currency_symbol_position_before', '1') ? '%s %v' : '%v %s'; ?>",
					decimal : "<?php echo $this->params->get('currency_decimal_separator', '.'); ?>",
					thousand: "<?php echo $this->params->get('currency_thousand_separator', ','); ?>",
					precision : <?php echo $this->params->get('decimals', '2'); ?>
				},
				number: {
					precision : <?php echo $this->params->get('decimals', '2'); ?>,
					thousand: "<?php echo $this->params->get('currency_thousand_separator', ','); ?>",
					decimal : "<?php echo $this->params->get('currency_decimal_separator', '.'); ?>"
				}
			}

			// Calculate default price.
			var total = 0;
			var formatedTotal;

			akeeba.jQuery(".price-modifier:checked").each(function () {
				total += parseFloat(akeeba.jQuery(this).attr("data-price"));
			});
			formatedTotal = accounting.formatMoney(total);
			akeeba.jQuery("#total").html(formatedTotal);

			// onClick calculate current product price adding all price modifiers: frames and accessories.
			akeeba.jQuery(document).on("click", ".price-modifier", function () {
					var total = 0;
					var formatedTotal = 0;
					akeeba.jQuery(".price-modifier:checked").each(function () {
						total += parseFloat(akeeba.jQuery(this).attr("data-price"));
					});
					formatedTotal = accounting.formatMoney(total);
					akeeba.jQuery("#total").html(formatedTotal);
				}
			);
		}
	);

	/**
	 * Sends customize data to server and retreives the resulting image.
	 *
	 * @param button Determines whether the call comes from "Customize it!" button or not.
	 */
	function customize(button) {

		var customizeOrNot = 0;
		var autoCustomizeParam = <?php echo $this->params->get('autoCustomize', 1); ?>;

		<?php
		/*
		 * 0 when customize function is called from an element different than button (textbox, font dropdown etc.)
		 * 1 when customize function is called from the button
		 * 3 when customize function is called from frames selection radio button
		 */
		?>

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

			design.areas.push({
				"id" : 			"<?php echo $area->reddesign_area_id; ?>",
				"textArea" :	akeeba.jQuery("#textArea<?php echo $area->reddesign_area_id; ?>").val(),
				"fontArea" : 	akeeba.jQuery("#fontArea<?php echo $area->reddesign_area_id; ?>").val(),
				"fontColor" :	akeeba.jQuery("#colorCode<?php echo $area->reddesign_area_id; ?>").val(),
				"fontSize" :	akeeba.jQuery("#fontSize<?php echo $area->reddesign_area_id; ?>").val(),
				"fontTypeId" :	akeeba.jQuery("#fontArea<?php echo $area->reddesign_area_id; ?>").val()
			});
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
						akeeba.jQuery("#autoSizeData").val(JSON.stringify(json.autoSizeData));
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
