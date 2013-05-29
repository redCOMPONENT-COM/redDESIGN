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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designbackground">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_designbackground_id" value="<?php echo $this->item->reddesign_designbackground_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
	<div id="basic_configuration" class="span12">
		<h3><?php echo JText::_('COM_REDDESIGN_TITLE_DESIGNBACKGROUNDS_EDIT'); ?></h3>
		<?php if (!empty($this->item->epsfile) && !empty($this->item->jpegpreviewfile)) : ?>
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW') ?>:
				</label>
				<div class="controls">
					<script type="text/javascript">
						$(document).ready(function () {
							$('img#background').imgAreaSelect({
								handles: true
								//onSelectEnd: someFunction
							});
						});
					</script>
					<img id="background" src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label " for="title">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE'); ?>
					*				</label>
				<div class="controls">
					<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>" class="inputbox required" size="50">
					<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_TITLE_DESC'); ?></span>
				</div>
			</div>
		<?php else : ?>
			<div class="control-group">
				<label class="control-label " for="fontfile">
					<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_EPSFILE'); ?>
				</label>
				<div class="controls">
					<input type="file" name="epsfile" id="epsfile" value="<?php echo $this->item->epsfile; ?>">
					<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_EPSFILE_DESC'); ?></span>
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
				foreach($this->designs_list as $design) :
					$options[] = JHTML::_('select.option', $design->reddesign_design_id, $design->title);
				endforeach;

				echo JHTML::_('select.genericlist', $options, 'reddesign_design_id', 'class="inputbox"', 'value', 'text', $this->item->reddesign_design_id);
				?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_DESIGN_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_AVAILABLE_FONTS'); ?>
			</label>
			<div class="controls">
				<fieldset>
				<?php foreach($this->fonts_list as $font) : ?>
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

