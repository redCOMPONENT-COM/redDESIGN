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

<div id="character-settings" class="span12 col-md12">
	<h4><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS'); ?></h4>

	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_SPECIFIC_SETTINGS_DESC'); ?></span>

	<div id="all-rows">
		<div id="other-rows" class="span9 col-md9">
			<?php foreach($this->item->chars as $charSettings) : ?>
				<?php if (!empty($charSettings)) : ?>
					<div id="row<?php echo $charSettings->id; ?>" class="row-fluid">
						<div class="control-group character-group span1">
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
						<div class="control-group character-group span1">
							<label for="width<?php echo $charSettings->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?>
							</label>
							<input class="input-mini"
								   type="text"
								   maxlength="15"
								   value="<?php echo $charSettings->width; ?>"
								   id="width<?php echo $charSettings->id; ?>"
								   name="width<?php echo $charSettings->id; ?>"
							>
						</div>
						<div class="control-group character-group span1">
							<label for="height<?php echo $charSettings->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?>
							</label>
							<input class="input-mini"
								   type="text"
								   maxlength="15"
								   value="<?php echo $charSettings->height; ?>"
								   id="height<?php echo $charSettings->id; ?>"
								   name="height<?php echo $charSettings->id; ?>"
							>
						</div>
						<div class="control-group character-group span3 col-md3">
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
						<div class="control-group character-group span2 col-md2">
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
						<div class="span2 col-md2">
							<div class="control-group character-group span6 col-md6">
								<label for="addButton<?php echo $charSettings->id; ?>">&nbsp;</label>
								<button id="addButton<?php echo $charSettings->id; ?>"
										type="button"
										class="btn btn-success btn-small"
										data-loading-text="<?php echo JText::_('COM_REDDESIGN_FONT_CHAR_UPDATING'); ?>"
										onclick="saveChar(<?php echo $charSettings->id; ?>);"
										title="<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>"
								>
									<?php echo JText::_('COM_REDDESIGN_FONT_SAVE_CHAR'); ?>
								</button>
							</div>
							<div class="control-group character-group span6 col-md6">
								<label for="removeButton<?php echo $charSettings->id; ?>">&nbsp;</label>
								<button id="removeButton<?php echo $charSettings->id; ?>"
										type="button"
										class="btn btn-danger btn-small"
										data-loading-text="<?php echo JText::_('COM_REDDESIGN_FONT_CHAR_REMOVING'); ?>"
										onclick="removeChar(<?php echo $charSettings->id; ?>);"
										title="<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>"
								>
									<?php echo JText::_('COM_REDDESIGN_FONT_REMOVE_CHAR'); ?>
								</button>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div id="startRow" class="row-fluid">
			<div class="span9 col-md9">
				<div class="row-fluid">
					<div class="control-group character-group span1">
						<label for="fontChar"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER') ?></label>
						<input class="input-mini" type="text" value="" maxlength="1" id="fontChar" name="fontChar">
					</div>
					<div class="control-group character-group span1">
						<label for="width"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_WIDTH') ?></label>
						<input class="input-mini" type="text" value="" maxlength="15" id="width" name="width">
					</div>
					<div class="control-group character-group span1">
						<label for="height"><?php echo JText::_('COM_REDDESIGN_FONT_CHARACTER_HEIGHT') ?></label>
						<input class="input-mini" type="text" value="" maxlength="15" id="height" name="height">
					</div>
					<div class="control-group character-group span3 col-md3">
						<label for="typography"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY') ?></label>
						<?php echo JHtml::_('select.genericlist', $this->typographies, 'typography', '', 'value', 'text', 0); ?>
					</div>
					<div class="control-group character-group span2 col-md2">
						<label for="typographyHeight"><?php echo JText::_('COM_REDDESIGN_FONT_TYPOGRAPHY_HEIGHT') ?></label>
						<input class="input-small" type="text" value="" maxlength="15" id="typographyHeight" name="typographyHeight">
					</div>
					<div class="control-group character-group span2 col-md2">
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