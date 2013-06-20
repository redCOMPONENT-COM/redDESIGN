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
<p>
	<a class="modal btn btn-primary" href="index.php?option=com_reddesign&view=background&tmpl=component&designtype=<?php echo $this->item->reddesign_designtype_id; ?>">Add</a>
</p>

<div class="form-container">
	<form id="background" name="background" method="post" action="index.php">
		<input type="hidden" value="com_reddesign" name="option">
		<input type="hidden" value="images" name="view">
		<input type="hidden" value="browse" name="task">
		<input type="hidden" value="" name="boxchecked">
		<input type="hidden" value="" name="hidemainmenu">
		<input type="hidden" value="id" name="filter_order">
		<input type="hidden" value="DESC" name="filter_order_Dir">
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
		<table id="itemsList" class="table table-striped">
			<thead>
			<tr>
				<th width="8%">
					<?php echo JHTML::_('grid.sort', 'JFIELD_ORDERING_LABEL', 'ordering', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					<?php echo JHTML::_('grid.order', $this->backgrounds); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'title', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
				</th>
				<th width="9%">
					<?php echo JHTML::_('grid.sort', 'JPUBLISHED', 'enabled', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
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
						<td class="order" align="center" width="8%">or
						</td>
						<td>
							<?php echo $background->reddesign_background_id; ?>
						</td>
						<td align="left">
							<a class="modal"
							   href="index.php?option=com_reddesign&view=background&id=<?php echo $background->reddesign_background_id ?>">
								<strong><?php echo $this->escape(JText::_($background->title)) ?></strong>
							</a>
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
	</form>
</div>