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
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" id="area_x1" name="area_x1" value="<?php echo $this->item->area_x1; ?>">
	<input type="hidden" id="area_y1" name="area_y1" value="<?php echo $this->item->area_y1; ?>">
	<input type="hidden" id="area_x2" name="area_x2" value="<?php echo $this->item->area_x2; ?>">
	<input type="hidden" id="area_y2" name="area_y2" value="<?php echo $this->item->area_y2; ?>">
	<input type="hidden" id="area_width" name="area_width" value="<?php echo $this->item->area_width; ?>">
	<input type="hidden" id="area_height" name="area_height" value="<?php echo $this->item->area_height; ?>">

	<div id="basic_configuration" class="span12">
		<h3><?php echo JText::_('COM_REDDESIGN_TITLE_DESIGNBACKGROUNDS_EDIT'); ?></h3>
		<?php if (!empty($this->item->epsfile) && !empty($this->item->jpegpreviewfile)) : ?>
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW') ?>:
				</label>

				<div class="controls">
					<script type="text/javascript">
						function populateSelectorData(img, selection) {
							jQuery('#area_x1').val(selection.x1);
							jQuery('#area_y1').val(selection.y1);
							jQuery('#area_x2').val(selection.x2);
							jQuery('#area_y2').val(selection.y2);
							jQuery('#area_width').val(selection.width);
							jQuery('#area_height').val(selection.height);
						}

						jQuery(document).ready(function ($) {
							jQuery('img#background').imgAreaSelect({
								handles: true,
								<?php
								if (!empty($this->item->area_x1)
									&& !empty($this->item->area_y1)
									&& !empty($this->item->area_x2)
									&& !empty($this->item->area_y2)
									&& !empty($this->item->area_width)
									&& !empty($this->item->area_height) )
									: ?>
								x1: <?php echo $this->item->area_x1; ?>,
								y1: <?php echo $this->item->area_y1; ?>,
								x2: <?php echo $this->item->area_x2; ?>,
								y2: <?php echo $this->item->area_y2; ?>,
								area_width: <?php echo $this->item->area_width; ?>,
								area_height: <?php echo $this->item->area_height; ?>,
								<?php endif; ?>
								onSelectEnd: populateSelectorData
							});
						});
					</script>
					<img id="background"
					     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>" />
						<span
							class="help-block">
							<br/>
							<span class="label label-info"><?php echo  JText::_('COM_REDDESIGN_COMMON_ATTENTION') ?>
							</span> <?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW_DESC'); ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label " for="title">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE'); ?>
					* </label>

				<div class="controls">
					<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>"
					       class="inputbox required" size="50">
					<span
						class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE_DESC'); ?></span>
				</div>
			</div>
		<?php else : ?>
			<div class="control-group">
				<label class="control-label " for="fontfile">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_EPSFILE'); ?>
				</label>

				<div class="controls">
					<input type="file" name="epsfile" id="epsfile" value="<?php echo $this->item->epsfile; ?>">
					<span
						class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_EPSFILE_DESC'); ?></span>
				</div>
			</div>
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
		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_AVAILABLE_FONTS'); ?>
			</label>

			<div class="controls">
				<fieldset>
					<?php foreach ($this->fonts_list as $font) : ?>
						<label class="checkbox">
							<input
								type="checkbox"
								name="background_fonts[]"
								value="<?php echo $font->reddesign_font_id; ?>"
								/><?php echo $font->title; ?>
						</label>
					<?php endforeach; ?>
				</fieldset>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
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

