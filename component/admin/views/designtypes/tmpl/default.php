<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrderingUrl = 'index.php?option=com_reddesign&task=designtypes.saveOrderAjax&tmpl=component';
$disableClassName = '';
$disabledLabel = '';

if ($listOrder == 'd.ordering')
{
	JHtml::_('rsortablelist.sortable', 'designtypesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
else
{
	$disabledLabel = JText::_('COM_REDDESIGN_COMMON_ORDERING_DISABLED');
	$disableClassName = 'inactive tip-top';
}
?>

<form action="index.php?option=com_reddesign&view=designtypes" method="post" id="adminForm" name="adminForm">
	<?php echo JText::_('COM_REDDESIGN_COMMON_FILTER'); ?>
	<?php
		echo RLayoutHelper::render(
			'searchtools.default',
			array(
				'view' => $this,
				'options' => array(
					'filterButton' => false,
					'searchField' => 'search_designtypes',
					'searchFieldSelector' => '#filter_search_designtypes',
					'limitFieldSelector' => '#list_search_designtypes',
					'activeOrder' => $listOrder,
					'activeDirection' => $listDirn
				)
			)
		);
	?>
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
					<th>
						<?php echo JText::_('COM_REDDESIGN_COMMON_NUM'); ?>
					</th>
					<th class="nowrap center hidden-phone">
						<?php
						echo JHtml::_(
							'rsearchtools.sort',
							'',
							'd.ordering',
							$listDirn,
							$listOrder,
							null,
							'asc',
							'',
							'icon-sort'
						);
						?>
					</th>
					<th>
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>)"? />
					</th>
					<th>
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_DESIGNTYPES_NAME', 'd.name', $listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_DESIGNTYPES_FIELD_ENABLED', 'd.state', $listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_COMMON_ID', 'd.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $row) : ?>
					<tr>
						<td>
							<?php echo $row->ordering; ?>
						</td>
						<td class="order nowrap center hidden-phone">
							<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
								<i class="icon-ellipsis-vertical"></i>
							</span>
							<input type="text"
								   style="display:none"
								   name="order[]"
								   size="5"
								   value="<?php echo $row->ordering; ?>"
								   class="width-20 text-area-order "
								>
							<span class="sortable-handler <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
								<i class="icon-ellipsis-vertical"></i>
							</span>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<?php
							echo JHtml::_(
								'link',
								JRoute::_('index.php?option=com_reddesign&task=designtype.edit&id=' . $row->id),
								$row->name
							);
							?>
						</td>
						<td>
							<?php echo JHtml::_('rgrid.published', $row->state, $i, 'designtypes.', true, 'cb'); ?>
						</td>
						<td>
							<?php echo $row->id;?>
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
	<?php echo JHtml::_('form.token'); ?>
</form>
