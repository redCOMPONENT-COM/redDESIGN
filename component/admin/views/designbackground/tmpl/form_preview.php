<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>

<div class="control-group">
	<label class="control-label ">
		<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW') ?>:
	</label>

	<div class="controls">
		<script type="text/javascript">
			function populateSelectorData(img, selection) {
				jQuery('#area_x1').val(selection.x1);
				jQuery('#area_y1').val(selection.y1);
				jQuery('#area_x2').val(selection.x2);
				jQuery('#area_y2').val(selection.y2);
				jQuery('#area_width').val(selection.width);
				jQuery('#area_height').val(selection.height);
			}

			jQuery(document).ready(function ($) {
				jQuery('img#background').imgAreaSelect({
					handles: true,
					<?php
					if (!empty($this->item->area_x1)
						&& !empty($this->item->area_y1)
						&& !empty($this->item->area_x2)
						&& !empty($this->item->area_y2)
						&& !empty($this->item->area_width)
						&& !empty($this->item->area_height) )
						: ?>
					x1: <?php echo $this->item->area_x1; ?>,
					y1: <?php echo $this->item->area_y1; ?>,
					x2: <?php echo $this->item->area_x2; ?>,
					y2: <?php echo $this->item->area_y2; ?>,
					area_width: <?php echo $this->item->area_width; ?>,
					area_height: <?php echo $this->item->area_height; ?>,
					<?php endif; ?>
					onSelectEnd: populateSelectorData
				});
			});
		</script>
		<img id="background"
			 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>" />
						<span
							class="help-block">
							<br/>
							<span class="label label-info"><?php echo  JText::_('COM_REDDESIGN_COMMON_ATTENTION') ?>
							</span> <?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW_DESC'); ?></span>
	</div>
</div>