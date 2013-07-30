<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>
<?php foreach ($this->accessorytypes as $accessorytype) : ?>
	<div class="media">
		<?php if ($accessorytype->sample_thumb) : ?>
			<?php if ($accessorytype->sample_image) : ?>
			<a
				class="pull-left modal"
				href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessorytypes/' . $accessorytype->sample_image); ?>">
			<?php endif; ?>
			<img
				class="media-object accessorytype-thumbnail"
				alt="<?php echo $accessorytype->title ?>"
				src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessorytypes/thumbnails/' . $accessorytype->sample_thumb); ?>">
			<?php if ($accessorytype->sample_image) : ?>
			</a>
			<?php endif; ?>
		<?php endif; ?>
		<div class="media-body">
			<h4 class="media-heading"><?php echo $accessorytype->title ?></h4>
			<?php echo $accessorytype->description ?>
		</div>
	</div>

	<table class="table table-hover top-buffer">
		<tbody>
			<?php foreach ($accessorytype->accessories as $accessory) : ?>
			<tr>
				<td class="accessory-selection">
					<?php if($accessorytype->single_select) : ?>
					<input type="radio"
						   class="price-modifier"
						   name="AccessoryId<?php echo $accessorytype->reddesign_accessorytype_id; ?>[]"
						   value="<?php echo $accessory->reddesign_accessory_id ?>"
						   data-price="<?php echo $accessory->price ?>"
						   <?php if ($accessory->isDefault) echo 'checked="checked"'; ?>
						/>
					<?php else : ?>
					<input
						class="price-modifier"
						type="checkbox"
						name="AccessoryId<?php echo $accessorytype->reddesign_accessorytype_id; ?>[]"
						value="<?php echo $accessory->reddesign_accessory_id ?>"
						data-price="<?php echo $accessory->price ?>"
						<?php if ($accessory->isDefault) echo 'checked="checked"'; ?>
						/>
					<?php endif; ?>
				</td>
				<td class="accessory-detail">
					<?php if($accessory->thumbnail) : ?>
					<div class="pull-left accessory-thumbnail-container">
						<?php if($accessory->image) : ?>
							<a href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/' . $accessory->image); ?>" class="modal">
						<?php endif; ?>
							<img
								class="img-polaroid accessory-thumbnail"
								src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/thumbnails/' . $accessory->thumbnail); ?>"/>
						<?php if($accessory->image) : ?>
							</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<h5><?php echo $accessory->title; ?>
						&nbsp;<span
							class="label">
							<?php if ($this->params->get('currency_symbol_position_before', '1')) : ?>
								<?php echo  $this->params->get('currency_symbol', '$'); ?>
							<?php endif; ?>
							<?php
							echo number_format(
								$accessory->price,
								$this->params->get('decimals', '2'),
								$this->params->get('currency_decimal_separator', '.'),
								$this->params->get('currency_thousand_separator', ',')
							); ?>
							<?php if (!$this->params->get('currency_symbol_position_before', '1')) : ?>
								<?php echo  $this->params->get('currency_symbol', '$'); ?>
							<?php endif; ?>					</span>
					</h5>
					<?php echo $accessory->description ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<input type="hidden" id="reddesign_accessorytype_id" name="reddesign_accessorytype_id[]" value="<?php echo $accessorytype->reddesign_accessorytype_id; ?>">
		</tbody>
	</table>
<?php endforeach; ?>
