<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
?>
<div id="basic_configuration" class="span12">
	<?php if (!empty($this->item->font_file)) : ?>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_THUMB_PREVIEW') ?>
			</label>

			<div class="controls">
				<span style="font-family: '<?php echo $this->item->name; ?>', Arial; font-size:30px;">Lorem ipsum</span>
			</div>
		</div>
	<?php else : ?>
		<div class="control-group">
			<label class="control-label " for="font_file">
				<?php echo $this->form->getLabel('font_file'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('font_file'); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="control-group">
		<label class="control-label " for="name">
			<?php echo $this->form->getLabel('name'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('name'); ?>
			<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_TITLE_DESC'); ?></span>
		</div>
	</div>

	<div class="control-group">
		<label for="state" class="control-label">
			<?php echo $this->form->getLabel('state'); ?>
		</label>
		<div class="controls">
			<?php echo $this->form->getInput('state'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="default_width">
			<?php echo $this->form->getLabel('default_width'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('default_width'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="default_height">
			<?php echo $this->form->getLabel('default_height'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('default_height'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="default_caps_height">
			<?php echo $this->form->getLabel('default_caps_height'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('default_caps_height'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="default_baseline_height">
			<?php echo $this->form->getLabel('default_baseline_height'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('default_baseline_height'); ?>
		</div>
	</div>
</div>