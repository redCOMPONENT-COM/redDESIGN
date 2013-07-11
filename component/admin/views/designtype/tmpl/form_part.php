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
<form id="background" name="background" method="post" action="index.php" enctype="multipart/form-data">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="part">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="returnurl" value="<?php echo base64_encode($returnUrl); ?>" />
	<input type="hidden" name="reddesign_designtype_id" id="background_reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>" />
	<input type="hidden" name="reddesign_part_id" id="reddesign_part_id" value="" />

	<div id="parts-configuration">
		<h3 id="partTitle"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label" for="part_title">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="part_title" value="">
			</div>
		</div>
		<div class="control-group">
			<label for="enabled" class="control-label">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'enabled', null, 1); ?>
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
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_PRICE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="price" id="price" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_PRICE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_STOCK'); ?>
			</label>

			<div class="controls">
				<input type="text" name="stock" id="stock" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_STOCK_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="part_enabled">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>

			<div class="controls">
				<select name="enabled" id="part_enabled">
					<option value="1" selected="selected"><?php echo JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
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
			akeeba.jQuery(document).on('click', '#saveBgBtn', function () {
					akeeba.jQuery('#background').submit();
				}
			);
			akeeba.jQuery(document).on('click', '#cancelBgBtn', function () {
					akeeba.jQuery("#backgroundTitle").html("<?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?>");
					akeeba.jQuery("#reddesign_background_id").val('');
					akeeba.jQuery("#bg_title").val('');
					akeeba.jQuery("#bg_isPDFbgimage").val('0');
					akeeba.jQuery("#bg_enabled").val('1');

					akeeba.jQuery('#backgroundForm').fadeOut("fast");
					akeeba.jQuery('#addBgBtn').parent().fadeIn("fast");
				}
			);
		});
</script>