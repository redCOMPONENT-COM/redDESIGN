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
		<input type="button" class="btn btn-primary" id="addPartBtn" value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PARTS_ADD'); ?>"/>
	</div>
	<div id="partForm" class="well" style="display:none;">
		<?php echo $this->loadTemplate('part'); ?>
	</div>
	<form id="partsForm" name="partsForm" method="post" action="index.php">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<input type="hidden" name="option" value="com_reddesign">
		<input type="hidden" name="view" value="part">
		<input type="hidden" name="task" id="part_task" value="">
		<input type="hidden" name="reddesign_part_id" id="parts_reddesign_part_id" value="">
		<input type="hidden" name="reddesign_designtype_id" id="parts_reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>">
		<table id="itemsList" class="table table-striped">
			<thead>
			<tr>
				<th>
					<?php echo JText::_('ID'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_TITLE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_THUMB'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_STOCK'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PART_PRICE'); ?>
				</th>
				<th>
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($count = count($this->parts)) : ?>
				<?php
				$i = -1;
				$m = 1;
				?>
				<?php foreach ($this->parts as $part) : ?>
					<?php
					$i++;
					$m = 1 - $m;
					$part->published = $part->enabled;
					?>
					<tr class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $part->reddesign_part_id; ?>
						</td>
						<td>
							<a href="#" class="editPart" onclick="selectPartForEdit()">
								<strong><?php echo $part->title; ?></strong>
							</a>
							&nbsp;
							<a class="modal btn btn-mini" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/parts/' . $part->part_image); ?>">
								<?php echo JText::_('COM_REDDESIGN_COMMON_PREVIEW'); ?>
							</a>
						</td>
						<td>
							<img src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/parts/' . $part->part_image); ?>" alt="" />
						</td>
						<td>
							<?php echo $part->stock; ?>
						</td>
						<td>
							<?php echo $part->price; ?>
						</td>
						<td>
							<?php echo JHTML::_('grid.published', $part, $i); ?>
						</td>
						<td>
							<button type="button" class="btn btn-danger delete btn-mini" onclick="removePart('<?php echo $part->reddesign_part_id; ?>')" >
								<i class="icon-minus icon-white"></i>
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>
					<tr class="hide">
					</tr>
				<?php endforeach ?>
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
	/**
	 *  Shows the part form
	 */
	function showPartForm() {
		akeeba.jQuery('#addPartBtn').parent().hide();
		akeeba.jQuery('#partForm').fadeIn("slow");
	}

	/**
	 *  Removes a background form activation
	 */
	function removePart(partid) {
		akeeba.jQuery('#part_task').val('remove');
		<?php $returnUrlRemovePart = JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->reddesign_designtype_id . '&tab=parts'; ?>
		akeeba.jQuery('#parts_form').
			append(
				akeeba.jQuery('<input/>')
					.attr('type', 'hidden')
					.attr('name', 'returnurl')
					.val('<?php echo base64_encode($returnUrlRemovePart) ?>')
			);
		akeeba.jQuery('#parts_reddesign_background_id').val(partid);
		akeeba.jQuery('#parts_form').submit();
	}

	/**
	 * Selects background for edit and populates field data accordingly
	 */
	function selectPartForEdit(reddesign_background_id, title, isPDFbgimage, enabled) {
		akeeba.jQuery("#reddesign_background_id").val(reddesign_background_id);
		akeeba.jQuery("#bg_title").val(title);
		akeeba.jQuery("#bg_isPDFbgimage").val(isPDFbgimage);
		akeeba.jQuery("#bg_enabled").val(enabled);
		showBackgroundForm()
		akeeba.jQuery("body").animate({
			'scrollTop':   akeeba.jQuery("#backgroundForm").offset().top
		}, 1000);
	}

	/**
	 * Saves background form activation
	 */
	function modifyPart(partid) {
		akeeba.jQuery('#backgrounds_task').val('save');
		akeeba.jQuery('#backgrounds_form').
			append(
				akeeba.jQuery('<input/>')
					.attr('type', 'hidden')
					.attr('name', 'returnurl')
					.val('<?php echo base64_encode(JURI::base() . 'index.php?option=com_reddesign&view=designtype&id=' . $this->item->reddesign_designtype_id . '&tab=backgrounds'); ?>')
			);
		akeeba.jQuery('#backgrounds_reddesign_background_id').val(partid);
		akeeba.jQuery('#backgrounds_form').submit();
	}

	/**
	 * Add behaviour to add Background button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#addPartBtn', function () {
					showPartForm();
				}
			);
		});
</script>