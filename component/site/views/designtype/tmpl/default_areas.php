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
	$this->item = $displayData->item;
	$this->productionBackgroundAreas = $displayData->productionBackgroundAreas;
	$this->fonts = $displayData->fonts;
}

?>

{RedDesignBreakDesignAreasTitle}
<h4 class="page-header">
	<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?>
</h4>
{RedDesignBreakDesignAreasTitle}

<?php foreach ($this->productionBackgroundAreas as $area) : ?>

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

			if ($this->item->fontsizer == 'auto' || $this->item->fontsizer == 'auto_chars')
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
			<input id="textAlign<?php echo $area->id ?>" type="hidden" value="<?php echo $area->textalign; ?>" />

			<label for="textArea<?php echo $area->id; ?>">
				<strong><?php echo $area->name; ?></strong>
			</label>
		{RedDesignBreakDesignAreaTitle}

		{RedDesignBreakDesignAreaInputTextLabel}
			<label for="textArea<?php echo $area->id; ?>">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_INPUT_TEXT'); ?>
			</label>
		{RedDesignBreakDesignAreaInputTextLabel}

		{RedDesignBreakDesignAreaInputText}
			<?php if ($area->input_field_type == 1) : ?>
				<textarea name="textArea<?php echo $area->id; ?>"
						  class="textAreaClass"
						  style="text-align: <?php echo $textAlign; ?>;"
						  placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
						  id="textArea<?php echo $area->id; ?>"
						  required="required"
						  <?php echo $maxChar; ?>
						  <?php echo $maxLine ?>><?php echo $area->default_text; ?></textarea>
			<?php else : ?>
				<input type="text"
					   name="textArea<?php echo $area->id; ?>"
					   class="textAreaClass"
					   style="text-align: <?php echo $textAlign; ?>;"
					   placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
					   id="textArea<?php echo $area->id; ?>"
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

					echo JHTML::_('select.genericlist', $defaultFonts, 'fontArea' . $area->id, 'class="inputbox" onChange="customize(0);"', 'value', 'text', null);
				}
				else
				{
					$areaFontsIds 	= explode(',', $area->font_id);

					// If there is only one font allowed, don't show anything.
					if (count($areaFontsIds) > 1)
					{
						$options = array();

						foreach ($areaFontsIds as $key => $value)
						{
							foreach ($this->fonts as $font => $f)
							{
								if ($f->id == $value)
								{
									$options[] = JHTML::_('select.option', trim($f->name), $f->name);
									$fontFile = 'fonts/' . $f->name . '.js';
									RHelperAsset::load($fontFile, 'com_reddesign');
								}
							}
						}

						echo JHTML::_('select.genericlist', $options, 'fontArea' . $area->id, 'class="inputbox" onChange="svgLoad();"', 'value', 'text', null);
					}
				}
			?>
		{RedDesignBreakDesignAreaChooseFont}

		{RedDesignBreakDesignAreaChooseFontSizeLabel}
			<?php // Font Size Selection ?>
			<?php if ($this->item->fontsizer != 'auto' && $this->item->fontsizer != 'auto_chars') : ?>
				<label for="<?php echo 'fontSize' . $area->id; ?>">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONTSIZE'); ?>
				</label>
			<?php endif; ?>
		{RedDesignBreakDesignAreaChooseFontSizeLabel}

		{RedDesignBreakDesignAreaChooseFontSize}
			<?php if ($this->item->fontsizer === 'slider') : ?>
				<?php
					// Case 1: using slider selector for font size.
					RHelperAsset::load('bootstrap-slider.js', 'com_reddesign');
					RHelperAsset::load('slider.css', 'com_reddesign');
				?>
				<input type="hidden"
					   id="fontSize<?php echo $area->id ?>"
					   class="fontSizeSlider"
					   name="fontSize<?php echo $area->id ?>"
					   value="<?php echo $area->defaultFontSize ?>"
					   data-slider-min="<?php echo $area->minFontSize ?>"
					   data-slider-max="<?php echo $area->maxFontSize ?>"
					   data-slider-value="[<?php echo $area->defaultFontSize ?>]"
				/>
			<?php elseif ($this->item->fontsizer == 'dropdown_numbers' || $this->item->fontsizer == 'dropdown_labels') : ?>
				<?php

					// Case 2: using dropdown selector for font size.

					$areaFontSizes = explode(',', $area->font_size);

					// If only one font size allowed don't show anything.
					if (count($areaFontSizes) > 1)
					{
						$sizeOptions = array();

						foreach ($areaFontSizes as $areaFontSize)
						{
							if ($this->item->fontsizer == 'dropdown_numbers')
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

							$sizeOptions[] = JHTML::_('select.option', $optionValue, $optionText);
						}

						echo JHTML::_(
										'select.genericlist',
										$sizeOptions,
										'fontSize' . $area->id,
										'class="inputbox" onChange="customize(0);"',
										'value',
										'text',
										null
						);
					}
				?>
			<?php endif; ?>
		{RedDesignBreakDesignAreaChooseFontSize}

		{RedDesignBreakDesignAreaChooseColorLabel}
			<label>
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_COLOR_CODE'); ?>
			</label>
		{RedDesignBreakDesignAreaChooseColorLabel}

		{RedDesignBreakDesignAreaChooseColor}
			<?php if (empty($area->color_code)) : ?>

				<input type="hidden" name="colorCode<?php echo $area->id ?>" value="000000" id="colorCode<?php echo $area->id ?>">

			<?php elseif ($area->color_code == 1) : ?>

				<div id="colorsContainer<?php echo $area->id ?>" class="span6">
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