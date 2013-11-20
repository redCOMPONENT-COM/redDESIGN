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
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_designtype_id" id="reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<div id="basic_configuration" class="span12">
		<h3>
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_HEADER'); ?>
		</h3>

		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="published" class="control-label" for="published">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'published', null, $this->item->enabled); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.genericlist', $this->sizerOptions, 'fontsizer', 'class="inputbox"', 'value', 'text', $this->item->fontsizer); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="minWidth">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_MIN_WIDTH'); ?>
			</label>
			<div class="controls">
				<input class="input-small" type="text" name="minWidth" id="minWidth" value="<?php echo $this->item->minWidth; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="minHeight">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_MIN_HEIGHT'); ?>
			</label>
			<div class="controls">
				<input class="input-small" type="text" name="minHeight" id="minHeight" value="<?php echo $this->item->minHeight; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="maxWidth">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_MAX_WIDTH'); ?>
			</label>
			<div class="controls">
				<input class="input-small" type="text" name="maxWidth" id="maxWidth" value="<?php echo $this->item->maxWidth; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="maxHeight">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_MAX_HEIGHT'); ?>
			</label>
			<div class="controls">
				<input class="input-small" type="text" name="maxHeight" id="maxHeight" value="<?php echo $this->item->maxHeight; ?>">
			</div>
		</div>
	</div>
</form>