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
			charId: charSettings.charId,
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

		jQuery("#typography" + charSettings.charId).val(charSettings.typography);
	}

	function saveChar(update) {
		var charId = 0;
		var fontId = 0;
		var font_char;
		var width;
		var height;
		var typography;
		var typography_height;

		if(update != 0)
		{
			charId = update;
			fontId = <?php echo $this->item->id; ?>;
			font_char = jQuery("#font_char_" + update).val();
			width = jQuery("#width_" + update).val();
			height = jQuery("#height_" + update).val();
			typography = jQuery("#typography_" + update).val();
			typography_height = jQuery("#typography_height_" + update).val();
		}
		else
		{
			charId = "";
			fontId = <?php echo $this->item->id; ?>;
			font_char = jQuery("#font_char").val();
			width = jQuery("#width").val();
			height = jQuery("#height").val();
			typography = jQuery("#typography").val();
			typography_height = jQuery("#typography_height").val();
		}

		jQuery.ajax({ url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=char&task=ajaxSave&format=raw",
			data: {
				charId: charId,
				fontId: fontId,
				font_char: font_char,
				width: width,
				height: height,
				typography: typography,
				typography_height: typography_height
			},
			type: "post",
			success: function (data) {
				var json = jQuery.parseJSON(data);
				if(update == 0)
				{
					addRow(json);
				}
				jQuery("#ajax-message").removeClass();
				jQuery("#ajax-message").addClass("alert alert-success");
				jQuery("#ajax-message").html(json.message);
				jQuery("#ajax-message").fadeIn("slow");
				jQuery("#ajax-message").fadeOut(3000);
			},
			error: function (data) {
				jQuery("#ajax-message").removeClass();
				jQuery("#ajax-message").addClass("alert alert-error");
				jQuery("#ajax-message").html(data);
				jQuery("#ajax-message").fadeIn("slow");
				jQuery("#ajax-message").fadeOut(3000);
			}
		});

		jQuery("#font_char").val("");
		jQuery("#width").val("");
		jQuery("#height").val("");
		jQuery("#typography").val(0);
		jQuery("#typography_height").val("");
	}

	function removeChar(charId) {
		jQuery.ajax({ url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=char&task=ajaxRemove&format=raw",
			data: {
				charId: charId
			},
			type: "post",
			success: function (data) {
				jQuery("#row_" + charId).remove();
				jQuery("#ajax-message").removeClass();
				jQuery("#ajax-message").addClass("alert alert-success");
				jQuery("#ajax-message").html(data);
				jQuery("#ajax-message").fadeIn("slow");
				jQuery("#ajax-message").fadeOut(3000);
			},
			error: function (data) {
				jQuery("#ajax-message").removeClass();
				jQuery("#ajax-message").addClass("alert alert-error");
				jQuery("#ajax-message").html(data);
				jQuery("#ajax-message").fadeIn("slow");
				jQuery("#ajax-message").fadeOut(3000);
			}
		});
	}
</script>