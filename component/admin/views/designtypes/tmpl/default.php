<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('rjquery.select2', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'd.ordering' && $listDirn == 'asc');
$search = $this->state->get('filter.search');
$originalOrders = array();
$user = JFactory::getUser();
$userId = $user->id;

if ($saveOrder) :
	JHTML::_('rsortablelist.sortable', 'table-items', 'adminForm', strtolower($listDirn), 'index.php?option=com_reddesign&task=designtypes.saveOrderAjax&tmpl=component', true, true);
endif;
?>
<form action="index.php?option=com_reddesign&view=designtypes" method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_REDDESIGN_COMMON_FILTER'); ?>
			<?php echo RLayoutHelper::render('search', array('view' => $this)) ?>
		</div>
	</div>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDDESIGN_COMMON_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table class="table table-striped" id="table-items">
			<thead>
				<tr>
					<th width="30" align="center">
						<?php echo '#'; ?>
					</th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					</th>
					<th width="1" align="center">
					</th>
					<th>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_DESIGNTYPES_NAME', 'd.title', $listDirn, $listOrder);?>
					</th>
					<?php if ($search == ''): ?>
					<th width='8%'>
						<?php echo JHTML::_('rgrid.sort', 'COM_REDDESIGN_DESIGNTYPES_ORDERING', 'd.ordering', $listDirn, $listOrder); ?>
					</th>
					<?php endif; ?>
					<th width='5%'>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_DESIGNTYPES_FIELD_ENABLED', 'd.published', $listDirn, $listOrder);?>
					</th>
					<th width='5%'>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_COMMON_ID', 'd.reddesign_designtype_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				foreach ($this->items as $i => $row) :
					$orderkey = array_search($row->reddesign_designtype_id, $this->ordering[0]);
					$parentsStr = '';
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td><?php echo JHtml::_('grid.id', $i, $row->reddesign_designtype_id); ?></td>
						<td></td>
						<td>
							<?php
							if ($row->checked_out)
							{
								$editor = JFactory::getUser($row->checked_out);
								$canCheckin = $row->checked_out == $userId || $row->checked_out == 0;
								echo JHtml::_('rgrid.checkedout', $i, $editor->name, $row->checked_out_time, 'designtypes.', $canCheckin);
								echo $row->title;
							}
							else
							{
								echo JHtml::_(
												'link',
												JRoute::_('index.php?option=com_reddesign&task=designtype.edit&reddesign_designtype_id=' . $row->reddesign_designtype_id),
												$row->title
								);
							}
							?>
						</td>
						<?php if ($search == ''): ?>
						<td class="order nowrap center">
							<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive' ;?>" title="<?php echo ($saveOrder) ? '' :JText::_('COM_REDDESIGN_DESIGNTYPES_ORDERING_DISABLED');?>"><i class="icon-move"></i></span>
							<input type="text" style="display:none" name="order[]" value="<?php echo $orderkey + 1;?>" class="text-area-order" />
						</td>
						<?php endif; ?>
						<td>
							<?php echo JHtml::_('rgrid.published', $row->published, $i, 'designtypes.', true, 'cb'); ?>
						</td>
						<td>
							<?php echo $row->reddesign_designtype_id;?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
