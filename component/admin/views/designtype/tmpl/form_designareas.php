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
FOFTemplateUtils::addCSS('media://com_reddesign/assets/css/imgareaselect-animated.css');
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/colorpicker.js');
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/selectionboxmove.js');
FOFTemplateUtils::addCSS('media://com_reddesign/assets/css/colorpicker.css');
?>

<?php if (empty($this->productionBackground)) : ?>

	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND_DESC'); ?></span>

<?php else : ?>

	<?php
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
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_WIDTH') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaWidth" name="areaWidth"
									value=""
									onkeyup="selectAreaOnWidthKeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
					<div class="control-group">
						<label for="areaHeight" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_HEIGHT') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaHeight" name="areaHeight"
									value=""
									onkeyup="selectAreaOnHeightKeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
					<div class="control-group">
						<label for="areaX1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X1'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX1" name="areaX1"
									value=""
									onkeyup="selectAreaOnX1KeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
					<div class="control-group">
						<label for="areaY1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y1') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY1" name="areaY1"
									value=""
									onkeyup="selectAreaOnY1KeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
					<div class="control-group">
						<label for="areaX2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X2') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX2" name="areaX2"
									value=""
									onkeyup="selectAreaOnX2KeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
					<div class="control-group">
						<label for="areaY2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y2') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY2" name="areaY2"
									value=""
									onkeyup="selectAreaOnY2KeyUp();">&nbsp;<?php echo $this->unit ?>
						</div>
					</div>
				</div>
				<div class="span9">
					<span class="help-block">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMG_HELP'); ?>
					</span>
					<div id="backgroundImageContainer">
						<img id="background"
								 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->productionBackground->image_path; ?>"/>
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
						onclick="preSaveArea(akeeba.jQuery('#designAreaId').val());"
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
					<tr id="areaSettingsRow<?php echo $area->reddesign_area_id; ?>"
						class="<?php echo 'row' . $m; ?> hide areaSettingsRow">
						<td colspan="5" >

							<div id="row">

								<div class="span3">

									<?php if($this->item->fontsizer != 'auto') : ?>
										<div class="control-group">
											<label for="<?php echo 'areaFontAlignment' . $area->reddesign_area_id; ?>">
												<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>
											</label>
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
										</div>
									<?php endif; ?>

									<div class="control-group">
										<label for="<?php echo 'areaFonts' . $area->reddesign_area_id; ?>">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALLOWED_FONTS') ?>
										</label>
										<?php
											echo JHtml::_('select.genericlist',
															$this->fontsOptions,
															'areaFonts' . $area->reddesign_area_id . '[]',
															' multiple="multiple" ',
															'value',
															'text',
															explode(',', $area->font_id)
											);
										?>
									</div>

								</div>

								<?php if($this->item->fontsizer != 'auto') : ?>

									<div class="span2">

										<?php if($this->item->fontsizer == 'dropdown') : ?>
											<div class="control-group">
												<label for="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>">
													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>
												</label>
												<textarea class="input-small"
														  style="resize: none;"
														  id="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>"
														  name="fontsizerDropdown<?php echo $area->reddesign_area_id; ?>"
														  rows="7"
													><?php echo $area->font_size; ?></textarea>
												<span class="help-block">
													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC') ?>
												</span>
											</div>
										<?php elseif($this->item->fontsizer == 'slider') : ?>
											<div class="control-group">
												<label for="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>">
													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_FONT_SIZE') ?>
												</label>
												<input class="input-small"
													   type="text"
													   value="<?php echo $area->defaultFontSize; ?>"
													   maxlength="3"
													   id="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>"
													   name="fontsizerSliderDefault<?php echo $area->reddesign_area_id; ?>"
												/>
											</div>
											<div class="control-group">
												<label for="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>">
													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MIN_FONT_SIZE') ?>
												</label>
												<input class="input-small"
													   type="text"
													   value="<?php echo $area->minFontSize; ?>"
													   maxlength="3"
													   id="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>"
													   name="fontsizerSliderMin<?php echo $area->reddesign_area_id; ?>"
												/>
											</div>
											<div class="control-group">
												<label for="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>">
													<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAX_FONT_SITE') ?>
												</label>
												<input class="input-small"
													   type="text"
													   value="<?php echo $area->maxFontSize; ?>"
													   maxlength="3"
													   id="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>"
													   name="fontsizerSliderMax<?php echo $area->reddesign_area_id; ?>"
												/>
											</div>
										<?php endif; ?>

									</div>

								<?php endif; ?>

								<div class="span3">

									<div class="control-group">
										<label for="inputFieldType<?php echo $area->reddesign_area_id; ?>">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_INPUT_FIELD_TYPE') ?>
										</label>
										<?php
											echo JHtml::_('select.radiolist',
															$this->inputFieldOptions,
															'inputFieldType' . $area->reddesign_area_id . '[]',
															' onclick="changeInputFieldType(' . $area->reddesign_area_id . ');" ',
															'value',
															'text',
															$area->input_field_type
											);
										?>
									</div>

									<div class="control-group">
										<label for="defaultText<?php echo $area->reddesign_area_id; ?>">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_TEXT') ?>
										</label>
										<div id="defaultTextContainer<?php echo $area->reddesign_area_id; ?>">
											<textarea class="input-small"
													  style="resize: none;"
													  id="defaultText<?php echo $area->reddesign_area_id; ?>"
													  name="defaultText<?php echo $area->reddesign_area_id; ?>"
											><?php echo $area->default_text; ?></textarea>
										</div>
									</div>

									<div class="control-group">
										<label for="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_CHARS') ?>
										</label>
										<input class="input-small"
											   type="text"
											   value="<?php echo $area->maxchar; ?>"
											   id="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>"
											   name="maximumCharsAllowed<?php echo $area->reddesign_area_id; ?>"
											/>
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

									<div class="control-group">
										<label id="maximumLinesAllowedLabel<?php echo $area->reddesign_area_id ?>"
											   for="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>"
											   <?php echo $style; ?>
										>
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_LINES') ?>
										</label>
										<input class="input-small"
											   type="text"
											   value="<?php echo $area->maxline; ?>"
											   id="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>"
											   name="maximumLinesAllowed<?php echo $area->reddesign_area_id; ?>"
											   <?php echo $style; ?>
											/>
									</div>

								</div>

								<div class="span3">
									<?php
										$colorCode = $area->color_code;

										if ($colorCode == 1 || $colorCode == '1')
										{
											$style = "style='display:none;'";
										}
										else
										{
											$style = "style='display:block;'";
										}
									?>

									<div class="control-group">
										<label class="control-label ">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR'); ?>
										</label>
										<div class="controls">
											<?php echo $this->colorCodes['allColor' . $area->reddesign_area_id];?>
											<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR_DESC'); ?></span>
										</div>
									</div>
									<div id="allowedColorsRow<?php echo $area->reddesign_area_id?>" <?php echo $style;?>>
										<div class="control-group">
											<div class="controls">
												<input type="text"
													   class="input-small"
													   value="ff0000"
													   id="color_code<?php echo $area->reddesign_area_id;?>"
													   name="color_code<?php echo $area->reddesign_area_id;?>"
													/>
												<button type="button" class="btn btn-small btn-success" onclick="addNewcolor('<?php echo $area->reddesign_area_id;?>');">
													<span>
														<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_ADD_COLOR'); ?>
													</span>
												</button>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label ">
												<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_ALLOWED_COLOR'); ?>
											</label>
											<div class="controls">

												<table class="loadcolor" id="extra_table<?php echo $area->reddesign_area_id;?>" cellpadding="2" cellspacing="2">
												<?php

													if ($this->colorCodes["color_" . $area->reddesign_area_id] != "1" )
													{
														if (strpos($this->colorCodes["color_" . $area->reddesign_area_id], "#") !== false)
														{
															$colorData = explode(",", $this->colorCodes["color_" . $area->reddesign_area_id]);

															for ($j = 0;$j < count($colorData); $j++)
															{
																$colorCodeVal = str_replace("#", "", $colorData[$j]);
												?>
													<tr valign="top" class="color">
														<td>
															<div class="colorSelector_list">
																<div style="background-color:<?php echo $colorData[$j]?>">&nbsp;</div>
															</div>
														</td>
														<td>
															<div>
																<?php echo $colorData[$j]; ?>
															</div>
															<input type="hidden"
																   class="colorCodes<?php echo $area->reddesign_area_id?>"
																   name="colorCodes<?php echo $area->reddesign_area_id?>[]"
																   value="<?php echo $colorCodeVal; ?>"
																   id="colorCodes<?php echo $area->reddesign_area_id?>"
																/>
														</td>
														<td>
															<div>
																<button type="button" class="btn btn-small btn-danger delete" onclick="deleteColor(this,'<?php echo $area->reddesign_area_id?>');">
																	<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
																</button>
																<input type="hidden"
																	   name="colour_id<?php echo $area->reddesign_area_id?>[]"
																	   id="colour_id<?php echo $area->reddesign_area_id?>"
																	   value="<?php echo $colorData[$j] ?>"
																	/>
															</div>
														</td>
													</tr>
												<?php
															}
														}
													}
												?>
												</table>

												<input type="hidden"
													   name="reddesign_color_code<?php echo $area->reddesign_area_id?>"
													   id="reddesign_color_code<?php echo $area->reddesign_area_id?>"
													   value="<?php echo $area->color_code?>"
													/>
											</div>
										</div>
									</div>
								</div>

								<div class="areSettingRowheight">
									<div id="colorPicker<?php echo $area->reddesign_area_id ?>" <?php echo $style;?>>
										<div class="control-group" >
											<label class="control-label ">
												<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_TEXT'); ?>
											</label>
											<div class="controls">
												<p id="colorpickerHolderC<?php echo $area->reddesign_area_id;?>"></p>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row span12 offset5">

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