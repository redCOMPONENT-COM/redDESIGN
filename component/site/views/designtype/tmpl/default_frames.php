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
<table class="table table-hover top-buffer">
	<tbody>
	<?php foreach($this->previewBackgrounds as $frame) : ?>
	<?php if ($frame->isPreviewbgimage) :
			$reddesign_background_id = $frame->reddesign_background_id;
	      endif; ?>
		<tr>
			<td class="accessory-selection">
					<input type="radio"
						   class="price-modifier"
						   name="frame"
						   value="<?php echo $frame->price ?>"
						   <?php if ($frame->isPreviewbgimage) echo 'checked="checked"'; ?>
						/>
			</td>
			<td class="accessory-detail">
				<div class="pull-left frame-thumbnail-container">
					<img
						class="img-polaroid frame-thumbnail"
						src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $frame->thumbnail; ?>"/>
				</div>
				<h5><?php echo $frame->title ?>
					&nbsp;<span class="label"><?php echo $frame->price . ' ' . $this->currency; ?></span></h5>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php // @TODO: this following hidden field will have to update it's value according to frame selection; ?>
<input type="hidden" name="reddesign_background_id" id="reddesign_background_id" value="<?php echo $reddesign_background_id;?>">

