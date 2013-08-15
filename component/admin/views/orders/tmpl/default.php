<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHTML::_('behavior.framework');
?>
<div class="form-container">
	<table id="itemsList" class="table table-striped">
		<thead>
		<tr>
			<th width="9%">
				<?php echo JText::_('ID'); ?>
			</th>
			<th width="9%">
				<?php echo JText::_('COM_REDDESIGN_ORDERS_REDSHOP_ORDER_ID'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_REDDESIGN_ORDERS_REDSHOP_ORDER_STATUS'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_REDDESIGN_ORDERS_REDSHOP_PRODUCT_NUMBER'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_REDDESIGN_ORDERS_PRODUCTIONFILE'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php if ($count = count($this->orders)) : ?>
			<?php
			$i = -1;
			$m = 1;
			?>
			<?php foreach ($this->orders as $order) : ?>
				<?php
				$i++;
				$m = 1 - $m;
				?>
				<tr class="<?php echo 'row' . $m; ?>">
					<td>
						<?php echo $order->reddesign_order_id; ?>
					</td>
					<td>
						<a href="index.php?option=com_redshop&view=order_detail&task=edit&cid[]=<?php echo $order->redshop_order_id;?>">
							<?php echo $order->redshop_order_id; ?>
						</a>
					</td>
					<td>
						<?php echo $order->order_status; ?>
					</td>
					<td align="left">
							<?php $productNumbers= explode(",", $order->redshop_product_number);
								  foreach($productNumbers as $productNumber): ?>
										<div>
											<a href="index.php?option=com_redshop&view=product_detail&task=edit&cid[]=<?php echo $order->redshop_product_id ?>">
												<?php echo $productNumber; ?>
											</a>
										</div>
							<?php endforeach; ?>
					</td>
					<td align="left">
						<?php $productionFiles= explode(",", $order->reddesign_productionfile);
							  foreach($productionFiles as $productionFile): ?>
									<div>
										<a target="_blank" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/orders/pdf/' . $productionFile); ?>">
											<?php echo JText::_('COM_REDDESIGN_ORDERS_PRODUCTIONFILE'); ?>
										</a>
									</div>
						<?php endforeach; ?>
					</td>

				</tr>
				<tr class="hide">
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
</div>
