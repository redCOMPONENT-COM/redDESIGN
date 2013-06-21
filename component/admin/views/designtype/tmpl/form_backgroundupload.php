<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal');

?>

<div id="basic_configuration">
	<h3><?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?></h3>
	<div class="control-group">
		<label class="control-label ">
			<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE'); ?>
		</label>
		<div class="controls">
			<input type="text" name="title" id="bg_title" value="">
			<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label " for="bg_eps_file">
			<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_FILE'); ?>
		</label>
		<div class="controls">
			<input type="file" name="eps_file" id="bg_eps_file" value="">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label todo-label" for="bg_isPDFbgimage">
			<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE'); ?>
		</label>
		<div class="controls">
			<select name="bg_isPDFbgimage" id="bg_isPDFbgimage">
				<option value="0" selected="selected"><?php echo JText::_('JNO'); ?></option>
				<option value="1"><?php echo JText::_('JYES'); ?></option>
			</select>
			<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE_DESC'); ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label todo-label" for="bg_status">
			<?php echo JText::_('JPUBLISHED'); ?>
		</label>
		<div class="controls">
			<select name="bg_status" id="bg_status">
				<option value="1" selected="selected"><?php echo JText::_('JYES'); ?></option>
				<option value="0"><?php echo JText::_('JNO'); ?></option>
			</select>
			<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
		</div>
	</div>
</div>
<div class="form-actions">
	<input type="button" class="btn btn-primary" id="saveBkBtn" value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDUPLOAD_UPLOAD'); ?>" />
	<input type="button" class="btn" id="cancelBkBtn" value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDUPLOAD_CANCEL'); ?>" />
</div>

<div id="ajax-message-container" style="height: 25px; padding-bottom: 11px;">
	<div id="ajax-message" style="display: none;">
	</div>
</div>

<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function()
		{
			akeeba.jQuery(document).on('click', '#saveBkBtn', function()
				{
					ajaxSaveBackground()
				}
			);
			akeeba.jQuery(document).on('click', '#cancelBkBtn', function()
				{
					akeeba.jQuery('#uploadBkForm').fadeOut("slow");
					akeeba.jQuery('#addBkBtn').parent().show();
				}
			);
		});

	function ajaxSaveBackground() {
		var bg_reddesign_designtype_id = akeeba.jQuery('#reddesign_designtype_id').val();
		var bg_title 					= akeeba.jQuery('#bg_title').val();
		var bg_isPDFbgimage 			= akeeba.jQuery('#bg_isPDFbgimage').val();
		var bg_status 			= akeeba.jQuery('#bg_status').val();

		akeeba.jQuery.ajax({ url: '<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=background&task=ajaxSave&format=raw',
			data: {
				reddesign_designtype_id: bg_reddesign_designtype_id,
				title: bg_title,
				isPDFbgimage: bg_isPDFbgimage,
				status: bg_status
			},
			type: 'post',
			success: function (data) {
				var json = akeeba.jQuery.parseJSON(data);
				addRow(json);
				akeeba.jQuery("#ajax-message").removeClass();
				akeeba.jQuery("#ajax-message").addClass("alert alert-success");
				akeeba.jQuery("#ajax-message").html(json.message);
				akeeba.jQuery("#ajax-message").fadeIn("slow");
				akeeba.jQuery("#ajax-message").fadeOut(3000);
			},
			error: function (data) {
				akeeba.jQuery("#ajax-message").removeClass();
				akeeba.jQuery("#ajax-message").addClass("alert alert-error");
				akeeba.jQuery("#ajax-message").html(data);
				akeeba.jQuery("#ajax-message").fadeIn("slow");
				akeeba.jQuery("#ajax-message").fadeOut(3000);
			}
		});

		akeeba.jQuery('#title').val("");
	}
</script>

