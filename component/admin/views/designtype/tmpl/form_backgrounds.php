<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal');
?>

<div class="form-container">
	<div class="well">
		<input type="button" class="btn btn-primary" id="addBgBtn"
			   value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_ADD'); ?>"/>
	</div>
	<div id="uploadBgForm" class="well" style="display:none;">
		<?php echo $this->loadTemplate('backgroundupload'); ?>
	</div>
	<script type="text/javascript">
		akeeba.jQuery(document).ready(
			function () {
				akeeba.jQuery(document).on('click', '#addBgBtn', function () {
						showBackgroundForm()
					}
				);
			});

		function showBackgroundForm() {
			akeeba.jQuery('#addBgBtn').parent().hide();
			akeeba.jQuery('#uploadBgForm').fadeIn("slow");
		}
	</script>
	<form id="backgrounds_form" name="backgrounds" method="post" action="index.php">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<input type="hidden" name="option" value="com_reddesign">
		<input type="hidden" name="view" value="background">
		<input type="hidden" name="task" id="backgrounds_task" value="">
		<input type="hidden" name="reddesign_background_id"
			   id="backgrounds_reddesign_background_id"
			   value="">
		<input type="hidden" name="reddesign_designtype_id"
			   id="backgrounds_reddesign_designtype_id"
			   value="<?php echo $this->item->reddesign_designtype_id; ?>">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
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
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PRODUCTION_BACKGROUND'); ?>
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
			<?php if ($count = count($this->backgrounds)) : ?>
				<?php $i = -1;
				$m       = 1; ?>
				<?php foreach ($this->backgrounds as $background) : ?>
					<?php
					$i++;
					$m                     = 1 - $m;
					$background->published = $background->enabled;
					?>
					<tr class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $background->reddesign_background_id; ?>
						</td>
						<td align="left">
							<a href="#">
								<strong><?php echo $this->escape(JText::_($background->title)) ?></strong>
							</a>&nbsp;<a class="modal btn btn-mini"
							   href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/' . $background->image_path); ?>">
								<?php echo JText::_('COM_REDDESIGN_BACKGROUNDS_THUMB_PREVIEW'); ?>
							</a>
						</td>
						<td align="center" width="9%" class="switchBg">
							<?php if (!$background->isPDFbgimage) : ?>
								<a class="jgrid" href="javascript:void(0);"
									onclick="setPDFbg('<?php echo $background->reddesign_background_id; ?>')" >
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
							<?php echo JHTML::_('grid.published', $background, $i); ?>
						</td>
						<td>
							<button type="button"
									class="btn btn-danger delete btn-mini"
									onclick="removeBg('<?php echo $background->reddesign_background_id; ?>')" >
								<i class="icon-minus icon-white"></i>
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>
				<?php endforeach ?>
			<?php else : ?>
				<tr>
					<td colspan="5">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
	function setPDFbg(bgid) {
		var backgrounds_reddesign_background_id;

		backgrounds_reddesign_background_id = bgid;

		akeeba.jQuery('#backgrounds_task').val('setPDFbg');
		akeeba.jQuery('#backgrounds_reddesign_background_id').val(bgid);
		akeeba.jQuery('#backgrounds_form').submit();
	}
	function removeBg(bgid) {
		akeeba.jQuery('#backgrounds_task').val('remove');
		<?php $return_url_removeBg = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->reddesign_designtype_id . '&tab=backgrounds'; ?>
		akeeba.jQuery('#backgrounds_form').
			append(
				akeeba.jQuery('<input/>')
					.attr('type', 'hidden')
					.attr('name', 'returnurl')
					.val('<?php echo base64_encode($return_url_removeBg) ?>')
			);
		akeeba.jQuery('#backgrounds_reddesign_background_id').val(bgid);
		akeeba.jQuery('#backgrounds_form').submit();
	}
</script>