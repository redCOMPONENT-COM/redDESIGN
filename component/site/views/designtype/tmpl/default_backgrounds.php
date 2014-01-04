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
}

?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="frames" class="thumbnails">
		<?php foreach($this->backgrounds as $frame) : ?>
			<?php
				if ($frame->isDefaultPreview)
				{
					$reddesign_background_id = $frame->id;
				}
			?>
			<li>
			<div class="frame-container">
				<div class="frame-selection">
					<input type="radio"
						   class="price-modifier"
						   onChange ="setBackground(<?php echo $frame->reddesign_background_id;?>);"
						   id="frame<?php echo $frame->reddesign_background_id;?>"
						   name="frame"
						   value="<?php echo $frame->reddesign_background_id ?>"
						<?php if ($frame->isDefaultPreview) : ?>
						   checked="checked"
						<?php endif; ?>
						/>
				</div>
				<div class="frame-detail">
					<div class="frame-thumbnail-container">
						<?php echo JHTML::_('image', 'media/com_reddesign/backgrounds/thumbnails/' . $frame->thumbnail, $frame->title, 'class="img-polaroid frame-thumbnail"') ?>
					</div>
					<div class="pull-left">
						<h5><?php echo $frame->title; ?></h5>
					</div>
				</div>
			</div>
			</li>
		<?php endforeach; ?>

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
									   id="frame<?php echo $frame->reddesign_background_id;?>"
									   name="frame"
									   value="<?php echo $frame->reddesign_background_id ?>"
									<?php if ($frame->isDefaultPreview) : ?>
									   checked="checked"
									<?php endif; ?>
								/>
							</div>
							<div class="frame-detail">
								<div class="frame-thumbnail-container">
									<?php echo JHTML::_('image', 'media/com_reddesign/backgrounds/thumbnails/' . $frame->thumbnail, $frame->title, 'class="img-polaroid frame-thumbnail"') ?>
								</div>
								<div class="pull-left">
									<h5><?php echo $frame->title; ?></h5>
								</div>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>

		<input type="hidden"
			   name="reddesign_background_id"
			   id="reddesign_background_id"
			   value="<?php echo $reddesign_background_id;?>"
			/>

		<input type="hidden"
			   name="production_background_id"
			   id="production_background_id"
			   value="<?php echo $this->productionBackground->reddesign_background_id;?>"
			/>

	</ul>
</div>
