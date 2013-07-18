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
<?php if (!extension_loaded('gd') && !function_exists('gd_info')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_GD_LIBRARY'); ?></p>
<?php endif; ?>
<?php if (!extension_loaded('imagick')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
<?php endif; ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="font">
	<input type="hidden" name="task" value="">
	<input type="hidden" id="reddesign_font_id" name="reddesign_font_id" value="<?php echo $this->item->reddesign_font_id; ?>">
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
					<img style="border: 1px black solid;" src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/fonts/') . $this->item->font_thumb; ?>">
				</div>
			</div>
		<?php else : ?>
			<div class="control-group">
				<label class="control-label " for="font_file">
					<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_FILE'); ?>
				</label>

				<div class="controls">
					<input type="file" name="font_file" id="font_file" class="inputbox" value="<?php echo $this->item->font_file; ?>" required="required">
				</div>
			</div>
		<?php endif; ?>

		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->title; ?>" maxlength="255" size="32" id="title" name="title">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="enabled" class="control-label">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'enabled', null, $this->item->enabled); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="default_width">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_WIDTH'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_width; ?>" maxlength="10" size="32" id="default_width" name="default_width" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="default_height">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_height; ?>" maxlength="10" size="32" id="default_height" name="default_height" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="default_caps_height">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_CAPS_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_caps_height; ?>" maxlength="10" size="32" id="default_caps_height" name="default_caps_height" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="default_baseline_height">
				<?php echo JText::_('COM_REDDESIGN_FONT_DEFAULT_BASELINE_HEIGHT'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->default_baseline_height; ?>" maxlength="10" size="32" id="default_baseline_height" name="default_baseline_height" required="required">
			</div>
		</div>
	</div>
	<?php
	if (!empty($this->item->reddesign_font_id))
	{
		echo $this->loadTemplate('chars');
	}
	?>
</form>