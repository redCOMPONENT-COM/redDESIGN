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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="font">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_font_id" value="<?php echo $this->item->reddesign_font_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
	<div id="basic_configuration" class="span12">
		<h3>Add/edit Font</h3>
		<?php if (!empty($this->item->fontfile) && !empty($this->item->fontthumb)) : ?>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_FONT_THUMB_PREVIEW') ?>:
			</label>
			<div class="controls">
					<img style="border: 1px black solid;" src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/fonts/') . $this->item->fontthumb; ?>">
			</div>
		</div>
		<?php else : ?>
		<div class="control-group">
			<label class="control-label " for="fontfile">
				<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_FILE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="fontfile" id="fontfile" value="<?php echo $this->item->fontfile; ?>">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_TITLE_DESC'); ?></span>
			</div>
		</div>
		<?php endif; ?>
		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_FONT_FIELD_TITLE'); ?>
				*				</label>
			<div class="controls">
				<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>" class="inputbox required" size="50">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_FONT_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('JSTATUS'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_(
					'select.booleanlist',
					'enabled',
					'class="inputbox"',
					$this->item->enabled,
					JText::_('JPUBLISHED'),
					JText::_('JUNPUBLISHED')
				);

				?>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
			</div>
		</div>
	</div>
</form>