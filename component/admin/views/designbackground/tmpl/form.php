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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designbackground">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_designbackground_id"
	       value="<?php echo $this->item->reddesign_designbackground_id; ?>">
	<?php if (!empty($this->item->epsfile) && !empty($this->item->jpegpreviewfile)) : ?>
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<input type="hidden" id="area_x1" name="area_x1" value="<?php echo $this->item->area_x1; ?>">
		<input type="hidden" id="area_y1" name="area_y1" value="<?php echo $this->item->area_y1; ?>">
		<input type="hidden" id="area_x2" name="area_x2" value="<?php echo $this->item->area_x2; ?>">
		<input type="hidden" id="area_y2" name="area_y2" value="<?php echo $this->item->area_y2; ?>">
		<input type="hidden" id="area_width" name="area_width" value="<?php echo $this->item->area_width; ?>">
		<input type="hidden" id="area_height" name="area_height" value="<?php echo $this->item->area_height; ?>">
	<?php endif; ?>

	<div id="basic_configuration" class="span12">
		<h3><?php echo JText::_('COM_REDDESIGN_TITLE_DESIGNBACKGROUNDS_EDIT'); ?></h3>
		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>"
					   class="inputbox required" size="50">
					<span
						class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>
		<?php if (!empty($this->item->epsfile) && !empty($this->item->jpegpreviewfile)) : ?>
			<?php echo $this->loadTemplate('preview'); ?>
		<?php else : ?>
			<?php echo $this->loadTemplate('upload'); ?>
		<?php endif; ?>
		<div class="control-group">
			<label class="control-label " for="reddesign_design_id">
				<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_DESIGN'); ?>
			</label>

			<div class="controls">
				<?php
				// Designs select list
				$options = array();
				foreach ($this->designs_list as $design) :
					$options[] = JHTML::_('select.option', $design->reddesign_design_id, $design->title);
				endforeach;

				echo JHTML::_('select.genericlist', $options, 'reddesign_design_id', 'class="inputbox"', 'value', 'text', $this->item->reddesign_design_id);
				?>
				<span
					class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_DESIGN_DESC'); ?></span>
			</div>
		</div>
		<?php if (!empty($this->fonts_list)) : ?>
			<?php echo $this->loadTemplate('fonts'); ?>
		<?php endif; ?>
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

