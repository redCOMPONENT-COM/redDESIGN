<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal', 'a.jmodal');

if (isset($displayData) && (count($displayData->items) > 0))
{
	$this->items = $displayData->items;
}

?>

<div class="form-container">
	<form id="backgrounds_form" name="backgrounds" method="post" action="index.php">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<input type="hidden" name="option" value="com_reddesign">
		<input type="hidden" name="view" value="background">
		<input type="hidden" name="task" id="backgrounds_task" value="">
		<input type="hidden" name="reddesign_background_id" id="backgrounds_reddesign_background_id" value="">
		<input type="hidden" name="reddesign_designtype_id" id="backgrounds_reddesign_designtype_id" value="<?php /*echo $this->item->id;*/ ?>">
		<table id="itemsList" class="table table-striped">
			<thead>
			<tr>
				<th width="9%">
					<?php echo JText::_('ID'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_EPS_FILE'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_DEFAULT_PREVIEW_BACKGROUND'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PRODUCTION_BACKGROUND'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PREVIEW_BACKGROUND'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="115">
					<?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($count = count($this->items)) : ?>
				<?php
				$i = -1;
				$m = 1;
				?>
				<?php foreach ($this->items as $background) : ?>
					<?php
					$i++;
					$m = 1 - $m;
					$background->published = $background->state;
					?>
					<tr class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $background->id; ?>
						</td>
						<td align="left">
							<a id="editBackground<?php echo $background->id; ?>" class="editBackground" href="#">
								<strong><?php echo $background->name; ?></strong>
							</a>
							&nbsp;
							<a class="jmodal btn btn-mini"
							   href="<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $background->image_path; ?>">
								<?php echo JText::_('COM_REDDESIGN_COMMON_PREVIEW'); ?>
							</a>
						</td>
						<td align="center" width="9%" class="switchBg">
							<?php if (!$background->isDefaultPreview) : ?>
								<a class="jgrid" href="javascript:void(0);" onclick="setPreviewbg('<?php echo $background->id; ?>')" >
									<span class="state notdefault">
										<span class="text">Default</span>
									</span>
								</a>
							<?php else : ?>
								<a class="jgrid">
									<span class="state default">
										<span class="text">Default</span>
									</span>
								</a>
							<?php endif; ?>
						</td>
						<td align="center" width="9%" class="switchBg">
							<?php if (!$background->isProductionBg) : ?>
								<a class="jgrid" href="javascript:void(0);" onclick="setProductionFileBg('<?php echo $background->id; ?>')" >
									<span class="state notdefault">
										<span class="text">Default</span>
									</span>
								</a>
							<?php else : ?>
								<a class="jgrid">
									<span class="state default">
										<span class="text">Default</span>
									</span>
								</a>
							<?php endif; ?>
						</td>
						<td align="center" width="9%">
							<?php echo JHTML::_('grid.published', $background->isPreviewBg, $i); ?>
						</td>
						<td align="center" width="9%">
							<?php echo JHTML::_('grid.published', $background, $i); ?>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-mini" onclick="removeBg('<?php echo $background->id; ?>')" >
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>
					<tr class="hide">
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready(
		function ($) {
			// Selects background for edit and populates field data accordingly.
			<?php foreach ($this->items as $background) : ?>
				jQuery(document).on("click", "#editBackground<?php echo $background->id; ?>", function() {
					jQuery("#backgroundTitle").html("<?php echo JText::_('COM_REDDESIGN_TITLE_BACKGROUNDS_EDIT'); ?>");
					jQuery("#reddesign_background_id").val("<?php echo $background->id; ?>");
					jQuery("#jform_bg_name").val("<?php echo $background->name; ?>");

					// State field
					jQuery("#jform_bg_state label").attr("class", "btn");
					var stateObj = jQuery("input[name='jform[bg_state]'][value='" + <?php echo $background->state; ?> + "']");
					stateObj.prop('checked', true);
					if (stateObj.val() == 1)
					{
						jQuery("#jform_bg_state label[for='" + stateObj.attr('id') + "']").addClass("active btn-success");
					}
					else
					{
						jQuery("#jform_bg_state label[for='" + stateObj.attr('id') + "']").addClass("active btn-danger");
					}

					// Production Background field
					<?php
						if ($background->isProductionBg)
						{
							$isProductionBgChecked = 'true';
						}
						else
						{
							$isProductionBgChecked = 'false';
						}
					?>
					jQuery("#jform_isProductionBg").prop("checked", <?php echo $isProductionBgChecked; ?>);

					// Preview Background field
					<?php
						if ($background->isPreviewBg)
						{
							$isPreviewBgChecked = 'true';
						}
						else
						{
							$isPreviewBgChecked = 'false';
						}
					?>
					jQuery("#jform_isPreviewBg").prop("checked", <?php echo $isPreviewBgChecked; ?>);

					// Default Preview field
					<?php
						if ($background->isDefaultPreview)
						{
							$isDefaultPreviewChecked = 'true';
						}
						else
						{
							$isDefaultPreviewChecked = 'false';
						}
					?>
					jQuery("#jform_isDefaultPreview").prop("checked", <?php echo $isDefaultPreviewChecked; ?>);

					if (jQuery("#jform_isPreviewBg").is(":checked"))
					{
						jQuery("#isDefaultPreviewContainer").show();
						jQuery("#useCheckerboardContainer").show();
					}
					else
					{
						jQuery("#isDefaultPreviewContainer").hide();
						jQuery("#useCheckerboardContainer").hide();
					}

					jQuery("#BgThumbnailLink")
						.attr("href", "<?php echo JURI::root() . 'com_reddesign/backgrounds/thumbnails/' . $background->thumbnail; ?>")
						.text("<?php echo $background->thumbnail; ?>");

					jQuery("#saveBgBtn").val("<?php echo JText::_('COM_REDDESIGN_COMMON_UPDATE'); ?>");

					jQuery('#addBgBtn').parent().hide();
					jQuery('#backgroundForm').fadeIn("slow");

					jQuery('body').animate({
						'scrollTop':   jQuery('#backgroundForm').offset().top
					}, 1000);
				});
			<?php endforeach ?>

			jQuery(document).on('click', '#addBgBtn', function () {
					jQuery('#addBgBtn').parent().hide();
					jQuery('#backgroundForm').fadeIn("slow");
					jQuery("#saveBgBtn").val("<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>");
				}
			);
		}
	);

	/**
	 *  Set a background as PDF production file background
	 */
	function setProductionFileBg(bgid) {
		var backgrounds_reddesign_background_id;

		backgrounds_reddesign_background_id = bgid;

		jQuery('#backgrounds_task').val('setProductionFileBg');
		jQuery('#backgrounds_reddesign_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}

	/**
	 *  Set a background as preview file background
	 */
	function setPreviewbg(bgid) {
		var backgrounds_reddesign_background_id;

		backgrounds_reddesign_background_id = bgid;

		jQuery('#backgrounds_task').val('setPreviewBg');
		jQuery('#backgrounds_reddesign_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}


	/**
	 *  Removes a background form activation
	 */
	function removeBg(bgid) {
		jQuery('#backgrounds_task').val('remove');
		<?php $return_url_removeBg = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' /*. $this->item->id*/ . '&tab=backgrounds'; ?>
		jQuery('#backgrounds_form').
			append(
				jQuery('<input/>')
					.attr('type', 'hidden')
					.attr('name', 'returnurl')
					.val('<?php echo base64_encode($return_url_removeBg) ?>')
			);
		jQuery('#backgrounds_reddesign_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}

	/**
	 * Saves background form activation
	 */
	function modifyBg(bgid) {
		jQuery('#backgrounds_task').val('save');
		<?php $return_url_removeBg = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' /*. $this->item->id*/ . '&tab=backgrounds'; ?>
		jQuery('#backgrounds_form').
			append(
				jQuery('<input/>')
					.attr('type', 'hidden')
					.attr('name', 'returnurl')
					.val('<?php echo base64_encode($return_url_removeBg) ?>')
			);
		jQuery('#backgrounds_reddesign_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}
</script>