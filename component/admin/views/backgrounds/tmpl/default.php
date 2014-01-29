<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal', 'a.jmodal');

// Set variables for using them in HMVC. For regular MVC $displayData can not be used.
$this->items = $displayData->items;
$this->designtype_id = $displayData->designtype_id;

// Preview and unit configuration
$config = ReddesignEntityConfig::getInstance();
$bgBackendPreviewWidth = $config->getMaxSVGPreviewAdminWidth();

$returnUrl = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->designtype_id . '&tab=backgrounds';
?>

<form id="backgrounds_form" name="backgrounds" method="post" action="index.php?option=com_reddesign&view=backgrounds">

	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="task" id="backgrounds_task" value="">
	<input type="hidden" name="cid[]" id="backgrounds_background_id" value="">
	<input type="hidden" name="designtype_id" id="backgrounds_designtype_id" value="<?php echo $this->designtype_id; ?>">
	<input type="hidden" name="return" id="backgrounds_return" value="<?php echo base64_encode($returnUrl); ?>" />
	<input type="hidden" name="returnurl" id="backgrounds_return2" value="<?php echo base64_encode($returnUrl); ?>" />

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDDESIGN_COMMON_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table id="itemsList" class="table table-striped">
			<thead>
				<tr>
					<th class="th-width-9">
						<?php echo JText::_('ID'); ?>
					</th>
					<th class="th-width-40">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_SVG_FILE'); ?>
					</th>
					<th class="th-width-9">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_DEFAULT_PREVIEW_BACKGROUND'); ?>
					</th>
					<th class="th-width-9">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PRODUCTION_BACKGROUND'); ?>
					</th>
					<th class="th-width-9">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PREVIEW_BACKGROUND'); ?>
					</th>
					<th class="th-width-9">
						<?php echo JText::_('JPUBLISHED'); ?>
					</th>
					<th class="td-align-center">
						<?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?>
					</th>
				</tr>
			</thead>
			<tbody>

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
						<td class="td-align-center">
							<?php echo $background->id; ?>
						</td>
						<td class="td-align-left">
							<a id="editBackground<?php echo $background->id; ?>" class="editBackground" href="#">
								<strong><?php echo $background->name; ?></strong>
							</a>
							&nbsp;
							<a class="jmodal btn btn-mini" href="#modalPreview<?php echo $background->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_COMMON_PREVIEW'); ?>
							</a>
							<div style="height: 100%;width: 100%;left: -2000px;position: absolute;">
								<div class="progressbar-holder" style="width: <?php echo $bgBackendPreviewWidth - 15; ?>px; margin-bottom:20px;">
									<div class="progress progress-striped" style="display:none;">
										<div class="bar bar-success" style="width: 0%; text-align: center;"></div>
									</div>
								</div>

								<div id="modalPreview<?php echo $background->id; ?>">
									<svg id="bgPreviewSvg<?php echo $background->id; ?>" width="600px" height="450px"></svg>
								</div>
							</div>
						</td>
						<td class="switchBg td-align-center">
							<?php if (!$background->isDefaultPreview) : ?>
								<a class="jgrid" href="javascript:void(0);" onclick="setPreviewbg('<?php echo $background->id; ?>')" >
									<span class="state notdefault">
										<span class="text"><?php echo JText::_('JDEFAULT'); ?></span>
									</span>
								</a>
							<?php else : ?>
								<a class="jgrid">
									<span class="state default">
										<span class="text"><?php echo JText::_('JDEFAULT'); ?></span>
									</span>
								</a>
							<?php endif; ?>
						</td>
						<td class="switchBg td-align-center">
							<?php if (!$background->isProductionBg) : ?>
								<a class="jgrid" href="javascript:void(0);" onclick="setProductionFileBg('<?php echo $background->id; ?>')" >
									<span class="state notdefault">
										<span class="text"><?php echo JText::_('JDEFAULT'); ?></span>
									</span>
								</a>
							<?php else : ?>
								<a class="jgrid">
									<span class="state default">
										<span class="text"><?php echo JText::_('JDEFAULT'); ?></span>
									</span>
								</a>
							<?php endif; ?>
						</td>
						<td class="td-align-center">
							<?php echo JHTML::_('grid.published', $background->isPreviewBg, $i); ?>
						</td>
						<td class="td-align-center">
							<?php echo JHTML::_('grid.published', $background, $i); ?>
						</td>
						<td class="td-align-center">
							<button type="button" class="btn btn-danger btn-mini" onclick="removeBg('<?php echo $background->id; ?>')" >
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>
					<tr class="hide">
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</form>

<script type="text/javascript">
	jQuery(document).ready(
		function ($) {

			// Hide form if there are items in the list.
			<?php if (!empty($this->items)) : ?>
				hideShowBackgroundForm();
			<?php endif; ?>

			<?php foreach ($this->items as $background) : ?>
			var snap<?php echo $background->id ?> = Snap("#bgPreviewSvg<?php echo $background->id; ?>");

			jQuery.ajax({
				url: "<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $background->svg_file; ?>",
				dataType: "text",
				xhrFields: {
					onprogress: function (e) {
						if (e.lengthComputable) {
							var loadedPercentage = e.loaded / e.total * 100;
							$('#modalPreview<?php echo $background->id; ?> .progress .bar-success')
								.css('width', '' + (loadedPercentage) + '%')
								.html(loadedPercentage + '% <?php echo JText::_('COM_REDDESIGN_COMMON_PROGRESS_LOADED', true); ?>');
						}
					}
				},
				beforeSend: function (xhr) {
					jQuery('#modalPreview<?php echo $background->id; ?> .progress').show().addClass('active');
					jQuery('#modalPreview<?php echo $background->id; ?> .progress .bar-success').css('width', '0%');
				},
				success: function (response) {
					jQuery('#modalPreview<?php echo $background->id; ?> .progress').removeClass('active');
					if(typeof response === 'undefined' || response == false){
						jQuery('#modalPreview<?php echo $background->id; ?> .progress')
							.append('<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#modalPreview<?php echo $background->id; ?> .progress .bar-success').css('width'))) + '%;"></div>');
					}
					else{
						jQuery('#modalPreview<?php echo $background->id; ?> .progressbar-holder').hide();
					}

					<?php if ($background->useCheckerboard) : ?>
					var checkerbox = Snap.parse('<?php echo ReddesignHelpersSvg::getSVGCheckerboard(600, 450); ?>');
					snap<?php echo $background->id ?>.append(checkerbox);
					<?php endif; ?>
					snap<?php echo $background->id ?>.append(Snap.parse(response));

					var svgLoaded = jQuery("#bgPreviewSvg<?php echo $background->id; ?>").find("svg")
						.attr("width", "600px")
						.attr("height", "450px");
				}
			});

			// Selects background for edit and populates field data accordingly.
				jQuery(document).on("click", "#editBackground<?php echo $background->id; ?>", function() {
					jQuery("#backgroundTitle").html("<?php echo JText::_('COM_REDDESIGN_TITLE_BACKGROUNDS_EDIT'); ?>");
					jQuery("#background_id").val("<?php echo $background->id; ?>");
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

					// Use CheckerBoard field
					<?php
						if ($background->useCheckerboard)
						{
							$useCheckerboardChecked = 'true';
						}
						else
						{
							$useCheckerboardChecked = 'false';
						}
					?>
					jQuery("#jform_useCheckerboard").prop("checked", <?php echo $useCheckerboardChecked; ?>);

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

					jQuery('#bgFormContainer').fadeIn("slow");
					jQuery("#saveBgBtn").val("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_HIDE_FORM'); ?>");
				});
			<?php endforeach ?>

			jQuery("#saveBgBtn").val("<?php echo JText::_('COM_REDDESIGN_COMMON_UPDATE'); ?>");

			jQuery('#backgroundForm').fadeIn("slow");

			jQuery('body').animate({'scrollTop': jQuery('#backgroundForm').offset().top}, 1000);
		}
	);

	/**
	 *  Set a background as PDF production file background
	 */
	function setProductionFileBg(bgid) 
	{
		jQuery('#backgrounds_task').val('backgrounds.setProductionFileBg');
		jQuery('#backgrounds_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}

	/**
	 *  Set a background as preview file background
	 */
	function setPreviewbg(bgid)
	{
		jQuery('#backgrounds_task').val('backgrounds.setPreviewBg');
		jQuery('#backgrounds_background_id').val(bgid);
		jQuery('#backgrounds_form').submit();
	}


	/**
	 * Removes a background
	 *
	 * @param  bgid  int  Background ID
	 *
	 * @return void
	 */
	function removeBg(bgid)
	{
		jQuery('#backgrounds_task').val('backgrounds.delete');
		jQuery('#backgrounds_background_id').val(bgid);

		jQuery('#backgrounds_form').submit();
	}

	/**
	 * Saves background form activation
	 */
	function modifyBg(bgid) {
		jQuery('#backgrounds_task').val('save');
		jQuery('#backgrounds_background_id').val(bgid);

		jQuery('#backgrounds_form').submit();
	}
</script>
