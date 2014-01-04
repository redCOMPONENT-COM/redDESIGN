<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('rjquery.framework');

if (isset($displayData) && (count($displayData->item) > 0))
{
	$this->item = $displayData->item;
	$this->form = $displayData->model->getForm();
}

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->designtype_id . '&tab=backgrounds';

?>

<script type="text/javascript">
	jQuery(document).ready(
		function () {
			if(jQuery("#jform_isPreviewBg").is(":checked"))
			{
				jQuery("#isDefaultPreviewContainer").show();
				jQuery("#useCheckerboardContainer").show();
			}
			else
			{
				jQuery("#isDefaultPreviewContainer").hide();
				jQuery("#useCheckerboardContainer").hide();
			}

			jQuery("#jform_isPreviewBg").change(function() {
				if (jQuery("#jform_isPreviewBg").is(":checked"))
				{
					jQuery("#isDefaultPreviewContainer").show();
					jQuery("#useCheckerboardContainer").show();
				}
				else
				{
					jQuery("#isDefaultPreviewContainer").hide();
					jQuery("#jform_isDefaultPreview").attr('checked', false);
					jQuery("#useCheckerboardContainer").hide();
					jQuery("#jform_useCheckerboard").attr('checked', false);
				}
			});

			jQuery("#bgFormSubmit").click(function ()
			{
				var form = jQuery('#bgForm');
				form.attr("action", "index.php?option=com_reddesign&task=background.ajaxBackgroundSave");
				form.attr("method", "post");
				form.attr("enctype", "multipart/form-data");
				form.attr("encoding", "multipart/form-data");
				form.attr("target", "bgPostIframe");
				// form.attr("file", jQuery('#jform_bg_eps_file').val());
				form.submit();
				jQuery("#bgPostIframe").load(function () {
					iframeContents = jQuery("#bgPostIframe")[0].contentWindow.document.body.innerHTML;
					jQuery("#bgMessage").html(iframeContents);
				});

				return false;

			});

			jQuery(document).on('click', '#cancelBgBtn',
				function () {
					jQuery("#backgroundTitle").html("<?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?>");
					document.getElementById('bgForm').reset();

					jQuery('#backgroundForm').fadeOut("fast");
					jQuery('#addBgBtn').parent().fadeIn("fast");
				}
			);
		});
</script>

<div id="bgMessage"></div>

<form action="index.php?option=com_reddesign&task=background.ajaxBackgroundSave"
	id="bgForm" name="adminForm" method="POST" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="returnurl" value="<?php echo base64_encode($return_url); ?>" />
	<input type="hidden" name="jform[designtype_id]" id="background_reddesign_designtype_id" value="<?php echo $this->item->designtype_id; ?>" />
	<input type="hidden" name="jform[id]" id="reddesign_background_id" value="" />
	<div id="backgrounds-configuration" class="span12">

		<h3 id="backgroundTitle"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label ">
				<?php echo $this->form->getLabel('name'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('name'); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?php echo $this->form->getLabel('bg_state'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('bg_state'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?php echo $this->form->getLabel('bg_eps_file'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('bg_eps_file'); ?>
			</div>
		</div>

		<div class="control-group" id="isProductionBgContainer">
			<label class="control-label">
				<?php echo $this->form->getLabel('isProductionBg'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('isProductionBg'); ?>
			</div>
		</div>

		<div class="control-group" id="isPreviewBgContainer">
			<div class="control-label">
				<?php echo $this->form->getLabel('isPreviewBg'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('isPreviewBg'); ?>
			</div>
		</div>

		<div class="control-group" id="isDefaultPreviewContainer">
			<div class="control-label">
				<?php echo $this->form->getLabel('isDefaultPreview'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('isDefaultPreview'); ?>
			</div>
		</div>

		<div class="control-group" id="useCheckerboardContainer">
			<div class="control-label">
				<?php echo $this->form->getLabel('useCheckerboard'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('useCheckerboard'); ?>
			</div>
		</div>

		<div class="control-group previewbg" style="display: none">
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
		<input type="button" class="btn btn-success" id="bgFormSubmit" value="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>"/>
		<input type="button" class="btn" id="cancelBgBtn" value="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>"/>
	</div>

</form>

<iframe name="bgPostIframe" id="bgPostIframe" style="display: none" ></iframe>
