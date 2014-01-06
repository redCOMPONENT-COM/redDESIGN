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
			<button id="cancelAreaBtn"
			        class="btn"
			        onclick="cancelArea();"
				>
                                        <span>
                                                <?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
                                        </span>
			</button>
		</div>
	</div>

	<table id="designAreaList" class="table table-striped">
	<thead>
	<tr>
		<th>
			<?php echo JText::_('ID'); ?>
		</th>
		<th>
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_NAME'); ?>
		</th>
		<th>
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_PROPERTIES'); ?>
		</th>
		<th>
			<?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?>
		</th>
		<th>
			<?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?>
		</th>
	</tr>
	</thead>
	<tbody id="areasTBody">
	<?php if ($count = count($this->areas)) : ?>
		<?php
		$i = -1;
		$m = 1;
		?>
		<?php foreach ($this->areas as $area) : ?>
			<?php
			$i++;
			$m = 1 - $m;
			?>
			<tr id="areaRow<?php echo $area->id; ?>"
			    class="<?php echo 'row' . $m; ?>">
				<td>
					<?php echo $area->id; ?>
				</td>
				<td class="span4">
					<a href="#" onclick="selectAreaForEdit(<?php echo $area->id . ',\'' .
						$area->name . '\',' .
						$area->x1_pos . ',' .
						$area->y1_pos . ',' .
						$area->x2_pos . ',' .
						$area->y2_pos . ',' .
						$area->width . ',' .
						$area->height; ?>);">
						<strong><?php echo $area->name; ?></strong>
					</a>
				</td>
				<td>
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong>
					<?php echo round($area->width * $this->pxToUnit) . $this->unit; ?>,
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong>
					<?php echo round($area->height * $this->pxToUnit) . $this->unit; ?>,
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong>
					<?php echo round($area->x1_pos * $this->pxToUnit) . $this->unit; ?>,
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong>
					<?php echo round($area->y1_pos * $this->pxToUnit) . $this->unit; ?>,
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong>
					<?php echo round($area->x2_pos * $this->pxToUnit) . $this->unit; ?>,
					<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong>
					<?php echo round($area->y2_pos * $this->pxToUnit) . $this->unit; ?>
				</td>
				<td>
					<button type="button"
					        class="btn btn-primary btn-mini"
					        onclick="showAreaSettings(<?php echo $area->id; ?>);">
						<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>
					</button>
				</td>
				<td>
					<button type="button" class="btn btn-danger btn-mini" onclick="removeArea(<?php echo $area->id; ?>);">
						<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
					</button>
				</td>
			</tr>

			<tr id="areaSettingsRow<?php echo $area->id ?>" class="<?php echo 'row' . $m; ?> hide areaSettingsRow">
			<td colspan="5" >

			<div class="row-fluid">
			<div class="span4">
				<fieldset>

					<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_FONT_SETTINGS'); ?></legend>

					<div class="row-fluid">
						<div class="span6">

							<?php if($this->item->fontsizer != 'auto' && $this->item->fontsizer != 'auto_chars') : ?>

								<div class="control-group">
									<label for="<?php echo 'areaFontAlignment' . $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>
										<?php
										echo JHtml::_('select.genericlist',
											$this->alignmentOptions,
											'areaFontAlignment' . $area->id,
											'',
											'value',
											'text',
											$area->textalign
										);
										?>
									</label>
								</div>

							<?php endif; ?>

							<div class="control-group">
								<label for="<?php echo 'areaFonts' . $area->id; ?>">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALLOWED_FONTS') ?>
									<?php
									echo JHtml::_(
										'select.genericlist',
										$this->fontsOptions,
										'areaFonts' . $area->id . '[]',
										' multiple="multiple" ',
										'value',
										'text',
										explode(',', $area->font_id)
									);
									?>
								</label>
							</div>

						</div>
						<div class="offset2 span4">

							<?php if ($this->item->fontsizer == 'dropdown_numbers' || $this->item->fontsizer == 'dropdown_labels') : ?>

								<div class="control-group">
									<label for="fontsizerDropdown<?php echo $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>
										<textarea class="span12"
										          style="resize: none;"
										          id="fontsizerDropdown<?php echo $area->id; ?>"
										          name="fontsizerDropdown<?php echo $area->id; ?>"
										          rows="7"
											><?php echo $area->font_size; ?></textarea>
                                                                                                                        <span class="help-block">
                                                                                                                                <?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC') ?>
                                                                                                                        </span>
									</label>
								</div>

							<?php elseif ($this->item->fontsizer == 'slider') : ?>

								<div class="control-group">
									<label for="fontsizerSliderDefault<?php echo $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_FONT_SIZE') ?>
										<input class="input-mini"
										       type="text"
										       value="<?php echo $area->defaultFontSize; ?>"
										       maxlength="3"
										       id="fontsizerSliderDefault<?php echo $area->id; ?>"
										       name="fontsizerSliderDefault<?php echo $area->id; ?>"
											/>
									</label>
								</div>

								<div class="control-group">
									<label for="fontsizerSliderMin<?php echo $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MIN_FONT_SIZE') ?>
										<input class="input-mini"
										       type="text"
										       value="<?php echo $area->minFontSize; ?>"
										       maxlength="3"
										       id="fontsizerSliderMin<?php echo $area->id; ?>"
										       name="fontsizerSliderMin<?php echo $area->id; ?>"
											/>
									</label>
								</div>

								<div class="control-group">
									<label for="fontsizerSliderMax<?php echo $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAX_FONT_SITE') ?>
										<input class="input-mini"
										       type="text"
										       value="<?php echo $area->maxFontSize; ?>"
										       maxlength="3"
										       id="fontsizerSliderMax<?php echo $area->id; ?>"
										       name="fontsizerSliderMax<?php echo $area->id; ?>"
											/>
									</label>
								</div>

							<?php endif; ?>

						</div>
					</div>

				</fieldset>
			</div>

			<div class="span3">
				<fieldset>

					<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_TEXT_INPUT_SETTINGS'); ?></legend>

					<div class="row-fluid">

						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_INPUT_FIELD_TYPE') ?>



								<?php
								echo JHtml::_('select.genericlist',
									$this->inputFieldOptions,
									'inputFieldType' . $area->id . '[]',
									' onclick="changeInputFieldType(' . $area->id . ');" ',
									'value',
									'text',
									$area->input_field_type,
									'inputFieldType' . $area->id
								);
								?>
							</label>
						</div>

						<div class="control-group">
							<label for="defaultText<?php echo $area->id; ?>">

								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_TEXT'); ?>

								<div id="defaultTextContainer<?php echo $area->id; ?>">
									<textarea class="input-medium"
									          style="resize: none;"
									          id="defaultText<?php echo $area->id; ?>"
									          name="defaultText<?php echo $area->id; ?>"
										><?php echo $area->default_text; ?></textarea>
								</div>

							</label>
						</div>

						<div class="control-group">
							<label for="maximumCharsAllowed<?php echo $area->id; ?>">

								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_CHARS'); ?>

								<input class="input-mini"
								       type="text"
								       value="<?php echo $area->maxchar; ?>"
								       id="maximumCharsAllowed<?php echo $area->id; ?>"
								       name="maximumCharsAllowed<?php echo $area->id; ?>"
									/>

							</label>
						</div>

						<?php
						if ($area->input_field_type == 0)
						{
							$style = ' style="display: none;" ';
						}
						else
						{
							$style = ' style="display: inline;" ';
						}
						?>

						<div class="control-group" id="maximumLinesAllowedContainer<?php echo $area->id ?>" <?php echo $style; ?>>
							<label for="maximumLinesAllowed<?php echo $area->id; ?>">

								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_LINES') ?>

								<input class="input-mini"
								       type="text"
								       value="<?php echo $area->maxline; ?>"
								       id="maximumLinesAllowed<?php echo $area->id; ?>"
								       name="maximumLinesAllowed<?php echo $area->id; ?>"
									/>

							</label>
						</div>

					</div>
				</fieldset>
			</div>

			<div class="span5">
				<fieldset>

					<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_TEXT_COLOR_SETTINGS'); ?></legend>

					<div class="row-fluid">
						<div class="span6">
							<div class="span12">
								<label class="checkbox inline" for="allColors<?php echo $area->id; ?>">
									<?php
									$allColorsHide = '';

									if ($area->color_code == 1)
									{
										$chkAllColors = 'checked="checked"';
										$allColorsHide = 'style="display: none;"';
									}
									else
									{
										$chkAllColors = '';
									}
									?>
									<input type="checkbox"
									       id="allColors<?php echo $area->id; ?>"
									       name="allColors<?php echo $area->id; ?>"
									       value="allColors"
										<?php echo $chkAllColors; ?>
										>
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_CHECK_ALL_COLORS'); ?>
								</label>
							</div>

							<div id="addColorContainer<?php echo $area->id ?>" class="span12 addColorButton" <?php echo $allColorsHide; ?> >
								<button class="btn btn-mini btn-success" id="addColorButton<?php echo $area->id ?>" type="button">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ADD_COLOR'); ?>
								</button>
							</div>

							<?php $colorCodes = explode(',', $area->color_code); ?>
							<div id="selectedColorsPalette<?php echo $area->id ?>" class="span12" <?php echo $allColorsHide; ?> >
								<?php foreach ($colorCodes as $colorCode) : ?>
									<?php if (!empty($colorCode) && $colorCode != 1) : ?>
										<div class="colorDiv"
										     id="<?php echo $area->id . '-' . str_replace('#', '', $colorCode); ?>"
										     style="background-color: <?php echo $colorCode; ?>;"
										     onclick="removeColorFromList(<?php echo $area->id ?>, '<?php echo $colorCode; ?>');">
											<i class="glyphicon icon-remove"></i>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>

							<input type="hidden"
							       id="colorCodes<?php echo $area->id ?>"
							       name="colorCodes<?php echo $area->id ?>"
							       value="<?php echo $area->color_code; ?>"
								/>
						</div>

						<div id="colorsContainer<?php echo $area->id ?>" class="span6" <?php echo $allColorsHide; ?>>

							<div class="span9">
								<label class="control-label">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
									<div id="colorPickerContainer<?php echo $area->id; ?>" class="colorPickerContainer"></div>
								</label>

								<label for="colorPickerSelectedColor<?php echo $area->id; ?>">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?>
									<input class="span12 colorPickerSelectedColor"
									       type="text"
									       value="#cfcfcf"
									       id="colorPickerSelectedColor<?php echo $area->id; ?>"
									       name="colorPickerSelectedColor<?php echo $area->id; ?>"
										/>
								</label>
							</div>

							<div class="span3 CMYKContainer">
								<div class="input-prepend">
									<span class="add-on">C</span>
									<input class="span8"
									       id="C<?php echo $area->id; ?>"
									       name="C<?php echo $area->id; ?>"
									       type="text"
									       value="10"
									       placeholder="C"
										>
								</div>
								<div class="input-prepend">
									<span class="add-on">M</span>
									<input class="span8"
									       id="M<?php echo $area->id; ?>"
									       name="M<?php echo $area->id; ?>"
									       type="text"
									       value="10"
									       placeholder="M"
										>
								</div>
								<div class="input-prepend">
									<span class="add-on">Y</span>
									<input class="span8"
									       id="Y<?php echo $area->id; ?>"
									       name="Y<?php echo $area->id; ?>"
									       type="text"
									       value="10"
									       placeholder="Y"
										>
								</div>
								<div class="input-prepend">
									<span class="add-on">K</span>
									<input class="span8"
									       id="K<?php echo $area->id; ?>"
									       name="K<?php echo $area->id; ?>"
									       type="text"
									       value="10"
									       placeholder="K"
										>
								</div>

							</div>

						</div>
					</div>
				</fieldset>
			</div>

			<div class="span6 offset5">
				<button id="saveAreaSettings<?php echo $area->id; ?>"
				        type="button"
				        class="btn btn-success"
				        data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>"
				        onclick="saveAreaSettings(<?php echo $area->id; ?>);">
                                                                                <span>
                                                                                        <?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
                                                                                </span>
				</button>
				<button type="button"
				        class="btn"
				        onclick="showAreaSettings(<?php echo $area->id; ?>);">
                                                                                <span>
                                                                                        <?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
                                                                                </span>
				</button>
			</div>

			</div>
			</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr id="noAreaMessage">
			<td colspan="5">
				<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
	</table>
	</div>
<?php endif; ?>
