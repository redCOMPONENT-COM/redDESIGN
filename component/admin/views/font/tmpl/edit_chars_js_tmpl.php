<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

?>

<script id="charsMustache" type="text/html">
	<div id="row{{charId}}" class="row-fluid">
		<div class="control0-group character-group span1">
			<label for="fontChar{{charId}}">
				<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?>
			</label>
			<input class="input-mini"
				   type="text"
				   maxlength="1"
				   value="{{fontChar}}"
				   id="fontChar{{charId}}"
				   name="fontChar{{charId}}"
				>
		</div>
		<div class="control-group character-group span1">
			<label for="width{{charId}}">
				<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?>
			</label>
			<input class="input-mini"
				   type="text"
				   maxlength="15"
				   value="{{width}}"
				   id="width{{charId}}"
				   name="width{{charId}}"
				>
		</div>
		<div class="control-group character-group span1">
			<label for="height{{charId}}">
				<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?>
			</label>
			<input class="input-mini"
				   type="text"
				   maxlength="15"
				   value="{{height}}"
				   id="height{{charId}}"
				   name="height{{charId}}"
				>
		</div>
		<div class="control-group character-group span3">
			<label for="typography{{charId}}"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
			<select id="typography{{charId}}" name="typography">
				<option value="0"><?php echo JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY'); ?></option>
				<option value="1"><?php echo JText::_('COM_REDDESIGN_FONT_X_HEIGHT'); ?></option>
				<option value="2"><?php echo JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT'); ?></option>
				<option value="3"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE'); ?></option>
				<option value="4"><?php echo JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'); ?></option>
			</select>
		</div>
		<div class="control-group character-group span2">
			<label for="typographyHeight{{charId}}">
				<?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?>
			</label>
			<input class="input-small"
				   type="text"
				   maxlength="15"
				   value="{{typographyHeight}}"
				   id="typographyHeight{{charId}}"
				   name="typographyHeight{{charId}}"
			>
		</div>
		<div class="span2">
			<div class="control-group character-group span6">
				<label for="addButton{{charId}}">&nbsp;</label>
				<button id="addButton{{charId}}"
						type="button"
						class="btn btn-success btn-small"
						data-loading-text="<?php echo JText::_('COM_REDDESIGN_FONT_CHAR_UPDATING'); ?>"
						onclick="saveChar({{charId}});"
						title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">
					<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>
				</button>
			</div>
			<div class="control-group character-group span6">
				<label for="removeButton{{charId}}">&nbsp;</label>
				<button id="removeButton{{charId}}"
						type="button"
						class="btn btn-danger btn-small"
						data-loading-text="<?php echo JText::_('COM_REDDESIGN_FONT_CHAR_REMOVING'); ?>"
						onclick="removeChar({{charId}});"
						title="<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>"
					>
					<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>
				</button>
			</div>
		</div>
	</div>
</script>