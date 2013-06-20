<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHTML::_('behavior.framework');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="background">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_designtype_id" value="<?php echo $this->input->getInt('designtype', 0); ?>">
	<input type="hidden" value="returnurl" name="index.php?option=com_reddesign&view=desingtype&id=<?php echo $this->input->getInt('designtype', 0); ?>">
	<input type="hidden" name="reddesign_background_id">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<div id="basic_configuration" class="span12">
		<h3>
			<?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?>
		</h3>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="eps_file">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_FILE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="eps_file" id="eps_file" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="isPDFbgimage">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'isPDFbgimage', null, $this->item->isPDFbgimage); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('JSTATUS'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'published', null, $this->item->status); ?>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
			</div>
		</div>
	</div>
	<p>
		<a href="#" class="btn" id="closemodal"><?php echo JText::_('JCANCEL'); ?></a>
		<a href="#" class="btn btn-primary" id="savebackground" onclick="Joomla.submitbutton('save')"><?php echo JText::_('JSAVE'); ?></a>
	</p>
</form>

<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function()
		{
			akeeba.jQuery(document).on('click', '#closemodal', function()
				{
					parent.SqueezeBox.close()
				}
			);
		});
</script>