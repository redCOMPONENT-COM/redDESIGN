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
RHelperAsset::load('mustache.min.js', 'com_reddesign');

// Colorpicker includes.
RHelperAsset::load('farbtastic.min.js', 'com_reddesign');
RHelperAsset::load('farbtastic.css', 'com_reddesign');
RHelperAsset::load('color-converter.js', 'com_reddesign');
$config = ReddesignEntityConfig::getInstance();
$bgBackendPreviewWidth = $config->getMaxSVGPreviewAdminWidth();

if (isset($displayData))
{
	$this->areas = $displayData->items;
	$this->item = $displayData->item;
	$this->productionBackground = $displayData->productionBackground;
	$this->bgBackendPreviewWidth = $displayData->bgBackendPreviewWidth;
	$this->unit = $displayData->unit;
	$this->unitConversionRatio = $displayData->unitConversionRatio;
	$this->sourceDpi = $displayData->sourceDpi;
	$this->productionBgAttributes = $displayData->productionBgAttributes;
	$this->fontsOptions = $displayData->fontsOptions;
	$this->inputFieldOptions = $displayData->inputFieldOptions;
}

?>

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
				<div class="span4">
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
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>
						</div>
					</div>
					<div class="control-group">
						<label for="areaHeight" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_HEIGHT'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaHeight" name="areaHeight"
							        value=""
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>
						</div>
					</div>
					<div class="control-group">
						<label for="areaX1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X1'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX1" name="areaX1"
							        value=""
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>
						</div>
					</div>
					<div class="control-group">
						<label for="areaY1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y1'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY1" name="areaY1"
							        value=""
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>

						</div>
					</div>
					<div class="control-group">
						<label for="areaX2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X2'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX2" name="areaX2"
							        value=""
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>
						</div>
					</div>
					<div class="control-group">
						<label for="areaY2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y2'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY2" name="areaY2"
							        value=""
							        class="area-textbox-control"
									placeholder="&nbsp;<?php echo $this->unit; ?>"
								>
						</div>
					</div>
				</div>
				<div class="span8">
					<span class="help-block">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMG_HELP'); ?>
					</span>
					<div id="backgroundImageContainer">
						<svg id="svgForAreas">
						</svg>
						<div class="progressbar-holder" style="width: <?php echo $bgBackendPreviewWidth; ?>px; margin-top:20px;">
							<div class="progress progress-striped" style="display:none;">
								<div class="bar bar-success"></div>
							</div>
						</div>
						<h3>
							<?php
								echo JText::sprintf(
														'COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMAGE_MEASURES',
														round($this->productionBgAttributes->width / $this->unitConversionRatio, 2),
														$this->unit,
														round($this->productionBgAttributes->height / $this->unitConversionRatio, 2),
														$this->unit
								);
							?>
						</h3>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
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
						<button id="cancelAreaBtn" class="btn" onclick="clearSelectionFields();">
							<span>
								<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
							</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif;
