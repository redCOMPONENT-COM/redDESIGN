<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

?>

<div class="form-container">
	<form id="adminForm" name="adminForm" method="post" action="index.php">
		<input type="hidden" value="com_reddesign" name="option">
		<input type="hidden" value="designs" name="view">
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
					<?php echo JHTML::_('grid.order', $this->items); ?>
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
			<tr>
				<td colspan="3">
					<input type="text" name="search" id="search"
					       value="<?php echo $this->escape($this->getModel()->getState('search', '')); ?>"
					       class="input-medium" onchange="document.adminForm.submit();"
					       placeholder="<?php echo JText::_('JGLOBAL_TITLE') ?>"
						/>
					<nobr>
						<button class="btn btn-mini" onclick="this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER'); ?>
						</button>
						<button class="btn btn-mini" onclick="document.adminForm.search.value='';this.form.submit();">
							<?php echo JText::_('JSEARCH_RESET'); ?>
						</button>
					</nobr>
				</td>
				<td>
					<?php ?>
				</td>
			</tr>
			</thead>
			<tbody>
			<?php if ($count = count($this->items)): ?>
				<?php $i = -1;
				$m = 1; ?>
				<?php foreach ($this->items as $item) : ?>
					<?php
					$i++;
					$m = 1 - $m;
					$item->published = $item->enabled;
					$ordering = $this->lists->order == 'ordering';
					?>
					<tr class="<?php echo 'row' . $m; ?>">
						<td class="order" align="center" width="8%">
							<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $ordering); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $count, true, 'orderdown', 'Move Down', $ordering); ?></span>
							<?php $disabled = $ordering ? '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5"
							       value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text_area"
							       style="text-align: center"/>
						</td>
						<td>
							<?php echo JHTML::_('grid.id', $i, $item->reddesign_design_id, false); ?>
						</td>
						<td align="left">
							<a href="index.php?option=com_reddesign&view=design&id=<?php echo $item->reddesign_design_id ?>">
								<strong><?php echo $this->escape(JText::_($item->title)) ?></strong>
							</a>

							<p class="smallsub">
								(<span><?php echo $this->escape($item->slug) ?></span>)
							</p>
						</td>

						<td align="center" width="9%">
							<?php echo JHTML::_('grid.published', $item, $i); ?>
						</td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="4">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php if ($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>