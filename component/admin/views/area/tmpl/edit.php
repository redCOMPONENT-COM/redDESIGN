<?php
/**
* @package     RedDesign.Component
* @subpackage  Administrator
*
* @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
* @license     GNU General Public License version 2 or later, see LICENSE.
*/

defined('_JEXEC') or die();

// JS templating framework
RHelperAsset::load('mustache.min.js');

// Colorpicker includes.
RHelperAsset::load('farbtastic.min.js');
RHelperAsset::load('farbtastic.css');
RHelperAsset::load('color-converter.js');

// SVG jQuery Plugin
RHelperAsset::load('jquery.svg.css');
RHelperAsset::load('jquery.svg.min.js');
RHelperAsset::load('jquery.svganim.min.js');
RHelperAsset::load('jquery.svgdom.min.js');
RHelperAsset::load('jquery.svgfilter.min.js');
RHelperAsset::load('jquery.svggraph.min.js');
RHelperAsset::load('jquery.svgplot.min.js');

if (isset($displayData))
{
	$this->areas = $displayData->items;
	$this->item = $displayData->item;
	$this->productionBackground = $displayData->productionBackground;
	$this->fontsOptions = $displayData->fontsOptions;
	$this->unit = $displayData->unit;
	$this->pxToUnit = $displayData->pxToUnit;
	$this->unitToPx = $displayData->unitToPx;
	$this->ratio = $displayData->ratio;
	$this->imageWidth = $displayData->imageWidth;
	$this->imageHeight = $displayData->imageHeight;
	$this->inputFieldOptions = $displayData->inputFieldOptions;
	$this->params = $displayData->params;
}

$canvasWidth = $this->params->get('max_svg_backend_bg_width', 600);
$canvasHeight = $this->params->get('max_svg_backend_bg_height', 400);

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->item->designtype_id . '&tab=design-areas';
?>


</div>
<?php if (empty($this->productionBackground)) : ?>

	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND_DESC'); ?></span>

<?php else : ?>

	<div id="areaMessage"></div>
	<div class="designAreasContainer">
	<h3><?php echo JText::sprintf('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS', $this->productionBackground->name); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DESC'); ?></span>

	<div class="well">
		<div id="selectorControls" class="row-fluid">
			<div class="span3">
				<input id="designAreaId" name="designAreaId" type="hidden" value="0">
				<div class="control-group">
					<label for="areaName" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_NAME'); ?>
					</label>
					<div class="controls">
						<input type="text" id="areaName" name="areaName" required="required" value="">
					</div>
				</div>
				<div class="control-group">
					<label for="areaWidth" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_WIDTH'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaWidth" name="areaWidth"
						        value=""
						        onkeyup="selectAreaOnWidthKeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
				<div class="control-group">
					<label for="areaHeight" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_HEIGHT'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaHeight" name="areaHeight"
						        value=""
						        onkeyup="selectAreaOnHeightKeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
				<div class="control-group">
					<label for="areaX1" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X1'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaX1" name="areaX1"
						        value=""
						        onkeyup="selectAreaOnX1KeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
				<div class="control-group">
					<label for="areaY1" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y1'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaY1" name="areaY1"
						        value=""
						        onkeyup="selectAreaOnY1KeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
				<div class="control-group">
					<label for="areaX2" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X2'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaX2" name="areaX2"
						        value=""
						        onkeyup="selectAreaOnX2KeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
				<div class="control-group">
					<label for="areaY2" class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y2'); ?>
					</label>
					<div class="controls">
						<input  type="text" id="areaY2" name="areaY2"
						        value=""
						        onkeyup="selectAreaOnY2KeyUp();">&nbsp;<?php echo $this->unit; ?>
					</div>
				</div>
			</div>
			<div class="span9">
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMG_HELP'); ?>
				</span>
				<div id="backgroundImageContainer">
					<div id="svgCanvas" style="width: <?php echo $canvasWidth ?>; height: <?php echo $canvasHeight; ?>;">
				</div>
				<h3>
					<?php
					echo JText::sprintf(
						'COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMAGE_MEASURES',
						round($this->imageWidth * $this->pxToUnit, 0),
						$this->unit,
						round($this->imageHeight * $this->pxToUnit, 0),
						$this->unit
					);
					?>
				</h3>
			</div>
		</div>
		<div class="form-actions">
			<button id="saveAreaBtn"
					class="btn btn-success"
					data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVING_AREA'); ?>"
					onclick="preSaveArea(jQuery('#designAreaId').val());"
			>
				<span>
					<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
				</span>
			</button>
			<button id="cancelAreaBtn" class="btn" onclick="cancelArea();">
				<span>
					<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
				</span>
			</button>
		</div>
	</div>
	</div>
<?php endif; ?>
