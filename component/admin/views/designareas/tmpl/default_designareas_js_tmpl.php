<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

?>

<script id="areaRowsMustache" type="text/html">
	<tr id="areaRow{{reddesignAreaId}}" class="{{rowClass}}">
		<td>
			{{reddesignAreaId}}
		</td>
		<td class="span4">
			<a href="#" onclick="selectAreaForEdit({{reddesignAreaId}},'{{title}}',{{x1}},{{y1}},{{x2}},{{y2}},{{width}},{{height}})">
				<strong>{{title}}</strong>
			</a>
		</td>
		<td>
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH') ?></strong>&nbsp;{{widthToUnit}}<?php echo $this->unit; ?>,
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT') ?></strong>&nbsp;{{heightToUnit}}<?php echo $this->unit; ?>,
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1') ?></strong>&nbsp;{{x1ToUnit}}<?php echo $this->unit; ?>,
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1') ?></strong>&nbsp;{{y1ToUnit}}<?php echo $this->unit; ?>,
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2') ?></strong>&nbsp;{{x2ToUnit}}<?php echo $this->unit; ?>,
			<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2') ?></strong>&nbsp;{{x2ToUnit}}<?php echo $this->unit; ?>
		</td>
		<td>
			<button type="button" class="btn btn-primary btn-mini" onclick="showAreaSettings({{reddesignAreaId}});">
				<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>
			</button>
		</td>
		<td>
			<button type="button" class="btn btn-danger btn-mini" onclick="removeArea({{reddesignAreaId}});">
				<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
			</button>
		</td>
	</tr>

	<tr id="areaSettingsRow{{reddesignAreaId}}" class="{{rowClass}} hide areaSettingsRow">
		<td colspan="5" >
			<div class="row-fluid">
				<div class="span4">
					<fieldset>
						<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_FONT_SETTINGS'); ?></legend>
						<div class="row-fluid">
							<div class="span6">
								<?php if ($this->item->fontsizer != 'auto' && $this->item->fontsizer != 'auto_chars') : ?>
									<div class="control-group">
										<label for="areaFontAlignment{{reddesignAreaId}}">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>
											<select id="areaFontAlignment{{reddesignAreaId}}" name="areaFontAlignment{{reddesignAreaId}}"></select>
										</label>
									</div>
								<?php endif; ?>
								<div class="control-group">
									<label for="areaFonts{{reddesignAreaId}}">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALLOWED_FONTS') ?>
										<select id="areaFonts{{reddesignAreaId}}" name="areaFonts{{reddesignAreaId}}[]" multiple="multiple"></select>
									</label>
								</div>
							</div>
							<div class="offset2 span4">
								<?php if ($this->item->fontsizer == 'dropdown_numbers' || $this->item->fontsizer == 'dropdown_labels') : ?>
									<div class="control-group">
										<label for="fontsizerDropdown{{reddesignAreaId}}">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>
											<textarea class="span12"
													  style="resize: none;"
													  id="fontsizerDropdown{{reddesignAreaId}}"
													  name="fontsizerDropdown{{reddesignAreaId}}"
													  rows="7"
													></textarea>
											<span class="help-block">
												<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC') ?>
											</span>
										</label>
									</div>
								<?php elseif ($this->item->fontsizer == 'slider') : ?>
									<div class="control-group">
										<label for="fontsizerSliderDefault{{reddesignAreaId}}">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_FONT_SIZE') ?>
												<input class="input-mini"
													   type="text"
													   value=""
													   maxlength="3"
													   id="fontsizerSliderDefault{{reddesignAreaId}}"
													   name="fontsizerSliderDefault{{reddesignAreaId}}"
												/>
										</label>
									</div>
									<div class="control-group">
										<label for="fontsizerSliderMin{{reddesignAreaId}}">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MIN_FONT_SIZE') ?>
											<input class="input-mini"
												   type="text"
												   value=""
												   maxlength="3"
												   id="fontsizerSliderMin{{reddesignAreaId}}"
												   name="fontsizerSliderMin{{reddesignAreaId}}"
											/>
										</label>
									</div>
									<div class="control-group">
										<label for="fontsizerSliderMax{{reddesignAreaId}}">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAX_FONT_SITE') ?>
											<input class="input-mini"
												   type="text"
												   value=""
												   maxlength="3"
												   id="fontsizerSliderMax{{reddesignAreaId}}"
												   name="fontsizerSliderMax{{reddesignAreaId}}"
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
									<select id="inputFieldType{{reddesignAreaId}}"
											name="inputFieldType{{reddesignAreaId}}[]"
											onclick="changeInputFieldType({{reddesignAreaId}});">
										<option value="0" selected="selected">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTBOX'); ?>
										</option>
										<option value="1">
											<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTAREA'); ?>
										</option>
									</select>
								</label>
							</div>
							<div class="control-group">
								<label for="defaultText{{reddesignAreaId}}">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_TEXT') ?>
									<div id="defaultTextContainer{{reddesignAreaId}}">
										<textarea class="input-medium" style="resize: none;" id="defaultText{{reddesignAreaId}}" name="defaultText{{reddesignAreaId}}"></textarea>
									</div>
								</label>
							</div>
							<div class="control-group">
								<label for="maximumCharsAllowed{{reddesignAreaId}}">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_CHARS') ?>
									<input class="input-mini"  type="text" value="" id="maximumCharsAllowed{{reddesignAreaId}}" name="maximumCharsAllowed{{reddesignAreaId}}"/>
								</label>
							</div>
							<div class="control-group" id="maximumLinesAllowedContainer{{reddesignAreaId}}" style="display: none;">
								<label for="maximumLinesAllowed{{reddesignAreaId}}">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_LINES') ?>
									<input class="input-mini" type="text" value="" id="maximumLinesAllowed{{reddesignAreaId}}" name="maximumLinesAllowed{{reddesignAreaId}}"/>
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
									<label class="checkbox inline" for="allColors{{reddesignAreaId}}">
										<input type="checkbox" id="allColors{{reddesignAreaId}}" name="allColors{{reddesignAreaId}}" value="allColors" checked="checked"/>
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_CHECK_ALL_COLORS'); ?>
									</label>
								</div>
								<div id="addColorContainer{{reddesignAreaId}}" class="span12 addColorButton" style="display: none;">
									<button class="btn btn-mini btn-success" id="addColorButton{{reddesignAreaId}}" type="button">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ADD_COLOR'); ?>
									</button>
								</div>
								<div id="selectedColorsPalette{{reddesignAreaId}}" class="span12" style="display: none;" >
								</div>
								<input type="hidden" id="colorCodes{{reddesignAreaId}}" name="colorCodes{{reddesignAreaId}}" value=""/>
							</div>
							<div id="colorsContainer{{reddesignAreaId}}" class="span6" style="display: none;">
								<div class="span9">
									<label class="control-label">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
										<div id="colorPickerContainer{{reddesignAreaId}}" class="colorPickerContainer"></div>
									</label>
									<label for="colorPickerSelectedColor{{reddesignAreaId}}">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?>
										<input class="span12 colorPickerSelectedColor"
											   type="text"
											   value="#cfcfcf"
											   id="colorPickerSelectedColor{{reddesignAreaId}}"
											   name="colorPickerSelectedColor{{reddesignAreaId}}"
										/>
									</label>
								</div>
								<div class="span3 CMYKContainer">
									<div class="input-prepend">
										<span class="add-on">C</span>
										<input class="span8" id="C{{reddesignAreaId}}" name="C{{reddesignAreaId}}" type="text" value="10" placeholder="C">
									</div>
									<div class="input-prepend">
										<span class="add-on">M</span>
										<input class="span8" id="M{{reddesignAreaId}}" name="M{{reddesignAreaId}}" type="text" value="10" placeholder="M">
									</div>
									<div class="input-prepend">
										<span class="add-on">Y</span>
										<input class="span8" id="Y{{reddesignAreaId}}" name="Y{{reddesignAreaId}}" type="text" value="10" placeholder="Y">
									</div>
									<div class="input-prepend">
										<span class="add-on">K</span>
										<input class="span8" id="K{{reddesignAreaId}}" name="K{{reddesignAreaId}}" type="text" value="10" placeholder="K">
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="span6 offset5">
					<button id="saveAreaSettings{{reddesignAreaId}}"
							type="button"
							class="btn btn-success"
							data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>"
							onclick="saveAreaSettings({{reddesignAreaId}});">
						<span>
							<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
						</span>
					</button>
					<button type="button"
							class="btn"
							onclick="showAreaSettings({{reddesignAreaId}});">
						<span>
							<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
						</span>
					</button>
				</div>
			</div>
		</td>
	</tr>
</script>