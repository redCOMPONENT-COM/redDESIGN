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
	<form id="background" name="background" method="post" action="index.php">
		<input type="hidden" value="com_reddesign" name="option">
		<input type="hidden" value="backgrounds" name="view">
		<input type="hidden" value="browse" name="task">
		<input type="hidden" value="" name="boxchecked">
		<input type="hidden" value="" name="hidemainmenu">
		<input type="hidden" value="id" name="filter_order">
		<input type="hidden" value="DESC" name="filter_order_Dir">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<table id="itemsList" class="table table-striped">
			<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
				</th>
				<th>
					<?php echo JText::_('JGLOBAL_TITLE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_EPS_FILE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_PRODUCTION_BACKGROUND'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('JGLOBAL_PUBLISHED'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($count = count($this->backgrounds)): ?>
				<?php $i = -1;
				$m = 1; ?>
				<?php foreach ($this->backgrounds as $background) : ?>
					<?php
					$i++;
					$m = 1 - $m;
					$background->published = $background->enabled;
					$ordering = $this->lists->order == 'ordering';
					?>
					<tr class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $background->reddesign_background_id; ?>
						</td>
						<td align="left">
							<a class="modal"
							   href="index.php?option=com_reddesign&view=background&id=<?php echo $background->reddesign_background_id ?>">
								<strong><?php echo $this->escape(JText::_($background->title)) ?></strong>
							</a>
						</td>
						<td>
							<?php echo $background->eps_file; ?>
						</td>
						<td align="center" width="9%">
							@ToDo
						</td>
						<td align="center" width="9%">
							<?php echo JHTML::_('grid.published', $background, $i); ?>
						</td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="3">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>

		<div class="well">
			<input type="button" class="btn btn-primary" id="addBkBtn" value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_ADD'); ?>" />
		</div>
		<div id="uploadBkForm" class="well" style="display:none;">
			<?php echo $this->loadTemplate('backgroundupload'); ?>
		</div>
		<script type="text/javascript">
			akeeba.jQuery(document).ready(
				function()
				{
					akeeba.jQuery(document).on('click', '#addBkBtn', function()
						{
							showBackgroundForm()
						}
					);
				});

			function showBackgroundForm() {
				akeeba.jQuery('#addBkBtn').parent().hide();
				akeeba.jQuery('#uploadBkForm').fadeIn("slow");
			}
		</script>
	</form>
</div>