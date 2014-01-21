<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

if (isset($displayData))
{
	$this->backgrounds = $displayData->backgrounds;
	$this->relatedDesignTypes = $displayData->relatedDesignTypes;
	$this->productionBackground = $displayData->productionBackground;
}

$background_id = null;

?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="frames" class="thumbnails">
		<?php foreach($this->backgrounds as $frame) : ?>
			<?php
				if ($frame->isDefaultPreview)
				{
					$background_id = $frame->id;
				}
			?>
			<li>
			<div class="frame-container">
				<div class="frame-selection">
					<input type="radio"
						   class="price-modifier"
						   onChange ="setBackground(<?php echo $frame->id;?>);"
						   id="frame<?php echo $frame->id;?>"
						   name="frame"
						   value="<?php echo $frame->id ?>"
						<?php if ($frame->isDefaultPreview) : ?>
						   checked="checked"
						<?php endif; ?>
						/>
				</div>
				<div class="frame-detail">
					<div class="frame-thumbnail-container">
						<object type="image/svg+xml"
						        data="<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $frame->svg_file; ?>"
						        width="150"
						        height="150"
						>
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BROWSER_NOT_SUPPORTED_FALLBACK'); ?>
						</object>
					</div>
					<div class="pull-left">
						<h5><?php echo $frame->name; ?></h5>
					</div>
				</div>
			</div>
			</li>
		<?php endforeach; ?>

		<?php if (!empty($this->relatedDesignTypes)) : ?>
			<?php foreach($this->relatedDesignTypes as $relatedDesignType) : ?>
				<?php if (!empty($relatedDesignType)) : ?>
					<?php
						$productId = $this->config['input']->getInt('productId', 0);
						$cid = $this->config['input']->getInt('cid', null);
						$Itemid = $this->config['input']->getInt('Itemid', null);

						$backgroundsModel = RModel::getAdminInstance('Backgrounds', array(), 'com_reddesign');
						$backgroundsModel->setState('designtype_id', $relatedDesignType);
						$relatedBackgrounds = $backgroundModel->getItems();
					?>
					<?php foreach($relatedBackgrounds as $frame) : ?>
						<li>
							<div class="frame-container">
								<div class="frame-selection">
									<input type="radio"
										   class="price-modifier"
										   onclick ="location.href='<?php echo JURI::base() ?>index.php?option=com_redshop&view=product&pid=<?php echo $productId ?>&cid=<?php echo $cid ?>&Itemid=<?php echo $Itemid ?>&designTypeId=<?php echo $relatedDesignType; ?>'"
										   id="frame<?php echo $frame->id;?>"
										   name="frame"
										   value="<?php echo $frame->id ?>"
										<?php if ($frame->isDefaultPreview) : ?>
										   checked="checked"
										<?php endif; ?>
									/>
								</div>
								<div class="frame-detail">
									<div class="frame-thumbnail-container">
										<?php echo JHTML::_('image', 'media/com_reddesign/backgrounds/thumbnails/' . $frame->thumbnail, $frame->name, 'class="img-polaroid frame-thumbnail"') ?>
									</div>
									<div class="pull-left">
										<h5><?php echo $frame->name; ?></h5>
									</div>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<input type="hidden"
			   name="background_id"
			   id="background_id"
			   value="<?php echo $background_id;?>"
			/>

		<input type="hidden"
			   name="production_background_id"
			   id="production_background_id"
			   value="<?php echo $this->productionBackground->id;?>"
			/>

	</ul>
</div>
