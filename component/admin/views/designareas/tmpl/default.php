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

// Select area JS includes.
RHelperAsset::load('jquery.imgareaselect.pack.js');
RHelperAsset::load('imgareaselect-animated.css');
RHelperAsset::load('selectionboxmove.js');

// Colorpicker includes.
RHelperAsset::load('farbtastic.min.js');
RHelperAsset::load('farbtastic.css');
RHelperAsset::load('color-converter.js');

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
}
?>

<?php if (empty($this->productionBackground)) : ?>

	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND_DESC'); ?></span>

<?php else : ?>

	<?php
		// Load JS template for design area setting rows.
		// Echo $this->loadTemplate('designareas_js_tmpl');

		// Load dynamically created JS.
		// Echo $this->loadTemplate('designareas_js');
	?>

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
						<img id="background" src="<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->image_path; ?>"/>
						<?php
						if (!empty($this->areas))
						{
							foreach ($this->areas as $area)
							{
						?>
							<div id="areaDiv<?php echo $area->id; ?>">
								<?php echo $area->name; ?>
							</div>
						<?php
							}
						}
						?>
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
																$color_codes = explode(',', $area->color_code);
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



<script type="text/javascript">

/**
 * Initiate PX to Unit conversation variables
 */
var pxToUnit    = parseFloat('<?php echo $this->pxToUnit;?>');
var unitToPx    = parseFloat('<?php echo $this->unitToPx;?>');
var ratio       = parseFloat('<?php echo $this->ratio; ?>');
var imageWidth  = parseFloat('<?php echo $this->imageWidth; ?>') * unitToPx * ratio;
var imageHeight = parseFloat('<?php echo $this->imageHeight; ?>') * unitToPx * ratio;
var selectionObjectInstance;

/**
 * Initiate imgAreaSelect plugin
 */
jQuery(document).ready(
	function ($) {

		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			onInit: clearSelectionFields,
			onSelectEnd: populateSelectionFields
		});

		<?php

		if ($this->areas != '')
		{
			foreach ($this->areas as  $area)
			{
		?>
				var colorPicker<?php echo $area->id ?> = jQuery.farbtastic("#colorPickerContainer<?php echo $area->id ?>");
				colorPicker<?php echo $area->id ?>.linkTo("#colorPickerSelectedColor<?php echo $area->id; ?>");

				jQuery(document).on("keyup", "#C<?php echo $area->id; ?>", function() {
					var newColor = getNewHexColor(parseInt("<?php echo $area->id; ?>"));
					colorPicker<?php echo $area->id ?>.setColor(newColor);
				});

				jQuery(document).on("keyup", "#M<?php echo $area->id; ?>", function() {
					var newColor = getNewHexColor(parseInt("<?php echo $area->id; ?>"));
					colorPicker<?php echo $area->id ?>.setColor(newColor);
				});

				jQuery(document).on("keyup", "#Y<?php echo $area->id; ?>", function() {
					var newColor = getNewHexColor(parseInt("<?php echo $area->id; ?>"));
					colorPicker<?php echo $area->id ?>.setColor(newColor);
				});

				jQuery(document).on("keyup", "#K<?php echo $area->id; ?>", function() {
					var newColor = getNewHexColor(parseInt("<?php echo $area->id; ?>"));
					colorPicker<?php echo $area->id ?>.setColor(newColor);
				});

				jQuery(document).on("keyup", "#colorPickerSelectedColor<?php echo $area->id; ?>", function() {
					var hex = jQuery("#colorPickerSelectedColor<?php echo $area->id; ?>").val();
					loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
				});

				jQuery(document).on("mouseup", "#colorPickerContainer<?php echo $area->id; ?>", function() {
					var hex = jQuery("#colorPickerSelectedColor<?php echo $area->id; ?>").val();
					loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
				});

				jQuery("#allColors<?php echo $area->id; ?>").click(function () {
					jQuery("#colorsContainer<?php echo $area->id; ?>").toggle(!this.checked);
					jQuery("#addColorContainer<?php echo $area->id; ?>").toggle(!this.checked);
					jQuery("#selectedColorsPalette<?php echo $area->id; ?>").toggle(!this.checked);
				});


				jQuery("#addColorButton<?php echo $area->id ?>").click(function () {
					addColorToList(parseInt("<?php echo $area->id; ?>"))
				});
		<?php
			}
		}
		?>
	}
);

/**
 * Adds selected color to the list.
 *
 * @param areaId integer Area ID.
 *
 * @return void
 */
function addColorToList(areaId)
{

	var selectedColor = jQuery("#colorPickerSelectedColor" + areaId).val();
	var colorCodes = jQuery("#colorCodes" + areaId).val();

	// Check if the same color is already added.
	if (colorCodes.indexOf(selectedColor) == -1)
	{
		// Create color div element.
		var element = '<div class="colorDiv" ' +
			'id="' + areaId + '-' + selectedColor.replace("#","") + '" ' +
			'style="background-color:' + selectedColor + ';" ' +
			'onclick="removeColorFromList(' + areaId + ', \'' + selectedColor + '\');">' +
			'<i class="glyphicon icon-remove"></i>' +
			'<input type="hidden" value="' + selectedColor + '" />' +
		'</div>';
		jQuery("#selectedColorsPalette" + areaId).append(element);

		// Update color codes hidden input field.
		if (colorCodes == "" || parseInt(colorCodes) == 1)
		{
			colorCodes = selectedColor;
		}
		else
		{
			colorCodes = colorCodes + "," + selectedColor;
		}

		jQuery("#colorCodes" + areaId).val(colorCodes);
		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxUpdateColors&format=raw",
			data: {
				reddesign_area_id: areaId,
				color_code: colorCodes
			},
			type: "post",
			error: function (data) {
				alert(data);
			}
		});
	}
	else
	{
		alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_ALREADY_ADDED'); ?>");
	}
}

/**
 * Removes color from the list and from the database.
 *
 * @param areaId int Area ID.
 * @param colorToRemove string Hexadecimal code of the color to be removed.
 */
function removeColorFromList(areaId, colorToRemove)
{
	var colorCodes = jQuery("#colorCodes" + areaId).val();
	colorCodes = colorCodes.split(",");

	colorCodes = jQuery.grep(colorCodes, function(value) {
		return value != colorToRemove;
	});

	colorCodes = colorCodes.join(",");

	jQuery("#colorCodes" + areaId).val(colorCodes);

	jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxUpdateColors&format=raw",
		data: {
			reddesign_area_id: areaId,
			color_code: colorCodes
		},
		type: "post",
		error: function (data) {
			alert(data);
		}
	});

	jQuery("#" + areaId + "-" + colorToRemove.replace("#","")).remove();
}

/**
 * Gets hexadecimal value of the color generated by entering values in CMYK fields.
 *
 * @param areaId int Area ID
 *
 * @return string hexadecimal value
 */
function getNewHexColor(areaId)
{
	var c = jQuery("#C" + areaId).val();
	var m = jQuery("#M" + areaId).val();
	var y = jQuery("#Y" + areaId).val();
	var k = jQuery("#K" + areaId).val();

	var colorObject = new CMYK(c, m, y, k);
	var rgb = ColorConverter.toRGB(colorObject);

	return rgbToHex(rgb.r, rgb.g, rgb.b);
}

/**
 * Converts hexadecimal value into RGB value
 *
 * @param r int Red value
 * @param g int Green value
 * @param b int Blue value
 *
 * @return string hexadecimal value
 */
function rgbToHex(r, g, b) {
	return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

/**
 * Load CMYK values into related CMYK area fields.
 * Gets them from a hexadecimal value.
 *
 * @param hex int Hexadecimal value.
 * @param areaId int Area ID.
 */
function loadCMYKValues(hex, areaId) {
	var colorObject = new RGB(hexToRgb(hex).r, hexToRgb(hex).g, hexToRgb(hex).b);
	var cmyk = ColorConverter.toCMYK(colorObject);

	jQuery("#C" + areaId).val(cmyk.c);
	jQuery("#M" + areaId).val(cmyk.m);
	jQuery("#Y" + areaId).val(cmyk.y);
	jQuery("#K" + areaId).val(cmyk.k);
}

/**
 * Converts hexadecimal value to RGB value.
 *
 * @param hex string Hexadecimal value.
 *
 * @return object with r,g and b values.
 */
function hexToRgb(hex) {
	// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	hex = hex.replace(shorthandRegex, function(m, r, g, b) {
		return r + r + g + g + b + b;
	});

	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

/**
 * Selects area with given parameters. Used onkeyup event in parameter input fields.
 *
 * @param x1
 * @param y1
 * @param x2
 * @param y2
 */
function selectArea(x1, y1, x2, y2) {
	selectionObjectInstance = jQuery("img#background").imgAreaSelect({
		instance: true,
		handles: true,
		x1: x1,
		y1: y1,
		x2: x2,
		y2: y2
	});
}

/**
 * Updates selection with entered width.
 */
function selectAreaOnWidthKeyUp()
{
	var width, selection, x2;

	width = jQuery("#areaWidth").val();
	selection = selectionObjectInstance.getSelection();

	// Convert width to a coordinate.
	width = parseFloat(width) * unitToPx * ratio;

	// Calculate X2 coordinate
	x2 = selection.x1 + width;

	if (width > 0 && x2 < imageWidth)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered height.
 */
function selectAreaOnHeightKeyUp()
{
	var height, selection, y2;

	height = jQuery("#areaHeight").val();
	selection = selectionObjectInstance.getSelection();

	// Convert height to a coordinate.
	height = parseFloat(height) * unitToPx * ratio;
	y2 = selection.y1 + height;

	if (height > 0 && y2 < imageHeight)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: selection.y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered X1.
 */
function selectAreaOnX1KeyUp()
{
	var x1, x2, selection;

	x1 = jQuery("#areaX1").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	x1 = parseFloat(x1) * unitToPx * ratio;

	// Calculate X2 coordinate.
	x2 = x1 + selection.width;

	if(x1 > 0 && x2 < imageWidth)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered Y1.
 */
function selectAreaOnY1KeyUp()
{
	var y1, y2, selection;

	y1 = jQuery("#areaY1").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	y1 = parseFloat(y1) * unitToPx * ratio;

	// Calculate Y2 coordinate.
	y2 = y1 + selection.height;

	if(y1 > 0 && y2 < imageHeight)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered X2.
 */
function selectAreaOnX2KeyUp()
{
	var x2, x1, selection;

	x2 = jQuery("#areaX2").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	x2 = parseFloat(x2) * unitToPx * ratio;

	// Calculate X1 coordinate.
	x1 = x2 - selection.width;

	if(x2 < imageWidth && x1 > 0)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered Y2.
 */
function selectAreaOnY2KeyUp()
{
	var y2, y1, selection;

	y2 = jQuery("#areaY2").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	y2 = parseFloat(y2) * unitToPx * ratio;

	// Calculate X1 coordinate.
	y1 = y2 - selection.height;

	if(y2 < imageHeight && y1 > 0)
	{
		selectionObjectInstance = jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Populate fields from entered values.
 *
 * @param selection
 */
function populateFields(selection)
{
	// Convert pixel to selected unit. Use ratio to calculate and display real metrics instead of scaled down.
	var x1_pos_in_unit = (parseFloat(selection.x1) / ratio) * pxToUnit;
	var y1_pos_in_unit = (parseFloat(selection.y1) / ratio) * pxToUnit;
	var x2_pos_in_unit = (parseFloat(selection.x2) / ratio) * pxToUnit;
	var y2_pos_in_unit = (parseFloat(selection.y2) / ratio) * pxToUnit;
	var width_in_unit  = (parseFloat(selection.width) / ratio) * pxToUnit;
	var height_in_unit = (parseFloat(selection.height) / ratio) * pxToUnit;

	jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
	jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
	jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
	jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
	jQuery("#areaWidth").val(width_in_unit.toFixed(0));
	jQuery("#areaHeight").val(height_in_unit.toFixed(0));
}

/**
 * Populates parameter fields from selected area
 *
 * @param img
 * @param selection
 */
function populateSelectionFields(img, selection) {
	if(selection.width == 0 || selection.height == 0)
	{
		clearSelectionFields();
		updateImageAreas();
	}
	else
	{
		// Convert pixel to selected unit. Use ratio to calculate and display real metrics instead of scaled down.
		var x1_pos_in_unit = (parseFloat(selection.x1) / ratio) * pxToUnit;
		var y1_pos_in_unit = (parseFloat(selection.y1) / ratio) * pxToUnit;
		var x2_pos_in_unit = (parseFloat(selection.x2) / ratio) * pxToUnit;
		var y2_pos_in_unit = (parseFloat(selection.y2) / ratio) * pxToUnit;
		var width_in_unit  = (parseFloat(selection.width) / ratio) * pxToUnit;
		var height_in_unit = (parseFloat(selection.height) / ratio) * pxToUnit;

		jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
		jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
		jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
		jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
		jQuery("#areaWidth").val(width_in_unit.toFixed(0));
		jQuery("#areaHeight").val(height_in_unit.toFixed(0));
	}
}

/**
 * Clears parameter input fields. Used when select area is not displayed anymore.
 */
function clearSelectionFields() {
	jQuery("#designAreaId").val("0");
	jQuery("#areaName").val("");
	jQuery("#areaX1").val("");
	jQuery("#areaY1").val("");
	jQuery("#areaX2").val("");
	jQuery("#areaY2").val("");
	jQuery("#areaWidth").val("");
	jQuery("#areaHeight").val("");
}

/**
 * Makes sure that the area has a name, alert otherwise
 *
 * @param update
 */
function preSaveArea(update) {
	if(!jQuery("#areaName").val())
	{
		alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_AREA_NAME'); ?>");
	}
	else
	{
		saveArea(update);
	}
}

/**
 * Saves area into the DB via AJAX. And prepares image for another selection.
 *
 * @param update
 */
function saveArea(update)
{
	jQuery("#saveAreaBtn").button("loading");

	var reddesign_area_id;
	var areaName	= jQuery("#areaName").val();
	var areaX1 		= jQuery("#areaX1").val();
	var areaY1 		= jQuery("#areaY1").val();
	var areaX2 		= jQuery("#areaX2").val();
	var areaY2 		= jQuery("#areaY2").val();
	var areaWidth  	= jQuery("#areaWidth").val();
	var areaHeight 	= jQuery("#areaHeight").val();

	var areaX1_in_px 		= (areaX1 * unitToPx * ratio).toFixed(0);
	var areaY1_in_px 		= (areaY1 * unitToPx * ratio).toFixed(0);
	var areaX2_in_px 		= (areaX2 * unitToPx * ratio).toFixed(0);
	var areaY2_in_px 		= (areaY2 * unitToPx * ratio).toFixed(0);
	var areaWidth_in_px 	= (areaWidth * unitToPx * ratio).toFixed(0);
	var areaHeight_in_px 	= (areaHeight * unitToPx * ratio).toFixed(0);

	if(update != 0)
	{
		// if update is not 0 than it holds reddesign_area_id and we are doing update of existing area
		reddesign_area_id = update;
	}
	else
	{
		reddesign_area_id = '';
	}

	jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxSave",
		data: {
			'jform[id]': reddesign_area_id,
			'jform[name]': areaName,
			'jform[reddesign_background_id]': <?php echo $this->productionBackground->id; ?>,
			'jform[x1_pos]': areaX1_in_px,
			'jform[y1_pos]': areaY1_in_px,
			'jform[x2_pos]': areaX2_in_px,
			'jform[y2_pos]': areaY2_in_px,
			'jform[width]': areaWidth_in_px,
			'jform[height]': areaHeight_in_px,
		},
		type: "post",
		success: function (data)
		{
			var json = jQuery.parseJSON(data);

			if (update == 0)
			{
				drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
				addAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
				clearAreaSelection();
				clearSelectionFields();
			}
			else
			{
				jQuery("#areaDiv" + reddesign_area_id).remove();
				drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
				jQuery("#areaDiv" + reddesign_area_id).html(areaName + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
				updateAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
			}

			jQuery("#saveAreaBtn").button("reset");
			// setTimeout(function () {jQuery("#saveAreaBtn").button("reset")}, 500);
		},
		error: function (data)
		{
			alert(data);
		}
	});
}

/**
 * Draws saved area onto an image.
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param width
 * @param height
 */
function drawArea(reddesign_area_id, title, x1_pos, y1_pos, width, height) {
	width -= 2;
	height -= 3;

	jQuery("#backgroundImageContainer").append(
		'<div id="areaDiv' + reddesign_area_id + '" ' +
			'style="position: absolute; ' +
			'width: ' + width + 'px; ' +
			'height: ' + height + 'px; ' +
			'left: ' + x1_pos + 'px; ' +
			'top: ' + y1_pos + 'px; ' +
			'color: rgb(91, 91, 169); border: 2px solid rgb(91, 91, 169);"' +
			'>' + title + '</div>');
}

/**
 * Adds area row to the template table
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function addAreaRow(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	var lastClass = jQuery("#areasTBody tr").last().attr("class");
	var rowClass;

	if (lastClass == "row0")
	{
		rowClass = "row1";
	}
	else
	{
		rowClass = "row0";
	}
	jQuery('#noAreaMessage').remove();

	var areasRowData = {
		reddesignAreaId: reddesign_area_id,
		title:			 title,
		x1:				 x1_pos,
		x1ToUnit:		(x1_pos * pxToUnit).toFixed(0),
		y1:				 y1_pos,
		y1ToUnit:		(y1_pos * pxToUnit).toFixed(0),
		x2:				 x2_pos,
		x2ToUnit:		(x2_pos * pxToUnit).toFixed(0),
		y2:				 y2_pos,
		y2ToUnit:		(y2_pos * pxToUnit).toFixed(0),
		width:			 width,
		widthToUnit:	(width * pxToUnit).toFixed(0),
		height:			 height,
		heightToUnit:	(height * pxToUnit).toFixed(0),
		rowClass: rowClass
	};

	var areasRowTemplate = jQuery("#areaRowsMustache").html();
	var areaRowRendered = Mustache.render(areasRowTemplate, areasRowData);

	jQuery("#areasTBody").append(areaRowRendered);

	<?php if ($this->item->fontsizer != 'auto' && $this->item->fontsizer != 'auto_chars') : ?>
		<?php foreach($this->alignmentOptions as  $alginmentOption) : ?>
			jQuery("#areaFontAlignment" + reddesign_area_id).append(
				'<option value="<?php echo $alginmentOption->value; ?>">' +
					'<?php echo $alginmentOption->text; ?>' +
				'</option>'
			);
		<?php endforeach; ?>
	<?php endif; ?>

	<?php foreach($this->fontsOptions as  $fontsOption) : ?>
		jQuery("#areaFonts" + reddesign_area_id).append(
			'<option value="<?php echo $fontsOption->value; ?>">' +
				'<?php echo $fontsOption->text; ?>' +
			'</option>'
		);
	<?php endforeach; ?>

	var colorPicker = jQuery.farbtastic("#colorPickerContainer" + reddesign_area_id);
	colorPicker.linkTo("#colorPickerSelectedColor" + reddesign_area_id);

	jQuery(document).on("keyup", "#C" + reddesign_area_id, function() {
		var newColor = getNewHexColor(reddesign_area_id);
		colorPicker.setColor(newColor);
	});

	jQuery(document).on("keyup", "#M" + reddesign_area_id, function() {
		var newColor = getNewHexColor(reddesign_area_id);
		colorPicker.setColor(newColor);
	});

	jQuery(document).on("keyup", "#Y" + reddesign_area_id, function() {
		var newColor = getNewHexColor(reddesign_area_id);
		colorPicker.setColor(newColor);
	});

	jQuery(document).on("keyup", "#K" + reddesign_area_id, function() {
		var newColor = getNewHexColor(reddesign_area_id);
		colorPicker.setColor(newColor);
	});

	jQuery(document).on("keyup", "#colorPickerSelectedColor" + reddesign_area_id, function() {
		var hex = jQuery("#colorPickerSelectedColor" + reddesign_area_id).val();
		loadCMYKValues(hex, reddesign_area_id);
	});

	jQuery(document).on("mouseup", "#colorPickerContainer" + reddesign_area_id, function() {
		var hex = jQuery("#colorPickerSelectedColor" + reddesign_area_id).val();
		loadCMYKValues(hex, reddesign_area_id);
	});

	jQuery("#allColors" + reddesign_area_id).click(function () {
		jQuery("#colorsContainer" + reddesign_area_id).toggle(!this.checked);
		jQuery("#addColorContainer" + reddesign_area_id).toggle(!this.checked);
		jQuery("#selectedColorsPalette" + reddesign_area_id).toggle(!this.checked);
	});

	jQuery("#addColorButton" + reddesign_area_id).click(function () {
		addColorToList(reddesign_area_id);
	});
}

/**
 * Updates area row in the template table
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function updateAreaRow(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	jQuery("#areaRow" + reddesign_area_id).html(
		'<td>' + reddesign_area_id + '</td>' +
			'<td>' +
			'<a href="#" onclick="selectAreaForEdit(' + reddesign_area_id + ',\'' +
			title + '\',' +
			x1_pos + ',' +
			y1_pos + ',' +
			x2_pos + ',' +
			y2_pos + ',' +
			width  + ',' +
			height + ')">' +
			'<strong>' + title + '</strong>' +
			'</a>' +
			'</td>' +
			'<td>' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong> ' +
			(width * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong> ' +
			(height * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong> ' +
			(x1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong> ' +
			(y1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong> ' +
			(x2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong> ' +
			(y2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?> ' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-primary btn-mini" onclick="showAreaSettings(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>' +
			'</button>' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-danger btn-mini" onclick="removeArea(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>' +
			'</button>' +
			'</td>'
	);
}

/**
 * Uses AJAX to update image with areas
 */
function updateImageAreas() {
	var json;

	jQuery("#backgroundImageContainer div").remove();

	jQuery.ajax({
		data: {
			reddesign_background_id: <?php echo $this->productionBackground->id; ?>
		},
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxGetAreas&format=raw",
		success: function (data) {
			json = jQuery.parseJSON(data);
			jQuery.each( json, function( key, value ) {
				drawArea(value.reddesign_area_id, value.title, value.x1_pos, value.y1_pos, value.width, value.height)
			});
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * Selects area for edit and populates field data accordingly
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function selectAreaForEdit(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	jQuery("#designAreaId").val(reddesign_area_id);
	jQuery("#areaName").val(title);

	// Convert pixel to selected unit. Use ratio to calculate and display real mertics instead of scaled down.
	var x1_pos_in_unit = (parseFloat(x1_pos) / ratio) * pxToUnit;
	var y1_pos_in_unit = (parseFloat(y1_pos) / ratio) * pxToUnit;
	var x2_pos_in_unit = (parseFloat(x2_pos) / ratio) * pxToUnit;
	var y2_pos_in_unit = (parseFloat(y2_pos) / ratio) * pxToUnit;
	var width_in_unit  = (parseFloat(width) / ratio) * pxToUnit;
	var height_in_unit = (parseFloat(height) / ratio) * pxToUnit;

	jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
	jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
	jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
	jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
	jQuery("#areaWidth").val(width_in_unit.toFixed(0));
	jQuery("#areaHeight").val(height_in_unit.toFixed(0));

	selectArea(x1_pos, y1_pos, x2_pos, y2_pos);

	jQuery("#areaDiv" + reddesign_area_id).html(title + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
}

/**
 * Function for cancel button
 */
function cancelArea() {
	clearSelectionFields();
	clearAreaSelection();
	updateImageAreas();
}

/**
 * Deletes an area.
 *
 * @param reddesign_area_id
 */
function removeArea(reddesign_area_id) {
	jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxRemove&format=raw",
		data: {
			reddesign_area_id: reddesign_area_id
		},
		type: "post",
		success: function (data) {
			jQuery("#areaRow" + reddesign_area_id).remove();
			jQuery("#areaSettingsRow" + reddesign_area_id).remove();
			updateImageAreas();
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * This function helps jquery slide effect because it doesn't work well with table rows
 *
 * @param reddesign_area_id
 */
function showAreaSettings(reddesign_area_id) {
	jQuery(".areaSettingsRow").hide();
	jQuery("#areaSettingsRow" + reddesign_area_id).slideToggle("slow");
}

/**
 * Saves settings for an area
 *
 * @param reddesign_area_id
 */
function saveAreaSettings(reddesign_area_id) {
	jQuery("#saveAreaSettings" + reddesign_area_id).button("loading");

	var areaFontAlignment = jQuery("#areaFontAlignment" + reddesign_area_id).val();
	var fontsizerDropdown = jQuery("#fontsizerDropdown" + reddesign_area_id).val();
	var fontsizerSliderDefault = jQuery("#fontsizerSliderDefault" + reddesign_area_id).val();
	var fontsizerSliderMin = jQuery("#fontsizerSliderMin" + reddesign_area_id).val();
	var fontsizerSliderMax = jQuery("#fontsizerSliderMax" + reddesign_area_id).val();
	var inputFieldType = jQuery("#inputFieldType" + reddesign_area_id).val();
	var maximumCharsAllowed = jQuery("#maximumCharsAllowed" + reddesign_area_id).val();
	var maximumLinesAllowed = jQuery("#maximumLinesAllowed" + reddesign_area_id).val();
	var areaFonts = jQuery('[name="areaFonts' + reddesign_area_id + '[]"]').val();
	var colorCodes = jQuery("#colorCodes" + reddesign_area_id).val();
	var defaultText = jQuery("#defaultText" + reddesign_area_id).val();

	if (jQuery("#allColors" + reddesign_area_id).is(":checked"))
	{
		colorCodes = 1;
	}

	jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxSave&format=raw",
		data: {
			reddesign_area_id: reddesign_area_id,
			textalign: areaFontAlignment,
			font_id: areaFonts,
			font_size: fontsizerDropdown,
			defaultFontSize: fontsizerSliderDefault,
			minFontSize: fontsizerSliderMin,
			maxFontSize: fontsizerSliderMax,
			input_field_type: inputFieldType,
			maxchar: maximumCharsAllowed,
			maxline: maximumLinesAllowed,
			color_code: colorCodes,
			default_text: defaultText
		},
		type: "post",
		success: function (data) {
			setTimeout(function () {jQuery("#saveAreaSettings" + reddesign_area_id).button("reset")}, 500);
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * Controls what needs to be shown regarding to input field type.
 *
 * @param reddesign_area_id
 */
function changeInputFieldType(reddesign_area_id)
{
	var selectedType = jQuery("#inputFieldType" + reddesign_area_id).val();

	if (selectedType == 1)
	{
		jQuery("#maximumLinesAllowedContainer" + reddesign_area_id).css("display", "inline");
	}
	else
	{
		jQuery("#maximumLinesAllowedContainer" + reddesign_area_id).css("display", "none");
	}
}

</script>

<style type="text/css">
	#backgroundImageContainer {
		position: relative;
	}

	<?php foreach($this->areas as $area) : ?>
	#areaDiv<?php echo $area->id; ?> {
		position: absolute;
		top: <?php echo $area->y1_pos;?>px;
		left: <?php echo $area->x1_pos;?>px;
		width: <?php echo $area->width;?>px;
		height: <?php echo $area->height;?>px;
		color: #5B5BA9;
		border: 2px solid #5B5BA9;
	}
	<?php endforeach; ?>
</style>