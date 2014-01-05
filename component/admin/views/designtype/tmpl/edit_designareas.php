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

?>

<?php if (empty($this->productionBackground)) : ?>

	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND_DESC'); ?></span>

<?php else : ?>

	<?php
		// Load JS template for design area setting rows.
		echo $this->loadTemplate('designareas_js_tmpl');

		// Load dynamically created JS.
		echo $this->loadTemplate('designareas_js');
	?>

	<div class="designAreasContainer">
		<h3><?php echo JText::sprintf('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS', $this->productionBackground->title); ?></h3>
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
						<img id="background"
							 src="<?php echo JURI::base() . '/media/com_reddesign/assets/backgrounds/' . $this->productionBackground->image_path; ?>"/>
						<?php foreach($this->areas as $area) : ?>
							<div id="areaDiv<?php echo $area->reddesign_area_id; ?>">
								<?php echo $area->title; ?>
							</div>
						<?php endforeach; ?>
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
					<tr id="areaRow<?php echo $area->reddesign_area_id; ?>"
						class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $area->reddesign_area_id; ?>
						</td>
						<td class="span4">
							<a href="#" onclick="selectAreaForEdit(<?php echo $area->reddesign_area_id . ',\'' .
								$area->title . '\',' .
								$area->x1_pos . ',' .
								$area->y1_pos . ',' .
								$area->x2_pos . ',' .
								$area->y2_pos . ',' .
								$area->width . ',' .
								$area->height; ?>);">
								<strong><?php echo $area->title; ?></strong>
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
									onclick="showAreaSettings(<?php echo $area->reddesign_area_id; ?>);">
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>
							</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-mini" onclick="removeArea(<?php echo $area->reddesign_area_id; ?>);">
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>

					<tr id="areaSettingsRow<?php echo $area->reddesign_area_id ?>" class="<?php echo 'row' . $m; ?> hide areaSettingsRow">
						<td colspan="5" >

							<div class="row-fluid">
								<div class="span4">
									<fieldset>

										<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_FONT_SETTINGS'); ?></legend>

										<div class="row-fluid">
											<div class="span6">

												<?php if($this->item->fontsizer != 'auto' && $this->item->fontsizer != 'auto_chars') : ?>

													<div class="control-group">
														<label for="<?php echo 'areaFontAlignment' . $area->reddesign_area_id; ?>">
															<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>
															<?php
																echo JHtml::_('select.genericlist',
																	$this->alignmentOptions,
																	'areaFontAlignment' . $area->reddesign_area_id,
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
													<label for="<?php echo 'areaFonts' . $area->reddesign_area_id; ?>">
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALLOWED_FONTS') ?>
														<?php
															echo JHtml::_(
																			'select.genericlist',
																			$this->fontsOptions,
																			'areaFonts' . $area->reddesign_area_id . '[]',
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
														<label for="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>">
															<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>
															<textarea class="span12"
																	  style="resize: none;"
																	  id="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>"
																	  name="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>"
																	  rows="7"
																><?php echo $area->font_size; ?></textarea>
															<span class="help-block">
																<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC') ?>
															</span>
														</label>
													</div>

												<?php elseif ($this->item->fontsizer == 'slider') : ?>

													<div class="control-group">
														<label for="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>">
															<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_FONT_SIZE') ?>
															<input class="input-mini"
																   type="text"
																   value="<?php echo $area->defaultFontSize; ?>"
																   maxlength="3"
																   id="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>"
																   name="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>"
																/>
														</label>
													</div>

													<div class="control-group">
														<label for="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>">
															<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MIN_FONT_SIZE') ?>
															<input class="input-mini"
																   type="text"
																   value="<?php echo $area->minFontSize; ?>"
																   maxlength="3"
																   id="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>"
																   name="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>"
																/>
														</label>
													</div>

													<div class="control-group">
														<label for="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>">
															<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAX_FONT_SITE') ?>
															<input class="input-mini"
																   type="text"
																   value="<?php echo $area->maxFontSize; ?>"
																   maxlength="3"
																   id="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>"
																   name="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>"
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
																			'inputFieldType' . $area->reddesign_area_id . '[]',
																			' onclick="changeInputFieldType(' . $area->reddesign_area_id . ');" ',
																			'value',
																			'text',
																			$area->input_field_type,
																			'inputFieldType' . $area->reddesign_area_id
															);
														?>
												</label>
											</div>

											<div class="control-group">
												<label for="defaultText<?php echo $area->reddesign_area_id; ?>">

													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_TEXT'); ?>

													<div id="defaultTextContainer<?php echo $area->reddesign_area_id; ?>">
														<textarea class="input-medium"
																  style="resize: none;"
																  id="defaultText<?php echo $area->reddesign_area_id; ?>"
																  name="defaultText<?php echo $area->reddesign_area_id; ?>"
														><?php echo $area->default_text; ?></textarea>
													</div>

												</label>
											</div>

											<div class="control-group">
												<label for="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>">

													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_CHARS'); ?>

													<input class="input-mini"
														   type="text"
														   value="<?php echo $area->maxchar; ?>"
														   id="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>"
														   name="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>"
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

											<div class="control-group" id="maximumLinesAllowedContainer<?php echo $area->reddesign_area_id ?>" <?php echo $style; ?>>
												<label for="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>">

													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_LINES') ?>

													<input class="input-mini"
														   type="text"
														   value="<?php echo $area->maxline; ?>"
														   id="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>"
														   name="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>"
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
													<label class="checkbox inline" for="allColors<?php echo $area->reddesign_area_id; ?>">
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
															   id="allColors<?php echo $area->reddesign_area_id; ?>"
															   name="allColors<?php echo $area->reddesign_area_id; ?>"
															   value="allColors"
															<?php echo $chkAllColors; ?>
															>
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_CHECK_ALL_COLORS'); ?>
													</label>
												</div>

												<div id="addColorContainer<?php echo $area->reddesign_area_id ?>" class="span12 addColorButton" <?php echo $allColorsHide; ?> >
													<button class="btn btn-mini btn-success" id="addColorButton<?php echo $area->reddesign_area_id ?>" type="button">
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ADD_COLOR'); ?>
													</button>
												</div>

												<?php $colorCodes = explode(',', $area->color_code); ?>
												<div id="selectedColorsPalette<?php echo $area->reddesign_area_id ?>" class="span12" <?php echo $allColorsHide; ?> >
													<?php foreach ($colorCodes as $colorCode) : ?>
														<?php if (!empty($colorCode) && $colorCode != 1) : ?>
															<div class="colorDiv"
																 id="<?php echo $area->reddesign_area_id . '-' . str_replace('#', '', $colorCode); ?>"
																 style="background-color: <?php echo $colorCode; ?>;"
																 onclick="removeColorFromList(<?php echo $area->reddesign_area_id ?>, '<?php echo $colorCode; ?>');">
																<i class="glyphicon icon-remove"></i>
															</div>
														<?php endif; ?>
													<?php endforeach; ?>
												</div>

												<input type="hidden"
													   id="colorCodes<?php echo $area->reddesign_area_id ?>"
													   name="colorCodes<?php echo $area->reddesign_area_id ?>"
													   value="<?php echo $area->color_code; ?>"
													/>
											</div>

											<div id="colorsContainer<?php echo $area->reddesign_area_id ?>" class="span6" <?php echo $allColorsHide; ?>>

												<div class="span9">
													<label class="control-label">
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
														<div id="colorPickerContainer<?php echo $area->reddesign_area_id; ?>" class="colorPickerContainer"></div>
													</label>

													<label for="colorPickerSelectedColor<?php echo $area->reddesign_area_id; ?>">
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?>
														<input class="span12 colorPickerSelectedColor"
															   type="text"
															   value="#cfcfcf"
															   id="colorPickerSelectedColor<?php echo $area->reddesign_area_id; ?>"
															   name="colorPickerSelectedColor<?php echo $area->reddesign_area_id; ?>"
															/>
													</label>
												</div>

												<div class="span3 CMYKContainer">
													<div class="input-prepend">
														<span class="add-on">C</span>
														<input class="span8"
															   id="C<?php echo $area->reddesign_area_id; ?>"
															   name="C<?php echo $area->reddesign_area_id; ?>"
															   type="text"
															   value="10"
															   placeholder="C"
															>
													</div>
													<div class="input-prepend">
														<span class="add-on">M</span>
														<input class="span8"
															   id="M<?php echo $area->reddesign_area_id; ?>"
															   name="M<?php echo $area->reddesign_area_id; ?>"
															   type="text"
															   value="10"
															   placeholder="M"
															>
													</div>
													<div class="input-prepend">
														<span class="add-on">Y</span>
														<input class="span8"
															   id="Y<?php echo $area->reddesign_area_id; ?>"
															   name="Y<?php echo $area->reddesign_area_id; ?>"
															   type="text"
															   value="10"
															   placeholder="Y"
															>
													</div>
													<div class="input-prepend">
														<span class="add-on">K</span>
														<input class="span8"
															   id="K<?php echo $area->reddesign_area_id; ?>"
															   name="K<?php echo $area->reddesign_area_id; ?>"
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
									<button id="saveAreaSettings<?php echo $area->reddesign_area_id; ?>"
											type="button"
											class="btn btn-success"
											data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>"
											onclick="saveAreaSettings(<?php echo $area->reddesign_area_id; ?>);">
										<span>
											<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
										</span>
									</button>
									<button type="button"
											class="btn"
											onclick="showAreaSettings(<?php echo $area->reddesign_area_id; ?>);">
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
