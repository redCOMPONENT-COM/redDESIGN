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

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->item->designtype_id . '&tab=backgrounds';

?>

<div>&nbsp;</div>

<div id="bgFormContainer" class="well">
	<form action="index.php?option=com_reddesign&task=background.ajaxBackgroundSave" id="bgForm" name="adminForm"
	      method="POST" enctype="multipart/form-data" class="form-horizontal">

		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<input type="hidden" name="returnurl" value="<?php echo base64_encode($return_url); ?>" />
		<input type="hidden" name="jform[designtype_id]" id="background_reddesign_designtype_id" value="<?php echo $this->item->designtype_id; ?>" />
		<input type="hidden" name="jform[id]" id="background_id" value="" />

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
					<?php echo $this->form->getLabel('bg_svg_file'); ?>
				</label>
				<div class="controls">
					<?php echo $this->form->getInput('bg_svg_file'); ?>
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

		</div>

		<div class="form-actions">
			<button class="btn btn-success" id="bgFormSubmit">
				<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
			</button>
			<button class="btn" type="button" id="cancelBgBtn">
				<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
			</button>
		</div>

	</form>
</div>
<iframe name="bgPostIframe" id="bgPostIframe" style="display: none;" ></iframe>

<script type="text/javascript">
	jQuery(document).ready(
		function ($) {
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
				form.submit();

				jQuery("#bgPostIframe").load(function () {
					iframeContents = jQuery("#bgPostIframe")[0].contentWindow.document.body.innerHTML;
					var jsonData = jQuery.parseJSON(iframeContents);

					if (jsonData[0] == 0)
					{
						jQuery("#bgMessage").html(jsonData[1]);
					}
					else
					{
						window.location.href="<?php echo $return_url; ?>";
					}
				});

				return false;
			});

			jQuery(document).on('click', '#cancelBgBtn',
				function () {
					jQuery('#addBgBtn').val("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_HIDE_FORM'); ?>")
					hideShowBackgroundForm();
				}
			);

			jQuery(document).on('click', '#addBgBtn', function () {
					hideShowBackgroundForm();
				}
			);
		}
	);

	/**
	 * Hides background form.
	 *
	 * @return void
	 */
	function hideShowBackgroundForm() {
		var buttonValue = jQuery('#addBgBtn').val();
		var hidden = "<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_SHOW_FORM'); ?>";
		var shown = "<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_HIDE_FORM'); ?>";

		if (buttonValue == hidden)
		{
			jQuery('#addBgBtn').val("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_HIDE_FORM'); ?>");
			jQuery('#bgFormContainer').fadeIn("slow");
			jQuery("#saveBgBtn").val(shown);
		}
		else
		{
			document.getElementById("bgForm").reset();
			jQuery("#bgFormContainer").fadeOut("fast");
			jQuery('#addBgBtn').val(hidden);
		}
	}
</script>