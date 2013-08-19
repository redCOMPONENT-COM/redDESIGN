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
						<?php
							$productIds = explode(",", $order->redshop_product_id);
							$productNumbers = explode(",", $order->redshop_product_number);

							for ($i = 0; $i < count($productIds); $i++)
							{
						?>
								<div>
									<a href="index.php?option=com_redshop&view=product_detail&task=edit&cid[]=<?php echo $productIds[$i]; ?>">
										<?php echo $productNumbers[$i]; ?>
									</a>
								</div>
						<?php
							}
						?>
					</td>

					<td align="left">
						<?php foreach ($productIds as $productId) : ?>
								<div>
									<button class="btn btn-mini" onclick="createProductionFile(<?php echo $productId; ?>);">
										<span><?php echo JText::_('COM_REDDESIGN_ORDERS_CREATE_PRODUCTION_FILE'); ?></span>
									</button>
								</div>
								<div id="pdf-link<?php echo $productId; ?>">
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

<script type="text/javascript">
	/**
	 * Creates a production file and opens it.
	 *
	 * @return void
	 */
	function createProductionFile(productId) {
		akeeba.jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=order&task=createProductionFile&format=raw&<?php echo JFactory::getSession()->getFormToken(); ?>=1",
			data: { productId : productId },
			type: "post",
			success: function(data) {
				akeeba.jQuery("#pdf-link" + productId).html(data);
			},
			error: function(errMsg) {
				alert(errMsg);
			}
		});
	}
</script>