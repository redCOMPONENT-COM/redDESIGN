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
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="frames" class="thumbnails">
		<?php foreach($this->previewBackgrounds as $frame) : ?>
			<?php
				if ($frame->isPreviewbgimage)
				{
					$reddesign_background_id = $frame->reddesign_background_id;
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
						   data-price="<?php echo $frame->price ?>"
						<?php if ($frame->isPreviewbgimage) : ?>
						   checked="checked"'
						<?php endif; ?>
						/>
				</div>
				<div class="frame-detail">
					<div class="frame-thumbnail-container">
						<img class="img-polaroid frame-thumbnail"
							 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $frame->thumbnail; ?>"
							 alt="<?php echo $frame->title; ?>"/>
					</div>
					<div class="pull-left">
						<h5><?php echo $frame->title; ?></h5>
					</div>
				</div>
			</div>
			</li>
		<?php endforeach; ?>
		<input type="hidden" name="reddesign_background_id" id="reddesign_background_id" value="<?php echo $reddesign_background_id;?>" />
		<input type="hidden"
			   name="production_background_id"
			   id="production_background_id"
			   value="<?php echo $this->productionBackground->reddesign_background_id;?>"
			/>
	</ul>
</div>
