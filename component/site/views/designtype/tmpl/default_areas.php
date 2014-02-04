<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

RHelperAsset::load('selectionboxmove.js', 'com_reddesign');

// Colorpicker includes.
RHelperAsset::load('farbtastic.min.js', 'com_reddesign');
RHelperAsset::load('farbtastic.css', 'com_reddesign');
RHelperAsset::load('color-converter.js', 'com_reddesign');

if (isset($displayData))
{
	$this->designType = $displayData->designType;
	$this->displayedAreas = $displayData->displayedAreas;
	$this->fonts = $displayData->fonts;
}

$config = ReddesignEntityConfig::getInstance();
$unit = $config->getUnit();
$sourceDpi = $config->getSourceDpi();
$unitConversionRatio = ReddesignHelpersSvg::getUnitConversionRatio($unit, $sourceDpi);
?>

{RedDesignBreakDesignAreasTitle}
<h4 class="page-header">
	<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?>
</h4>
{RedDesignBreakDesignAreasTitle}

<?php foreach ($this->displayedAreas as $area) : ?>

	<?php echo '{RedDesignBreakDesignArea' . $area->id . '}'; ?>

		<?php
			switch ($area->textalign)
			{
				case 1:
					$textAlign = 'left';
					break;
				case 2:
					$textAlign = 'right';
					break;
				case 0:
				case 3:
				default:
					$textAlign = 'center';
					break;
			}

			if ($this->designType->fontsizer == 'auto' || $this->designType->fontsizer == 'auto_chars')
			{
				$textAlign = 'center';
			}

			if (!empty($area->maxchar) && $area->maxchar != 0)
			{
				$maxChar = 'maxlength="' . $area->maxchar . '"';
			}
			else
			{
				$maxChar = '';
			}

			if (!empty($area->maxline) && $area->maxline != 0)
			{
				$maxLine = 'rows="' . $area->maxline . '"';
			}
			else
			{
				$maxLine = '';
			}
		?>

		{RedDesignBreakDesignAreaTitle}
			<label for="textArea_<?php echo $area->id; ?>">
				<strong><?php echo $area->name; ?></strong>
			</label>
		{RedDesignBreakDesignAreaTitle}

		{RedDesignBreakDesignAreaInputTextLabel}
			<label for="textArea_<?php echo $area->id; ?>">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_INPUT_TEXT'); ?>
			</label>
		{RedDesignBreakDesignAreaInputTextLabel}

		{RedDesignBreakDesignAreaInputText}
			<?php if ($area->input_field_type == 1) : ?>
				<textarea name="textArea_<?php echo $area->id; ?>"
						  class="textAreaClass"
						  style="text-align: <?php echo $textAlign; ?>;"
						  placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
						  id="textArea_<?php echo $area->id; ?>"
						  required="required"
						  <?php echo $maxChar; ?>
						  <?php echo $maxLine ?>><?php echo $area->default_text; ?></textarea>
			<?php else : ?>
				<input type="text"
					   name="textArea_<?php echo $area->id; ?>"
					   class="textAreaClass"
					   style="text-align: <?php echo $textAlign; ?>;"
					   placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
					   id="textArea_<?php echo $area->id; ?>"
					   value="<?php echo $area->default_text; ?>"
					   <?php echo $maxChar; ?>
					   required="required"
				/>
			<?php endif; ?>
		{RedDesignBreakDesignAreaInputText}

		{RedDesignBreakDesignAreaChooseFontLabel}
			<label for="<?php echo 'fontArea' . $area->id; ?>">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
			</label>
		{RedDesignBreakDesignAreaChooseFontLabel}

		{RedDesignBreakDesignAreaChooseFont}
			<?php
				// Font Selection

				// If no font is selected, Arial will be used.
				if (empty($area->font_id))
				{
					$defaultFonts = array();
					$defaultFonts[] = JHTML::_('select.option', 0, 'Arial');

					echo JHTML::_('select.genericlist', $defaultFonts, 'fontArea' . $area->id, 'class="inputbox reddesign-font-selection" onChange="customize(0);"', 'value', 'text', null);
				}
				else
				{
					$areaFontsIds = ReddesignHelpersFont::getSelectedFontsFromArea(array($area));

					if (count($areaFontsIds) > 0)
					{
						$firstFontSelected = null;
						$options = array();

						foreach ($areaFontsIds as $key => $value)
						{
							foreach ($this->fonts as $font => $f)
							{
								if ($f->id == $value)
								{
									$options[] = JHTML::_('select.option', $f->id, $f->name);

									if (empty($firstFontSelected))
									{
										$firstFontSelected = $f->id;
									}
								}
							}
						}

						echo JHTML::_('select.genericlist',
							$options,
							'fontArea' . $area->id,
							'class="inputbox reddesign-font-selection"',
							'value',
							'text',
							$firstFontSelected
						);
					}
				}
			?>
		{RedDesignBreakDesignAreaChooseFont}

		{RedDesignBreakDesignAreaChooseFontSizeLabel}
			<?php // Font Size Selection ?>
			<?php if (($this->designType->fontsizer != 'auto' && $this->designType->fontsizer != 'auto_chars') && ($area->font_size != '' || $this->designType->fontsizer == 'slider')) : ?>
				<label for="<?php echo 'fontSize' . $area->id; ?>">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONTSIZE'); ?>
				</label>
			<?php endif; ?>
		{RedDesignBreakDesignAreaChooseFontSizeLabel}

		{RedDesignBreakDesignAreaChooseFontSize}
			<?php
				$defaultFontSizeOutput = false;

				if ($this->designType->fontsizer === 'auto' || $this->designType->fontsizer === 'auto_chars'):
					$area->defaultFontSize = round($area->height * $unitConversionRatio, 0);
				endif;
			?>
			<?php if ($this->designType->fontsizer === 'slider') : ?>
				<?php
					// Case 1: using slider selector for font size.
					RHelperAsset::load('bootstrap-slider.js', 'com_reddesign');
					RHelperAsset::load('slider.css', 'com_reddesign');
				?>
				<input type="hidden"
					   id="fontSizeSlider<?php echo $area->id ?>"
					   class="fontSizeSlider reddesign-font-size-selection"
					   name="fontSizeSlider<?php echo $area->id ?>"
					   value="<?php echo $area->defaultFontSize ?>"
					   data-slider-min="<?php echo $area->minFontSize ?>"
					   data-slider-max="<?php echo $area->maxFontSize ?>"
					   data-slider-value="[<?php echo $area->defaultFontSize ?>]"
				/>
			<?php $defaultFontSizeOutput = true; ?>
			<?php elseif ($this->designType->fontsizer == 'dropdown_numbers' || $this->designType->fontsizer == 'dropdown_labels') : ?>
				<?php

					// Case 2: using dropdown selector for font size.

					$areaFontSizes = explode(',', $area->font_size);

					// If only one font size allowed don't show anything.
					if (count($areaFontSizes) > 1)
					{
						$firstFontSizeSelected = $areaFontSizes[0];
						sort($areaFontSizes);
						$sizeOptions = array();
						$firstElement = null;

						foreach ($areaFontSizes as $areaFontSize)
						{
							if ($this->designType->fontsizer == 'dropdown_numbers')
							{
								$optionValue = $areaFontSize;
								$optionText = $areaFontSize;
							}
							else
							{
								$labels = explode(':', $areaFontSize);

								if (!empty($labels[1]))
								{
									$optionValue = $labels[1];
								}
								else
								{
									$optionValue = $labels[0];
								}

								$optionText = $labels[0];
							}

							if ($areaFontSize == $firstFontSizeSelected)
							{
								$firstElement = $optionValue;
							}

							$sizeOptions[] = JHTML::_('select.option', $optionValue, $optionText);
						}

						echo JHTML::_(
							'select.genericlist',
							$sizeOptions,
							'fontSize' . $area->id,
							'class="inputbox reddesign-font-size-selection"',
							'value',
							'text',
							$firstElement
						);
					}
					else
					{
						$defaultFontSizeOutput = true;
					}
				?>
			<?php else : ?>
				<?php $defaultFontSizeOutput = true; ?>
			<?php endif; ?>

			<?php if ($defaultFontSizeOutput): ?>
				<input type="hidden"
				       id="fontSize<?php echo $area->id ?>"
				       class="reddesign-font-size-selection"
				       name="fontSize<?php echo $area->id ?>"
				       value="<?php echo (isset($area->defaultFontSize) && $area->defaultFontSize > 0) ? $area->defaultFontSize : 12; ?>"
					/>
			<?php endif; ?>

		<input id="textAlign<?php echo $area->id ?>" type="hidden" value="<?php echo $textAlign; ?>" />
		<input id="verticalAlign<?php echo $area->id ?>" type="hidden" value="<?php echo $area->verticalAlign; ?>" />

		<div class="btn-group btn-group-textAlign">
			<button class="btn" type="button" name="textAlignButton<?php echo $area->id ?>" value="left">
				<i class="icon-align-left"></i>&nbsp;
			</button>
			<button class="btn" type="button" name="textAlignButton<?php echo $area->id ?>" value="center">
				<i class="icon-align-center"></i>&nbsp;
			</button>
			<button class="btn" type="button" name="textAlignButton<?php echo $area->id ?>" value="right">
				<i class="icon-align-right"></i>&nbsp;
			</button>
		</div>

		<div class="btn-group btn-group-textVerticalAlign">
			<button class="btn" type="button" name="textVerticalAlignButton<?php echo $area->id ?>" value="top">
				<i class="icon-collapse-top"></i>&nbsp;
			</button>
			<button class="btn" type="button" name="textVerticalAlignButton<?php echo $area->id ?>" value="middle">
				<i class="icon-expand"></i>&nbsp;
			</button>
			<button class="btn" type="button" name="textVerticalAlignButton<?php echo $area->id ?>" value="bottom">
				<i class="icon-collapse"></i>&nbsp;
			</button>
		</div>
		{RedDesignBreakDesignAreaChooseFontSize}

		{RedDesignBreakDesignAreaChooseColorLabel}
			<?php if (!empty($area->color_code)) : ?>
				<label>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_COLOR_CODE'); ?>
				</label>
			<?php endif; ?>
		{RedDesignBreakDesignAreaChooseColorLabel}

		{RedDesignBreakDesignAreaChooseColor}
			<?php if (empty($area->color_code)) : ?>

				<input type="hidden" name="colorCode<?php echo $area->id ?>" value="#000000" id="colorCode<?php echo $area->id ?>">

			<?php elseif ($area->color_code == 1) : ?>

		<script type="text/javascript">
			jQuery(document).ready(
				function ($) {
					// Check div before add farbtastic
					if (jQuery("#colorPickerContainer<?php echo $area->id ?>")[0])
					{
						var colorPicker<?php echo $area->id ?> = jQuery.farbtastic("#colorPickerContainer<?php echo $area->id ?>");
						colorPicker<?php echo $area->id ?>.linkTo("#colorCode<?php echo $area->id; ?>");
					}

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

					jQuery(document).on("keyup", "#colorCode<?php echo $area->id; ?>", function() {
						var hex = jQuery("#colorCode<?php echo $area->id; ?>").val();
						loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
					});

					jQuery(document).on("mouseup", "#colorPickerContainer<?php echo $area->id; ?>", function() {
						var hex = jQuery("#colorCode<?php echo $area->id; ?>").val();
						loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
					});

					jQuery("#allColors<?php echo $area->id; ?>").click(function () {
						jQuery("#colorsContainer<?php echo $area->id; ?>").toggle(!this.checked);
						jQuery("#addColorContainer<?php echo $area->id; ?>").toggle(!this.checked);
						jQuery("#selectedColorsPalette<?php echo $area->id; ?>").toggle(!this.checked);
					});
				});
		</script>

				<div id="colorsContainer<?php echo $area->id ?>" class="span6">
					<div class="span9">
						<label class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
							<div id="colorPickerContainer<?php echo $area->id; ?>" class="colorPickerContainer"></div>
						</label>
						<label for="colorCode<?php echo $area->id; ?>">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?>
							<input class="span12 colorPickerSelectedColor colorCode"
								   type="text"
								   value="#cfcfcf"
								   id="colorCode<?php echo $area->id ?>"
								   name="colorCode<?php echo $area->id; ?>"
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

			<?php else : ?>

				<div id="loadColors" class="row-fluid">
					<?php
						$defaultColor = '#ff0000';
						$defaultColorVal = 'ff0000';

						if (strpos($area->color_code, '#') !== false)
						{
							$colors = explode(',', $area->color_code);
							$defaultColor = $colors[0];
							$defaultColorVal = str_replace('#', '', $colors[0]);

							foreach ($colors as $key => $value)
							{
								$colorCodeVal = str_replace('#', '', $colors[$key]);
					?>
								<div class="colorSelector_list">
									<div onClick="setColorCode(<?php echo $area->id ?>,'<?php echo $colorCodeVal; ?>');"
										 style="background-color:<?php echo $value; ?>;cursor:pointer;">
										 &nbsp;
									</div>
								</div>
					<?php
							}
						}
					?>
				</div>

				<div class="choosenColor">
					<label for="fontColor<?php echo $area->id ?>">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DEFAULT_COLOR_CODE'); ?>
					</label>
					<div class="colorSelector_list" id="fontColor<?php echo $area->id ?>">
						<div style="background-color:<?php echo $defaultColor; ?>;cursor:pointer;">&nbsp;</div>
					</div>
					<input type="hidden" class="colorCode<?php echo $area->id ?>" name="colorCode<?php echo $area->id ?>" value="<?php echo $defaultColorVal; ?>" id="colorCode<?php echo $area->id ?>">
				</div>

			<?php endif; ?>
		{RedDesignBreakDesignAreaChooseColor}

	<?php echo '{RedDesignBreakDesignArea' . $area->id . '}'; ?>

<?php endforeach; ?>