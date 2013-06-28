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
	function addRow(char_settings) {
		akeeba.jQuery("#other-rows").append(
			'<div id="row_' + char_settings.reddesign_char_id + '" class="row">' +
				'<div class="control-group character-group">' +
					'<label for="font_char_' + char_settings.reddesign_char_id + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>' +
					'<input class="input-mini" type="text" value="' + char_settings.font_char +
					'" maxlength="1" id="font_char_' + char_settings.reddesign_char_id + '" name="font_char_' + char_settings.reddesign_char_id + '">' +
				'</div>' +
				'<div class="control-group character-group">' +
					'<label for="width_' + char_settings.reddesign_char_id + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>' +
					'<input class="input-small" type="text" value="' + char_settings.width +
					'" maxlength="15" id="width_' + char_settings.reddesign_char_id + '" name="width_' + char_settings.reddesign_char_id + '">' +
				'</div>' +
				'<div class="control-group character-group">' +
					'<label for="height_' + char_settings.reddesign_char_id + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>' +
					'<input class="input-small" type="text" value="' + char_settings.height +
					'" maxlength="15" id="height_' + char_settings.reddesign_char_id + '" name="height_' + char_settings.reddesign_char_id + '">' +
				'</div>' +
				'<div class="control-group character-group">' +
					'<label for="typography_' + char_settings.reddesign_char_id + '"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>' +
					'<select id="typography_' + char_settings.reddesign_char_id + '" name="typography_' + char_settings.reddesign_char_id + '">' +
						'<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>' +
						'<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>' +
						'<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>' +
						'<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>' +
						'<option value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>' +
					'</select>' +
				'</div>' +
				'<div class="control-group character-group">' +
					'<label for="typography_height_' + char_settings.reddesign_char_id + '"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>' +
					'<input class="input-small" type="text" value="' + char_settings.typography_height + '" maxlength="15"' +
					'id="typography_height_' + char_settings.reddesign_char_id + '" name="typography_height_' + char_settings.reddesign_char_id + '">' +
				'</div>' +
				'<div class="control-group character-group">' +
					'<label for="add_button_' + char_settings.reddesign_char_id +'">&nbsp;</label>' +
					'<a id="add_button_' + char_settings.reddesign_char_id + '" rel="" ' +
						'class="btn btn-success"' +
						'onclick="saveChar(' + char_settings.reddesign_char_id + ');"' +
						'title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">' +
						'<i class="icon-plus icon-white"></i>' +
						'<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>' +
					'</a>' +
				'</div>' +
				'<div class="control-group character-group">' +
				'<label for="remove_button_' + char_settings.reddesign_char_id +'">&nbsp;</label>' +
				'<a id="remove_button_' + char_settings.reddesign_char_id + '" rel="" ' +
				' class="btn btn-danger"' +
				'onclick="removeChar(' + char_settings.reddesign_char_id + ');"' +
				'title="<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>">' +
				'<i class="icon-minus icon-white"></i>' +
				'<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>' +
				'</a>' +
				'</div>' +
			'</div>'
		);
		akeeba.jQuery("#typography-" + char_settings.reddesign_char_id).val(char_settings.typography);
	}

	function saveChar(update) {
		var reddesign_char_id = 0;
		var reddesign_font_id = 0;
		var font_char;
		var width;
		var height;
		var typography;
		var typography_height;

		if(update != 0)
		{
			reddesign_char_id = update;
			reddesign_font_id = <?php echo $this->item->reddesign_font_id; ?>;
			font_char = akeeba.jQuery("#font_char_" + update).val();
			width = akeeba.jQuery("#width_" + update).val();
			height = akeeba.jQuery("#height_" + update).val();
			typography = akeeba.jQuery("#typography_" + update).val();
			typography_height = akeeba.jQuery("#typography_height_" + update).val();
		}
		else
		{
			reddesign_char_id = "";
			reddesign_font_id = <?php echo $this->item->reddesign_font_id; ?>;
			font_char = akeeba.jQuery("#font_char").val();
			width = akeeba.jQuery("#width").val();
			height = akeeba.jQuery("#height").val();
			typography = akeeba.jQuery("#typography").val();
			typography_height = akeeba.jQuery("#typography_height").val();
		}

		akeeba.jQuery.ajax({ url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=char&task=ajaxSave&format=raw",
			data: {
				reddesign_char_id: reddesign_char_id,
				reddesign_font_id: reddesign_font_id,
				font_char: font_char,
				width: width,
				height: height,
				typography: typography,
				typography_height: typography_height
			},
			type: "post",
			success: function (data) {
				var json = akeeba.jQuery.parseJSON(data);
				if(update == 0)
				{
					addRow(json);
				}
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

		akeeba.jQuery("#reddesign_font_id").val("");
		akeeba.jQuery("#font_char").val("");
		akeeba.jQuery("#width").val("");
		akeeba.jQuery("#height").val("");
		akeeba.jQuery("#typography").val(0);
		akeeba.jQuery("#typography_height").val("");
	}

	function removeChar(reddesign_char_id) {
		akeeba.jQuery.ajax({ url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=char&task=ajaxRemove&format=raw",
			data: {
				reddesign_char_id: reddesign_char_id
			},
			type: "post",
			success: function (data) {
				akeeba.jQuery("#row_" + reddesign_char_id).remove();
				akeeba.jQuery("#ajax-message").removeClass();
				akeeba.jQuery("#ajax-message").addClass("alert alert-success");
				akeeba.jQuery("#ajax-message").html(data);
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
	}
</script>

<div id="character-settings" class="span12">
	<h4><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS'); ?></h4>

	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS_DESC'); ?></span>

	<div id="ajax-message-container" style="height: 25px; padding-bottom: 11px;">
		<div id="ajax-message" style="display: none;">
		</div>
	</div>

	<div id="all-rows">
		<div id="other-rows">
		<?php foreach($this->item->chars as $char_settings) : ?>
			<div id="row_<?php echo $char_settings->reddesign_char_id; ?>" class="row">
				<div class="control-group character-group">
					<label for="font_char_<?php echo $char_settings->reddesign_char_id; ?>">
						<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?>
					</label>
					<input class="input-mini" type="text" maxlength="1"
						   value="<?php echo $char_settings->font_char; ?>"
						   id="font_char_<?php echo $char_settings->reddesign_char_id; ?>"
						   name="font_char_<?php echo $char_settings->reddesign_char_id; ?>">
				</div>
				<div class="control-group character-group"><label for="width">
						<label for="width_<?php echo $char_settings->reddesign_char_id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?>
						</label>
						<input class="input-small" type="text" maxlength="15"
							   value="<?php echo $char_settings->width; ?>"
							   id="width_<?php echo $char_settings->reddesign_char_id; ?>"
							   name="width_<?php echo $char_settings->reddesign_char_id; ?>">
				</div>
				<div class="control-group character-group"><label for="height">
						<label for="height_<?php echo $char_settings->reddesign_char_id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?>
						</label>
						<input class="input-small" type="text" maxlength="15"
							   value="<?php echo $char_settings->height; ?>"
							   id="height_<?php echo $char_settings->reddesign_char_id; ?>"
							   name="height_<?php echo $char_settings->reddesign_char_id; ?>">
				</div>
				<div class="control-group character-group">
					<label for="typography_<?php echo $char_settings->reddesign_char_id; ?>"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
					<select id="typography_<?php echo $char_settings->reddesign_char_id; ?>" name="typography">
						<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>
						<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>
						<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>
						<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>
						<option value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>
					</select>
					<script type="text/javascript">
						akeeba.jQuery('#typography_<?php echo $char_settings->reddesign_char_id; ?>')
						.val(<?php echo $char_settings->typography; ?>);
					</script>
				</div>
				<div class="control-group character-group">
					<label for="typography_height_<?php echo $char_settings->reddesign_char_id; ?>">
						<?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?>
					</label>
					<input class="input-small" type="text" maxlength="15"
						   value="<?php echo $char_settings->typography_height; ?>"
						   id="typography_height_<?php echo $char_settings->reddesign_char_id; ?>"
						   name="typography_height_<?php echo $char_settings->reddesign_char_id; ?>">
				</div>
				<div class="control-group character-group">
					<label for="add_button_<?php echo $char_settings->reddesign_char_id; ?>">&nbsp;</label>
					<a id="add_button_<?php echo $char_settings->reddesign_char_id; ?>" rel=""
					   class="btn btn-success"
					   onclick="saveChar(<?php echo $char_settings->reddesign_char_id; ?>);"
					   title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">
						<i class="icon-plus icon-white"></i>
						<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>
					</a>
				</div>
				<div class="control-group character-group">
					<label for="remove_button_<?php echo $char_settings->reddesign_char_id; ?>">&nbsp;</label>
					<a id="remove_button_<?php echo $char_settings->reddesign_char_id; ?>" rel=""
					   class="btn btn-danger"
					   onclick="removeChar(<?php echo $char_settings->reddesign_char_id; ?>);"
					   title="<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>">
						<i class="icon-minus icon-white"></i>
						<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
		<div id="start_row" class="row">
			<div class="control-group character-group">
				<label for="font_char"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>
				<input class="input-mini" type="text" value="" maxlength="1" id="font_char" name="font_char">
			</div>
			<div class="control-group character-group"><label for="width">
					<label for="width"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>
					<input class="input-small" type="text" value="" maxlength="15" id="width" name="width">
			</div>
			<div class="control-group character-group"><label for="height">
					<label for="height"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>
					<input class="input-small" type="text" value="" maxlength="15" id="height" name="height">
			</div>
			<div class="control-group character-group">
				<label for="typography"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
				<select id="typography" name="typography">
					<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>
					<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>
					<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>
					<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>
					<option
						value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>
				</select>
			</div>
			<div class="control-group character-group">
				<label
					for="typography_height"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>
				<input class="input-small" type="text" value="" maxlength="15" id="typography_height"
					   name="typography_height">
			</div>
			<div class="control-group character-group">
				<label for="add_button">&nbsp;</label>
				<a id="add_button" rel=""
				   class="btn btn-success"
				   onclick="saveChar(0);"
				   title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">
					<i class="icon-plus icon-white"></i>
					<?php echo JText::_('COM_REDDESIGN_FONT_ADD_CHAR'); ?>
				</a>
			</div>
		</div>
	</div>
</div>