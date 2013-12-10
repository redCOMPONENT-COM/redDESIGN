<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$listOrder        = $this->escape($this->state->get('list.ordering'));
$listDirn        = $this->escape($this->state->get('list.direction'));
?>
<form action="index.php?option=com_reddesign&view=fonts" method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_REDDESIGN_COMMON_FILTER'); ?>
			<?php echo RLayoutHelper::render('search', array('view' => $this)) ?>
		</div>
	</div>
	<?php if (empty($items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDDESIGN_COMMON_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('COM_REDDESIGN_COMMON_NUM'); ?>
					</th>
					<th>
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>)"? />
					</th>
					<th>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_FONT_FIELD_TITLE', 'tbl.title', $listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_COMMON_ENABLED', 'tbl.enabled', $listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo JHtml::_('rgrid.sort', 'COM_REDDESIGN_COMMON_ID', 'tbl.reddesign_font_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $row) : ?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.reddesign_font_id', $i, $row->reddesign_font_id); ?>
						</td>
						<td>
							<?php
								echo JHtml::_(
												'link',
												JRoute::_('index.php?option=com_reddesign&task=font.edit&reddesign_font_id=' . $row->reddesign_font_id),
												$row->title
								);
							?>
						</td>
						<td>
							<?php echo $row->enabled;?>
						</td>
						<td>
							<?php echo $row->reddesign_font_id;?>
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