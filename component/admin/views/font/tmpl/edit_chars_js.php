<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>


<script type="text/javascript">
	function addRow(charSettings) {
		// Form data for the Mustache template.
		var charRowData = {
			charId: charSettings.id,
			fontChar: charSettings.font_char,
			width: charSettings.width,
			height: charSettings.height,
			typographyHeight: charSettings.typography_height
		};

		// Render Mustache template for the chars row.
		var charRowTemplate = jQuery("#charsMustache").html();
		var charRowRendered = Mustache.render(charRowTemplate, charRowData);

		// Add rendered row template to the rows html element.
		jQuery("#other-rows").append(charRowRendered);

		jQuery("#typography" + charSettings.id).val(charSettings.typography);
	}

	function saveChar(update) {
		var charId = 0;
		var fontId = 0;
		var fontChar;
		var width;
		var height;
		var typography;
		var typographyHeight;

		if(update != 0)
		{
			charId = update;
			fontId = <?php echo $this->item->id; ?>;
			fontChar = jQuery("#fontChar" + update).val();
			width = jQuery("#width" + update).val();
			height = jQuery("#height" + update).val();
			typography = jQuery("#typography" + update).val();
			typographyHeight = jQuery("#typographyHeight" + update).val();
		}
		else
		{
			charId = "";
			fontId = <?php echo $this->item->id; ?>;
			fontChar = jQuery("#fontChar").val();
			width = jQuery("#width").val();
			height = jQuery("#height").val();
			typography = jQuery("#typography").val();
			typographyHeight = jQuery("#typographyHeight").val();
		}

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&font.ajaxSaveChar",
			data: {
				id: charId,
				font_id: fontId,
				font_char: fontChar,
				width: width,
				height: height,
				typography: typography,
				typography_height: typographyHeight
			},
			type: "post",
			beforeSend: function () {
				jQuery("#addButton").button("loading");
			},
			success: function (data) {
				var json = jQuery.parseJSON(data);
				if(update == 0)
				{
					addRow(json);
				}

				setTimeout(function () {jQuery("#addButton").button("reset")}, 500);
			},
			error: function (data) {

			}
		});

		jQuery("#fontChar").val("");
		jQuery("#width").val("");
		jQuery("#height").val("");
		jQuery("#typography").val(0);
		jQuery("#typographyHeight").val("");
	}

	function removeChar(charId) {
		jQuery.ajax({ url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=char&task=ajaxRemove&format=raw",
			data: {
				charId: charId
			},
			type: "post",
			success: function (data) {
				jQuery("#row" + charId).remove();
			},
			error: function (data) {

			}
		});
	}
</script>