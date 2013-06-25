<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

FOFTemplateUtils::addJS('media://com_reddesign/assets/js/jquery.imgareaselect.pack.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/assets/css/imgareaselect-animated.css');
?>

<script type="text/javascript">
	function populateSelectionFields(img, selection) {
		akeeba.jQuery('#area_x1').val(selection.x1);
		akeeba.jQuery('#area_y1').val(selection.y1);
		akeeba.jQuery('#area_x2').val(selection.x2);
		akeeba.jQuery('#area_y2').val(selection.y2);
		akeeba.jQuery('#area_width').val(selection.width);
		akeeba.jQuery('#area_height').val(selection.height);
	}

	function selectArea(x1, y1, x2, y2, width, height) {
		akeeba.jQuery('img#background').imgAreaSelect({
			handles: true,
			x1: x1,
			y1: y1,
			x2: x2,
			y2: y2,
			area_width: width,
			area_height: height
		});
	}

	akeeba.jQuery(document).ready(
		function ($) {
			akeeba.jQuery('img#background').imgAreaSelect({
				handles: true,
				onSelectEnd: populateSelectionFields
		});
	});
</script>

<h3><?php echo JText::sprintf('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS', $this->productionBackground->title); ?></h3>
<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DESC'); ?></span>

<div class="well">
	<div id="selector-controls" class="row-fluid">
		<div class="span4">
			<div class="control-group">
				<label for="area_x1" class="control-label">
					<?php echo JText::_('COM_REDDESIGN_AREA_X1') ?>
				</label>
				<div class="controls">
					<input type="text" id="area_x1" name="area_x1"
						value=""
									onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
				</div>
			</div>
			<div class="control-group">
				<label for="area_y1" class="control-label">
					<?php echo JText::_('COM_REDDESIGN_AREA_Y1') ?>
				</label>
				<div class="controls">
					<input type="text" id="area_y1" name="area_y1"
					value=""
					onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
				</div>
			</div>
			<div class="control-group">
				<label for="area_x2" class="control-label">
					<?php echo JText::_('COM_REDDESIGN_AREA_X2') ?>
				</label>

				<div class="controls">
					<input type="text" id="area_x2" name="area_x2"
						value=""
									onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_y2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_Y2') ?>
						</label>

						<div class="controls">
							<input type="text" id="area_y2" name="area_y2"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_width" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_WIDTH') ?>
						</label>

						<div class="controls">
							<input type="text" id="area_width" name="area_width"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="area_width" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_AREA_HEIGHT') ?>
						</label>

						<div class="controls">
							<input type="text" id="area_height" name="area_height"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#area_x1').val(),
														akeeba.jQuery('#area_y1').val(),
														akeeba.jQuery('#area_x2').val(),
														akeeba.jQuery('#area_y2').val(),
														akeeba.jQuery('#area_width').val(),
														akeeba.jQuery('#area_height').val());">
						</div>
					</div>
		</div>
		<div id="background-image-container">
				<img id="background"
					 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->productionBackground->image_path; ?>"/>
		</div>
	</div>
	<div class="form-actions">
		<a id="save-area-btn" rel="" class="btn btn-primary" onclick="saveArea();" title="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>">
			<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
		</a>
		<a id="cancel-area-btn" rel="" class="btn" onclick="saveArea();" title="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>">
			<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
		</a>
	</div>
</div>