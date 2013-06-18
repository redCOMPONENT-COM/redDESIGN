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
	function addRow(elementid) {
		var counter = akeeba.jQuery('#elements-counter').val();

		akeeba.jQuery('#' + elementid).append(
			'<div id="row_' + counter + '" class="row">' +
				'<div class="control-group character-group"><label for="charcter_' + counter + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>' +
				'<input class="input-mini" type="text" value="" maxlength="1" id="charcter_' + counter + '" name="charcter[]"></div>' +
				'<div class="control-group character-group"><label for="width_' + counter + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>' +
				'<input class="input-small" type="text" value="" maxlength="15" id="width_' + counter + '" name="width[]"></div>' +
				'<div class="control-group character-group"><label for="height_' + counter + '"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>' +
				'<input class="input-small" type="text" value="" maxlength="15" id="height_' + counter + '" name="height[]"></div>' +
				'<div class="control-group character-group"><label for="typography_' + counter + '"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>' +
				'<select id="typography" name="typography[]">' +
				'<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>' +
				'<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>' +
				'<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>' +
				'<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>' +
				'<option value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>' +
				'</select></div>' +
				'<div class="control-group character-group"><label for="typography_height_' + counter + '"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>' +
				'<input class="input-small" type="text" value="" maxlength="15" id="typography_height_' + counter + '" name="typography_height[]"></div>' +
				'</div>'
		);

		counter += 1;
		akeeba.jQuery('elements-counter').val(counter);
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="font">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_font_id" value="<?php echo $this->item->reddesign_font_id; ?>">
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

	<div id="character-settings" class="span12">
		<h4><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS'); ?></h4>

		<p><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS_DESC'); ?></p>

		<div id="rows">

		</div>
		<input id="elements-counter" type="hidden" value="0">

		<div id="add-row-button">
			<a rel="" onclick="addRow('rows');" class="btn btn-small"
			   title="<?php echo JText::_('COM_REDDESIGN_FONT_ADD_ROW'); ?>">
				<i class="icon-plus"></i>
				<?php echo JText::_('COM_REDDESIGN_FONT_ADD_ROW'); ?>
			</a>
		</div>
	</div>
</form>