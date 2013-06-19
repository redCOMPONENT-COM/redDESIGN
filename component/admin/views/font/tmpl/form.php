<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHTML::_('behavior.framework');

?>

<script type="text/javascript">
	function addRow(char_settings) {
		akeeba.jQuery('#other-rows').append(
			'<div id="start-row-' + char_settings.reddesign_font_char_id + '" class="row">' +
				'<div class="control-group character-group">' +
				'<label for="font_char"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>' +
				'<input class="input-mini" type="text" value="' + char_settings.font_char + '" maxlength="1" id="font-char-' + char_settings.reddesign_font_char_id + '" name="font_char">' +
				'</div>' +
				'<div class="control-group character-group"><label for="width">' +
				'<label for="width"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>' +
				'<input class="input-small" type="text" value="' + char_settings.width + '" maxlength="15" id="width-' + char_settings.reddesign_font_char_id + '" name="width">' +
				'</div>' +
				'<div class="control-group character-group"><label for="height">' +
				'<label for="height"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>' +
				'<input class="input-small" type="text" value="' + char_settings.height + '" maxlength="15" id="height-' + char_settings.reddesign_font_char_id + '" name="height">' +
				'</div>' +
				'<div class="control-group character-group">' +
				'<label for="typography"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>' +
				'<select id="typography-' + char_settings.reddesign_font_char_id + '" name="typography">' +
				'<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>' +
				'<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>' +
				'<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>' +
				'<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>' +
				'<option value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>' +
				'</select>' +
				'</div>' +
				'<div class="control-group character-group">' +
				'<label for="typography_height"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>' +
				'<input class="input-small" type="text" value="' + char_settings.typography_height + '" maxlength="15" id="typography_height-' + char_settings.reddesign_font_char_id + '" name="typography_height">' +
				'</div>' +
				'<div class="control-group character-group">' +
				'<label for="add-button">&nbsp;</label>' +
				'<a id="add-button-' + char_settings.reddesign_font_char_id + '" rel="" onclick="saveChar();" class="btn" title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">' +
				'<i class="icon-plus"></i>' +
				'<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>' +
				'</a>' +
				'</div>' +
				'</div>'
		);
		akeeba.jQuery('#typography-' + char_settings.reddesign_font_char_id).val(char_settings.typography);
	}

	function saveChar() {
		var reddesign_font_id = akeeba.jQuery('#reddesign_font_id').val();
		var font_char = akeeba.jQuery('#font_char').val();
		var width = akeeba.jQuery('#width').val();
		var height = akeeba.jQuery('#height').val();
		var typography = akeeba.jQuery('#typography').val();
		var typography_height = akeeba.jQuery('#typography_height').val();

		akeeba.jQuery.ajax({ url: '<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=font_char&task=ajax_save&format=raw',
			data: {
				reddesign_font_id: reddesign_font_id,
				font_char: font_char,
				width: width,
				height: height,
				typography: typography,
				typography_height: typography_height
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

		akeeba.jQuery('#reddesign_font_id').val("");
		akeeba.jQuery('#font_char').val("");
		akeeba.jQuery('#width').val("");
		akeeba.jQuery('#height').val("");
		akeeba.jQuery('#typography').val(0);
		akeeba.jQuery('#typography_height').val("");
	}


</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="font">
	<input type="hidden" name="task" value="">
	<input type="hidden" id="reddesign_font_id" name="reddesign_font_id"
	       value="<?php echo $this->item->reddesign_font_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1">

	<div id="basic_configuration" class="span12">
		<h3>
			<?php echo JText::_('COM_REDDESIGN_FONT_TITLE'); ?>
		</h3>

		<?php if (!empty($this->item->font_file) && !empty($this->item->font_thumb)) : ?>
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_FONT_THUMB_PREVIEW') ?>
				</label>

				<div class="controls">
					<img style="border: 1px black solid;"
					     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/fonts/') . $this->item->font_thumb; ?>">
				</div>
			</div>
		<?php else : ?>
			<div class="control-group">
				<label class="control-label " for="font_file">
					<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_FILE'); ?>
				</label>

				<div class="controls">
					<input type="file" name="font_file" id="font_file" class="inputbox"
					       value="<?php echo $this->item->font_file; ?>" required="required">
				</div>
			</div>
		<?php endif; ?>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->title; ?>" maxlength="255" size="32" id="title"
				       name="title">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_WIDTH'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_width; ?>" maxlength="10" size="32"
				       id="default_width" name="default_width" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_height; ?>" maxlength="10" size="32"
				       id="default_height" name="default_height" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_CAPS_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_caps_height; ?>" maxlength="10" size="32"
				       id="default_caps_height" name="default_caps_height" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_BASELINE_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_baseline_height; ?>" maxlength="10" size="32"
				       id="default_baseline_height" name="default_baseline_height" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('JSTATUS'); ?>
			</label>

			<div class="controls">
				<?php
				echo JHTML::_('select.booleanlist', 'enabled', 'class="inputbox"', $this->item->enabled, JText::_('JPUBLISHED'), JText::_('JUNPUBLISHED'));
				?>
			</div>
		</div>
	</div>
	<?php if (!empty($this->item->reddesign_font_id)) : ?>
		<div id="character-settings" class="span12">
			<h4><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS'); ?></h4>

			<p><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS_DESC'); ?></p>

			<div id="ajax-message-container" style="height: 25px; padding-bottom: 11px;">
				<div id="ajax-message" style="display: none;">
				</div>
			</div>

			<div id="all-rows">
				<div id="other-rows">

				</div>
				<div id="start-row" class="row">
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
						<label for="add-button">&nbsp;</label>
						<a id="add-button" rel="" onclick="saveChar();" class="btn"
						   title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">
							<i class="icon-plus"></i>
							<?php echo JText::_('COM_REDDESIGN_FONT_ADD_CHAR'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</form>