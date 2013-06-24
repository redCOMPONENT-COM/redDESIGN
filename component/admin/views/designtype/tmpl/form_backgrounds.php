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
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_REMOVE'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php if ($count = count($this->backgrounds)): ?>
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
						<a class="modal"
						   href="/media/com_reddesign/assets/backgrounds/<?php echo $background->image_path; ?>">
							<strong><?php echo $this->escape(JText::_($background->title)) ?></strong>
						</a>
					</td>
					<td align="center" width="9%">
						@ToDo
					</td>
					<td align="center" width="9%">
						<?php echo JHTML::_('grid.published', $background, $i); ?>
					</td>
					<td>
						<?php // @ToDo: this delete background feature still needs to be implemented. It wil also have to remove areas, fonts and colors ?>
						<button type="button" class="btn btn-danger delete">
							<i class="icon-trash icon-white"></i>
							<span><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_REMOVE'); ?></span>
						</button>
					</td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="5">
					<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>