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

<?php if (!extension_loaded('gd') && !function_exists('gd_info')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_GD_LIBRARY'); ?></p>
<?php endif; ?>

<?php if (!extension_loaded('imagick')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
<?php endif; ?>

<div id="basic_configuration" class="span12">
	<?php if (!empty($this->item->font_file)) : ?>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_THUMB_PREVIEW') ?>
			</label>

			<div class="controls">
				<?php echo JHtml::image('media/com_reddesign/fonts/' . $this->fontThumbnail, '') ?>
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
		<label class="control-label " for="title">
			<?php echo $this->form->getLabel('title'); ?>
		</label>

		<div class="controls">
			<?php echo $this->form->getInput('title'); ?>
			<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_TITLE_DESC'); ?></span>
		</div>
	</div>

	<div class="control-group">
		<label for="enabled" class="control-label">
			<?php echo $this->form->getLabel('enabled'); ?>
		</label>
		<div class="controls">
			<?php echo $this->form->getInput('enabled'); ?>
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