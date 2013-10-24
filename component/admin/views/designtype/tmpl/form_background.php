<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->reddesign_designtype_id . '&tab=backgrounds';

?>
<form id="background" name="background" method="post" action="index.php" enctype="multipart/form-data">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="background">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="returnurl" value="<?php echo base64_encode($return_url); ?>" />
	<input type="hidden" name="reddesign_designtype_id" id="background_reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>" />
	<input type="hidden" name="reddesign_background_id" id="reddesign_background_id" value="" />

	<div id="backgrounds-configuration">

		<h3 id="backgroundTitle"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="bg_title" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
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
			<label class="control-label " for="bg_eps_file">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_FILE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="bg_eps_file" id="bg_eps_file" value="">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="isPDFbgimage">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_BACKGROUND_TYPE'); ?>
			</label>
			<div class="controls">
				<?php echo JHtml::_('select.radiolist', $this->backgroundTypeOptions, 'isPDFbgimage', '', 'value', 'text', $this->item->isPDFbgimage); ?>
			</div>
		</div>

		<div class="control-group" id="createPreviewContainer">
			<div class="controls">
				<input type="checkbox" id="createPreview" name="createPreview" checked="checked" value="1">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_CREATE_PREVIEW'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_CREATE_PREVIEW_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group" id="useCheckerboardContainer">
			<div class="controls">
				<input type="checkbox" id="useCheckerboard" name="useCheckerboard" checked="checked" value="1">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_USE_CHECKERBOARD'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_USE_CHECKERBOARD_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group previewbg">
			<label class="control-label " for="bg_thumbnail">
				<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL'); ?>
			</label>
			<div class="controls">
				<input type="file" name="thumbnail" id="bg_thumbnail" value="">&nbsp;<a href="#" class="modal" id="BgThumbnailLink"></a>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL_DESC'); ?>
				</span>
			</div>
		</div>

	</div>

	<div class="form-actions">
		<input type="button" class="btn btn-success" id="saveBgBtn" value="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>"/>
		<input type="button" class="btn" id="cancelBgBtn" value="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>"/>
	</div>

</form>

<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function () {
			var backgroundType = akeeba.jQuery('input[name=backgroundType]:checked', '#background').val();

			if (backgroundType == 0)
			{
				akeeba.jQuery("#createPreviewContainer").hide();
			}

			akeeba.jQuery("input[name=backgroundType]").change(function() {
				var backgroundType = akeeba.jQuery('input[name=backgroundType]:checked', '#background').val();

				if (backgroundType == 0)
				{
					akeeba.jQuery("#createPreviewContainer").hide();
				}
				else
				{
					akeeba.jQuery("#createPreviewContainer").show();
				}
			});

			akeeba.jQuery(document).on('click', '#saveBgBtn',
				function () {
					akeeba.jQuery('#background').submit();
				}
			);

			akeeba.jQuery(document).on('click', '#cancelBgBtn',
				function () {
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

