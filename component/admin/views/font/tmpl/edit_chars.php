<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

RHelperAsset::load('mustache.min.js', 'com_reddesign');

// Load JS template for character setting rows.
echo $this->loadTemplate('chars_js_tmpl');

// Load dynamically created JS.
echo $this->loadTemplate('chars_js');
?>

<div id="character-settings" class="span12">
	<h4><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS'); ?></h4>

	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS_DESC'); ?></span>

	<div id="all-rows">
		<div id="other-rows">
			<?php foreach($this->item->chars as $charSettings) : ?>
				<div id="row<?php echo $charSettings->id; ?>" class="row">
					<div class="control-group character-group">
						<label for="fontChar<?php echo $charSettings->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?>
						</label>
						<input class="input-mini"
							   type="text"
							   maxlength="1"
							   value="<?php echo $charSettings->font_char; ?>"
							   id="fontChar<?php echo $charSettings->id; ?>"
							   name="fontChar<?php echo $charSettings->id; ?>"
							>
					</div>
					<div class="control-group character-group">
						<label for="width<?php echo $charSettings->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?>
						</label>
						<input class="input-small"
							   type="text"
							   maxlength="15"
							   value="<?php echo $charSettings->width; ?>"
							   id="width<?php echo $charSettings->id; ?>"
							   name="width<?php echo $charSettings->id; ?>"
							>
					</div>
					<div class="control-group character-group">
						<label for="height<?php echo $charSettings->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?>
						</label>
						<input class="input-small"
							   type="text"
							   maxlength="15"
							   value="<?php echo $charSettings->height; ?>"
							   id="height<?php echo $charSettings->id; ?>"
							   name="height<?php echo $charSettings->id; ?>"
							>
					</div>
					<div class="control-group character-group">
						<label for="typography<?php echo $charSettings->id; ?>"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
						<?php
							echo JHtml::_(
											'select.genericlist',
											$this->typographies,
											'typography' . $charSettings->id,
											'',
											'value',
											'text',
											$charSettings->typography
							);
						?>
					</div>
					<div class="control-group character-group">
						<label for="typographyHeight<?php echo $charSettings->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?>
						</label>
						<input class="input-small"
							   type="text"
							   maxlength="15"
							   value="<?php echo $charSettings->typography_height; ?>"
							   id="typographyHeight<?php echo $charSettings->id; ?>"
							   name="typographyHeight<?php echo $charSettings->id; ?>"
							>
					</div>
					<div class="control-group character-group">
						<label for="add_button_<?php echo $charSettings->id; ?>">&nbsp;</label>
						<button id="add_button_<?php echo $charSettings->id; ?>"
								type="button"
								class="btn btn-success btn-mini"
								onclick="saveChar(<?php echo $charSettings->id; ?>);"
								title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>">
							<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>
						</button>
					</div>
					<div class="control-group character-group">
						<label for="remove_button_<?php echo $charSettings->id; ?>">&nbsp;</label>
						<button id="remove_button_<?php echo $charSettings->id; ?>"
								type="button"
								class="btn btn-danger btn-mini"
								onclick="removeChar(<?php echo $charSettings->id; ?>);"
								title="<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>"
							>
							<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div id="startRow" class="row-fluid">
			<div class="span9">
				<div class="row-fluid">
					<div class="control-group character-group span1">
						<label for="font_char"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>
						<input class="input-mini" type="text" value="" maxlength="1" id="font_char" name="font_char">
					</div>
					<div class="control-group character-group span1">
						<label for="width"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>
						<input class="input-mini" type="text" value="" maxlength="15" id="width" name="width">
					</div>
					<div class="control-group character-group span1">
						<label for="height"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>
						<input class="input-mini" type="text" value="" maxlength="15" id="height" name="height">
					</div>
					<div class="control-group character-group span3">
						<label for="typography"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
						<?php echo JHtml::_('select.genericlist', $this->typographies, 'typography', '', 'value', 'text', 0); ?>
					</div>
					<div class="control-group character-group span2">
						<label for="typographyHeight"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>
						<input class="input-small" type="text" value="" maxlength="15" id="typographyHeight" name="typographyHeight">
					</div>
					<div class="control-group character-group span2">
						<label for="addButton">&nbsp;</label>
						<button id="addButton"
								type="button"
								class="btn btn-success btn-small"
								data-loading-text="<?php echo JText::_('COM_REDDESIGN_FONT_CHAR_SAVING'); ?>"
								onclick="saveChar(0);"
								title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>"
							>
							<?php echo JText::_('COM_REDDESIGN_FONT_ADD_CHAR'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>