<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = $displayData;
$area = $data['area'];
$designType = $data['designType'];

JHtml::_('rjquery.select2', 'select');

?>
<div class="row-fluid">
	<div class="span4 col-md4">
		<fieldset>
			<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_FONT_SETTINGS'); ?></legend>
			<div class="row-fluid">
				<div class="span6 col-md6">

					<?php if($designType->fontsizer != 'auto') : ?>

						<div class="control-group">
							<label for="<?php echo 'areaFontAlignment' . $area->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>
								<?php
								echo JHtml::_('select.genericlist',
									ReddesignHelpersArea::getAreaHorizontalAlignmentOptions(),
									'areaFontAlignment' . $area->id,
									'',
									'value',
									'text',
									$area->textalign
								);
								?>
							</label>
						</div>

						<div class="control-group">
							<label for="<?php echo 'areaVerticalAlignment' . $area->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_VERTICAL_ALIGNMENT') ?>
								<?php
								echo JHtml::_('select.genericlist',
									ReddesignHelpersArea::getAreaVerticalAlignmentOptions(),
									'areaVerticalAlignment' . $area->id,
									'',
									'value',
									'text',
									$area->verticalAlign
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
								ReddesignHelpersFont::getFontSelectOptions(),
								'areaFonts' . $area->id . '[]',
								' multiple="multiple" ',
								'value',
								'text',
								ReddesignHelpersFont::getSelectedFontsFromArea(array($area))
							);
							?>
						</label>
					</div>

				</div>
				<div class="offset2 span4 col-md4">

					<?php if ($designType->fontsizer == 'dropdown_numbers' || $designType->fontsizer == 'dropdown_labels') : ?>

						<div class="control-group">
							<label for="fontsizerDropdown<?php echo $area->id; ?>">
								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>
								<textarea class="span12 col-md12"
								          style="resize: none;"
								          id="fontsizerDropdown<?php echo $area->id; ?>"
								          name="fontsizerDropdown<?php echo $area->id; ?>"
								          rows="7"
									><?php echo $area->font_size; ?></textarea>
									<span class="help-block">
										<?php if ($designType->fontsizer == 'dropdown_numbers'):
											echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC');
										else:
											echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZE_LABELS_DESC');
										endif; ?>
									</span>
							</label>
						</div>

					<?php elseif ($designType->fontsizer == 'slider') : ?>

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

	<div class="span3 col-md3">
		<fieldset>

			<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_TEXT_INPUT_SETTINGS'); ?></legend>

			<div class="row-fluid">

				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_INPUT_FIELD_TYPE') ?>
						<?php
						echo JHtml::_('select.genericlist',
							ReddesignHelpersArea::getAreaTextTypeOptions(),
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

	<div class="span5 col-md5">
		<fieldset>
			<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_TEXT_COLOR_SETTINGS'); ?></legend>

			<div class="row-fluid">
				<div class="span6 col-md6">
					<div class="span12 col-md12">
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

					<div id="addColorContainer<?php echo $area->id ?>" class="span12 col-md12 addColorButton" <?php echo $allColorsHide; ?> >
						<button class="btn btn-mini btn-success" id="addColorButton<?php echo $area->id ?>" type="button">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ADD_COLOR'); ?>
						</button>
					</div>

					<?php $colorCodes = explode(',', $area->color_code); ?>
					<div id="selectedColorsPalette<?php echo $area->id ?>" class="span12 col-md12" <?php echo $allColorsHide; ?> >
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

				<div id="colorsContainer<?php echo $area->id ?>" class="span6 col-md6" <?php echo $allColorsHide; ?>>

					<div class="span9 col-md9">
						<label class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
							<div id="colorPickerContainer<?php echo $area->id; ?>" class="colorPickerContainer"></div>
						</label>

						<label for="colorPickerSelectedColor<?php echo $area->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?>
							<input class="span12 col-md12 colorPickerSelectedColor"
							       type="text"
							       value="#cfcfcf"
							       id="colorPickerSelectedColor<?php echo $area->id; ?>"
							       name="colorPickerSelectedColor<?php echo $area->id; ?>"
								/>
						</label>
					</div>

					<div class="span3 col-md3 CMYKContainer">
						<div class="input-prepend">
							<span class="add-on">C</span>
							<input class="span8 col-md8"
							       id="C<?php echo $area->id; ?>"
							       name="C<?php echo $area->id; ?>"
							       type="text"
							       value="10"
							       placeholder="C"
								>
						</div>
						<div class="input-prepend">
							<span class="add-on">M</span>
							<input class="span8 col-md8"
							       id="M<?php echo $area->id; ?>"
							       name="M<?php echo $area->id; ?>"
							       type="text"
							       value="10"
							       placeholder="M"
								>
						</div>
						<div class="input-prepend">
							<span class="add-on">Y</span>
							<input class="span8 col-md8"
							       id="Y<?php echo $area->id; ?>"
							       name="Y<?php echo $area->id; ?>"
							       type="text"
							       value="10"
							       placeholder="Y"
								>
						</div>
						<div class="input-prepend">
							<span class="add-on">K</span>
							<input class="span8 col-md8"
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

	<div class="span6 col-md6 offset5">
		<button id="saveAreaSettings<?php echo $area->id; ?>"
		        type="button"
		        class="btn btn-success"
		        data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>"
		        onclick="saveAreaSettings(<?php echo $area->id . ',\'' . $area->name . '\''; ?>);">
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
