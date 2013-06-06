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
		<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW') ?>
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

			function selectArea(x1, y1, x2, y2, width, height) {

				jQuery('img#background').imgAreaSelect({
					handles: true,
					x1: x1,
					y1: y1,
					x2: x2,
					y2: y2,
					area_width: width,
					area_height: height
				});

				console.log(x1 + ',' + y1 + ',' + x2 + ',' + y2 + ',' + width + ',' + height);
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
		<div>
			<div>
				<img class="left" id="background"
				     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>"/>

				<div class="left">
					<div class="control-group">
						<label for="area_x1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_X1') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_x1" name="area_x1"
							       value="<?php echo $this->item->area_x1; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_y1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_Y1') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_y1" name="area_y1"
							       value="<?php echo $this->item->area_y1; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_x2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_X2') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_x2" name="area_x2"
							       value="<?php echo $this->item->area_x2; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_y2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_Y2') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_y2" name="area_y2"
							       value="<?php echo $this->item->area_y2; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_width" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_WIDTH') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_width" name="area_width"
							       value="<?php echo $this->item->area_width; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_width" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_HEIGHT') ?>
						</label>

						<div class="controls">
							<input type="text" class="left" id="area_height" name="area_height"
							       value="<?php echo $this->item->area_height; ?>"
							       onkeyup="javascript:selectArea(jQuery('#area_x1').val(), jQuery('#area_y1').val(), jQuery('#area_x2').val(), jQuery('#area_y2').val(), jQuery('#area_width').val(), jQuery('#area_height').val());">
						</div>
					</div>
				</div>
			</div>
			<span class="help-block clear-both">
				<br/>
				<span class="label label-info">
					<?php echo JText::_('COM_REDDESIGN_COMMON_ATTENTION') ?>
				</span>
				<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_THUMB_PREVIEW_DESC'); ?>
			</span>
		</div>
	</div>
</div>