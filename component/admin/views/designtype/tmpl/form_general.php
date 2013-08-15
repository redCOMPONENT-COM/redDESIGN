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
			<label class="control-label " for="sample_image">
				<?php echo JText::_('COM_REDDESIGN_COMMON_IMAGE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="sample_image" id="sample_image" value="">
				<a class="modal" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/' . $this->item->sample_image); ?>">
					<?php echo $this->item->sample_image; ?>
				</a>
			</div>
		</div>

		<div class="control-group" id="autoGenerateThumb" style="display: none;">
				<label class="control-label" for="autoGenerateThumbCheck">
					<?php echo JText::_('COM_REDDESIGN_COMMON_AUTO_GENERATE_THUMB'); ?>
				</label>
				<div class="controls">
					<input type="checkbox" name="autoGenerateThumbCheck" id="autoGenerateThumbCheck" />
				</div>
		</div>

		<div class="control-group" id="thumbnail-group">
			<label class="control-label " for="sample_thumb">
				<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL'); ?>
			</label>
			<div class="controls">
				<input type="file" name="sample_thumb" id="sample_thumb" value="">
				<a class="modal" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/' . $this->item->sample_thumb); ?>">
					<?php echo $this->item->sample_thumb; ?>
				</a>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_RELATED_DESIGNTYPES'); ?>
			</label>
			<div class="controls">
				<?php echo $this->relatedDesigntypes; ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_RELATED_DESIGNTYPES_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_ACCESSORYTYPES'); ?>
			</label>
			<div class="controls">
				<?php echo $this->accessorytypes; ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_ACCESSORYTYPES_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="description">
				<?php echo JText::_('COM_REDDESIGN_COMMON_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<?php echo $this->editor->display('description', $this->item->description, 400, 400, 20, 20, true); ?>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
	akeeba.jQuery("document").ready(
		function()
		{
			akeeba.jQuery("#sample_image").change(
				function()
				{
					akeeba.jQuery('#autoGenerateThumb').fadeIn();
				}
			);
			akeeba.jQuery("#autoGenerateThumbCheck").change(
				function()
				{
					if (akeeba.jQuery("#autoGenerateThumbCheck").is(':checked'))
					{
						akeeba.jQuery('#thumbnail-group').fadeOut();
					}
					else
					{
						akeeba.jQuery('#thumbnail-group').fadeIn();
					}
				}
			);
		}
	);
</script>