<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$returnUrl = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->reddesign_designtype_id . '&tab=parts';

?>
<form id="part" name="background" method="post" action="index.php" enctype="multipart/form-data">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="part">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="returnurl" value="<?php echo base64_encode($returnUrl); ?>" />
	<input type="hidden" name="reddesign_designtype_id" id="part_reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>" />
	<input type="hidden" name="reddesign_part_id" id="reddesign_part_id" value="" />

	<div id="parts-configuration">
		<h3 id="partTitle"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_ADD_NEW'); ?></h3>
		<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_DESC'); ?></span>
		<div class="control-group">
			<label class="control-label" for="part_title">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="part_title" required="required" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="enabled">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'enabled', null, 1); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="accessory">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_IS_ACCESSORY'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'accessory', null, 1); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_IS_ACCESSORY_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="partImage">
				<?php echo JText::_('COM_REDDESIGN_COMMON_IMAGE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="partImage" id="partImage" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="partThumbnail">
				<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL'); ?>
			</label>
			<div class="controls">
				<input type="file" name="partThumbnail" id="partThumbnail" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="partPrice">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_PRICE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="price" id="partPrice" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_PRICE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="partStock">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_STOCK'); ?>
			</label>

			<div class="controls">
				<input type="text" name="stock" id="partStock" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_STOCK_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="required">
				<?php echo JText::_('COM_REDDESIGN_COMMON_REQUIRED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'required', null, 1); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="single_select">
				<?php echo JText::_('COM_REDDESIGN_COMMON_SINGLE_SELECT_GROUP'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'single_select', null, 1); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_COMMON_SINGLE_SELECT_GROUP_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="partDescription">
				<?php echo JText::_('COM_REDDESIGN_COMMON_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<?php echo $this->editor->display('partDescription', '', 400, 400, 20, 20, false, 'partDescription'); ?>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="button" class="btn btn-success" id="savePartBtn"
			   value="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>"/>
		<input type="button" class="btn" id="cancelPartBtn"
			   value="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>"/>
	</div>
</form>


<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#savePartBtn', function () {
					akeeba.jQuery('#part').submit();
				}
			);
			akeeba.jQuery(document).on('click', '#cancelPartBtn', function () {
					akeeba.jQuery("#partTitle").html("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_ADD_NEW'); ?>");
					akeeba.jQuery("#reddesign_part_id").val('');
					akeeba.jQuery("#part_title").val('');
					akeeba.jQuery("#enabled").val(1);
					akeeba.jQuery("#partPrice").val('');
					akeeba.jQuery("#partStock").val('');
					akeeba.jQuery("#required").val(1);
					akeeba.jQuery("#single_select").val(1);
					tinyMCE.activeEditor.setContent('');

					akeeba.jQuery('#partForm').fadeOut("fast");
					akeeba.jQuery('#addPartBtn').parent().fadeIn("fast");
				}
			);
		});
</script>