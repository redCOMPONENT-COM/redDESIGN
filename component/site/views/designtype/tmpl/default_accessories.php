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
	<div class="paragraphs">
		<div class="row">
			<div class="span9">
				<div class="clearfix content-heading">
					<h3><?php echo $accessorytype->title ?></h3>
				</div>
				<?php echo $accessorytype->description ?>
			</div>
		</div>
	</div>
	<table class="table table-hover">
		<tbody>
			<?php foreach ($accessorytype->accessories as $accessory) : ?>
			<tr>
				<td class="accessory-selection">
					<?php if($accessorytype->single_select) : ?>
					<input type="radio" name="accessorytype<?php echo $accessorytype->reddesign_accessorytype_id; ?>[]" value="<?php echo $accessory->price ?>" />
					<?php else : ?>
					<input type="checkbox" name="accessorytype<?php echo $accessorytype->reddesign_accessorytype_id; ?>" value="<?php echo $accessory->price ?>" />
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
					<h4><?php echo $accessory->title ?></h4>
					<?php echo $accessory->description ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>
