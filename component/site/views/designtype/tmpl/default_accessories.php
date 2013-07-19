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
		<a
			class="pull-left modal"
			href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessorytypes/' . $accessorytype->sample_image); ?>">
			<img
				class="media-object accessory-type-thumb"
				alt="<?php echo $accessorytype->title ?>"
				src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessorytypes/thumbnails/' . $accessorytype->sample_thumb); ?>">
		</a>
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
						   class="accessory-option"
						   name="accessorytype<?php echo $accessorytype->reddesign_accessorytype_id; ?>[]"
						   value="<?php echo $accessory->price ?>" />
					<?php else : ?>
					<input
						class="accessory-option"
						type="checkbox"
						name="accessorytype<?php echo $accessorytype->reddesign_accessorytype_id; ?>"
						value="<?php echo $accessory->price ?>" />
					<?php endif; ?>
				</td>
				<td class="accessory-detail">
					<div class="pull-left accessory-image-container">
						<a href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/' . $accessory->image); ?>" class="modal">
							<img
								class="img-polaroid accessory-image"
								src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/thumbnails/' . $accessory->thumbnail); ?>"/>
						</a>
					</div>
					<h5><?php echo $accessory->title ?></h5>
					<?php echo $accessory->description ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>
