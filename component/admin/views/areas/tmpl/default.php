<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// Set variables for using them in HMVC. For regular MVC $displayData can not be used.
$this->item = $displayData->item;
$this->areas = $displayData->items;
$this->designtype_id =	$displayData->item->designtype_id;
$this->pxToUnit = $displayData->pxToUnit;
$this->unit = $displayData->unit;
$this->fontsOptions = $displayData->fontsOptions;
$this->inputFieldOptions = $displayData->inputFieldOptions;

?>


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
								/*
								 * @Todo: this is a terrible hotfix for fastfixing a not storing
								 * font_id  in database issue.But it needs to be moved to the
								 * areas model asap.
								 * It Converts the font_id JSON field to an array.
								 */
								$registry = new JRegistry;
								$registry->loadString($area->font_id);
								$selectedFonts = $registry->toArray();
								echo JHtml::_(
									'select.genericlist',
									$this->fontsOptions,
									'areaFonts' . $area->id . '[]',
									' multiple="multiple" ',
									'value',
									'text',
									$selectedFonts
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

<script type="application/javascript">
	/**
	 * Deletes an area.
	 *
	 * @param reddesign_area_id
	 */
	function removeArea(reddesign_area_id) {
		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxRemove",
			data: {
				id: reddesign_area_id
			},
			type: "post",
			success: function (data) {
				var jsonData = jQuery.parseJSON(data);

				if (jsonData.status == 1)
				{
					jQuery("#areaRow" + reddesign_area_id).remove();
					jQuery("#areaSettingsRow" + reddesign_area_id).remove();
					updateImageAreas();
				}
				else
				{
					alert(jsonData.message);
				}
			},
			error: function (data) {
				console.log('function removeArea() Error');
				console.log(data);
			}
		});
	}

	/**
	 * @Todo: this function should update the SVG with the existing areas
	 * is for example called when afterRemoving an area you need to refresh all areas (see removeArea)
	 */
	function updateImageAreas() {
		/*
		 var json;

		 jQuery("#backgroundImageContainer div").remove();

		 jQuery.ajax({
		 data: {
		 background_id: <?php echo $this->productionBackground->id; ?>
		 },
		 url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxGetAreas",
		 success: function (data) {
		 json = jQuery.parseJSON(data);
		 jQuery.each( json, function( key, value ) {
		 drawArea(value.reddesign_area_id, value.title, value.x1_pos, value.y1_pos, value.width, value.height)
		 });
		 },
		 error: function (data) {
		 console.log('UpdateImageAreas function Error');
		 console.log(data);
		 }
		 });
		 */
	}

	/**
	 * Shows a hidden area containing the editable parameters of an area
	 *
	 * @param reddesign_area_id
	 */
	function showAreaSettings(reddesign_area_id)
	{
		var areaSetting = jQuery("#areaSettingsRow" + reddesign_area_id);

		if (areaSetting.css('display') == 'none')
		{
			jQuery(".areaSettingsRow").hide();
			areaSetting.show("slow")
		}
		else
		{
			areaSetting.hide("slow");
		}
	}

	/**
	 * Saves settings for an area
	 *
	 * @param reddesign_area_id
	 */
	function saveAreaSettings(reddesign_area_id, areaName) {
		jQuery("#saveAreaSettings" + reddesign_area_id).button("loading");

		var areaFontAlignment = jQuery("#areaFontAlignment" + reddesign_area_id).val();
		var fontsizerDropdown = jQuery("#fontsizerDropdown" + reddesign_area_id).val();
		var fontsizerSliderDefault = jQuery("#fontsizerSliderDefault" + reddesign_area_id).val();
		var fontsizerSliderMin = jQuery("#fontsizerSliderMin" + reddesign_area_id).val();
		var fontsizerSliderMax = jQuery("#fontsizerSliderMax" + reddesign_area_id).val();
		var inputFieldType = jQuery("#inputFieldType" + reddesign_area_id).val();
		var maximumCharsAllowed = jQuery("#maximumCharsAllowed" + reddesign_area_id).val();
		var maximumLinesAllowed = jQuery("#maximumLinesAllowed" + reddesign_area_id).val();
		var areaFonts = jQuery("#areaFonts" + reddesign_area_id).val();
		var colorCodes = jQuery("#colorCodes" + reddesign_area_id).val();
		var defaultText = jQuery("#defaultText" + reddesign_area_id).val();

		if (jQuery("#allColors" + reddesign_area_id).is(":checked"))
		{
			colorCodes = 1;
		}

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxSave",
			data: {
				'jform[id]': reddesign_area_id,
				'jform[name]': areaName,
				'jform[textalign]': areaFontAlignment,
				'jform[font_id]': areaFonts,
				'jform[font_size]': fontsizerDropdown,
				'jform[defaultFontSize]': fontsizerSliderDefault,
				'jform[minFontSize]': fontsizerSliderMin,
				'jform[maxFontSize]': fontsizerSliderMax,
				'jform[input_field_type]': inputFieldType,
				'jform[maxchar]': maximumCharsAllowed,
				'jform[maxline]': maximumLinesAllowed,
				'jform[color_code]': colorCodes,
				'jform[default_text]': defaultText
			},
			type: "post",
			success: function (data) {
				setTimeout(function () {jQuery("#saveAreaSettings" + reddesign_area_id).button("reset")}, 500);
			},
			error: function (data) {
				console.log('function saveAreaSettings() Error');
				console.log(data);
			}
		});
	}

</script>
