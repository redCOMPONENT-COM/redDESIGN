<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/colorpicker.js');
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/selectionboxmove.js');
FOFTemplateUtils::addCSS('media://com_reddesign/assets/css/colorpicker.css');
?>

<h4 class="page-header">
	<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?>
</h4>
<?php foreach ($this->productionBackgroundAreas as $area) : ?>

	<input id="textAlign<?php echo $area->reddesign_area_id; ?>" type="hidden" value="<?php echo $area->textalign; ?>" />

	<div class="row-fluid">
		<div class="span4">
			<label for="textArea<?php echo $area->reddesign_area_id; ?>">
				<strong><?php echo $area->title; ?></strong>
			</label>

			<?php if ($area->input_field_type == 1) : ?>
				<textarea
					name="textArea<?php echo $area->reddesign_area_id; ?>"
					class="textAreaClass"
					placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
					id="textArea<?php echo $area->reddesign_area_id; ?>"
					required="required"
					maxlength="<?php echo $area->maxchar; ?>"
					rows="<?php echo $area->maxline; ?>"></textarea>
			<?php else : ?>
				<input
					type="text"
					name="textArea<?php echo $area->reddesign_area_id; ?>"
					class="textAreaClass"
					placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
					id="textArea<?php echo $area->reddesign_area_id; ?>"
					value=""
					maxlength="<?php echo $area->maxchar; ?>"
					required="required"
					>
			<?php endif; ?>
		</div>
		<div class="span4">
			<label for="<?php echo 'fontArea' . $area->reddesign_area_id; ?>">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
			</label>

			<?php
			/* FONT SELECTION */

			// If no font is selected Arial will be used
			if (empty($area->font_id))
			{
				$defaultFonts = array();
				$defaultFonts[] = JHTML::_('select.option', 0, 'Arial');
				echo JHTML::_('select.genericlist', $defaultFonts, 'fontArea' . $area->reddesign_area_id, 'class="inputbox" onChange="customize(0);"', 'value', 'text', null);
			}
			else
			{
				$areaFontsIds 	= explode(',', $area->font_id);
				$options 		= array();

				foreach ($areaFontsIds as $key => $value) :
					$options[] = JHTML::_('select.option', $value, $this->fonts[$value]->title);
				endforeach;

				echo JHTML::_('select.genericlist', $options, 'fontArea' . $area->reddesign_area_id, 'class="inputbox" onChange="customize(0);"', 'value', 'text', null);
			}
			?>

			<?php /* FONT SIZE SELECTION */ ?>

			<?php /* Case 1: automatic font size */ ?>
			<?php if ($this->item->fontsizer != 'auto') : ?>
				<label for="<?php echo 'fontSize' . $area->reddesign_area_id; ?>">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONTSIZE'); ?>
				</label>
			<?php endif; ?>

			<?php /* Case 2: using slider selector for font size */ ?>
			<?php if ($this->item->fontsizer === 'slider') : ?>
				<?php FOFTemplateUtils::addJS('media://com_reddesign/assets/js/bootstrap-slider.js'); ?>
				<?php FOFTemplateUtils::addCSS('media://com_reddesign/assets/css/slider.css'); ?>
				<input type="hidden"
					   id="fontSize<?php echo $area->reddesign_area_id ?>"
					   name="fontSize<?php echo $area->reddesign_area_id ?>"
					   class="span2"
					   value="<?php echo $area->defaultFontSize ?>"
					   data-slider-min="<?php echo $area->minFontSize ?>"
					   data-slider-max="<?php echo $area->maxFontSize ?>"
					   data-slider-value="[<?php echo $area->defaultFontSize ?>]"

					/>

				<?php /* Case 3: using dropdown selector for font size */ ?>
			<?php elseif ($this->item->fontsizer === 'dropdown') : ?>
				<?php
				$areaFontSizes = explode(',', $area->font_size);
				$sizeOptions       = array();

				foreach ($areaFontSizes as $key => $value) :
					$sizeOptions[] = JHTML::_('select.option', $value, $value);
				endforeach;

				echo JHTML::_('select.genericlist', $sizeOptions, 'fontSize' . $area->reddesign_area_id, 'class="inputbox" onChange="customize(0);"', 'value', 'text', null);
				?>
			<?php endif; ?>
		</div>
		<div class="span4">
			<label>
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_COLOR_CODE'); ?>
			</label>

			<?php if (empty($area->color_code)) : ?>
				<input type="hidden" name="colorCode<?php echo $area->reddesign_area_id ?>" value="000000"
					   id="colorCode<?php echo $area->reddesign_area_id ?>">
			<?php elseif ($area->color_code == 1) : ?>
				<div id="color-selector<?php echo $area->reddesign_area_id; ?>" class="colorSelector">
					<div style="background-color: #000000"></div>
				</div>
				<div class="help-block">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLORS_HELP'); ?>
				</div>
				<input type="hidden" name="colorCode<?php echo $area->reddesign_area_id ?>" value="000000"
					   id="colorCode<?php echo $area->reddesign_area_id ?>">
			<?php else : ?>
				<div id="loadColors" class="row-fluid">
					<?php
						if (strpos($area->color_code, "#") !== false)
						{
							$colors = explode(",", $area->color_code);

							$defaultColor = $colors[0];
							$defaultColorVal = str_replace('#', '', $colors[0]);

							foreach ($colors as $key => $value)
							{
								$colorCodeVal = str_replace('#', '', $colors[$key]);
					?>
								<div class="colorSelector_list">
									<div onClick="setColorCode(<?php echo $area->reddesign_area_id; ?>,'<?php echo $colorCodeVal; ?>');"
										 style="background-color:<?php echo $value; ?>;cursor:pointer;">
										 &nbsp;
									</div>
								</div>
					<?php
							}
						}
					?>
				</div>

				<div class="span12">
					<label for="fontColor<?php echo $area->reddesign_area_id ?>">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DEFAULT_COLOR_CODE'); ?>
					</label>
					<div class="colorSelector_list" id="fontColor<?php echo $area->reddesign_area_id ?>">
						<div style="background-color:<?php echo $defaultColor; ?>;cursor:pointer;">&nbsp;</div>
					</div>
					<input type="hidden" class="colorCode<?php echo $area->reddesign_area_id ?>"
							name="colorCode<?php echo $area->reddesign_area_id ?>"
							value="<?php echo $defaultColorVal; ?>"
							id="colorCode<?php echo $area->reddesign_area_id ?>">
				</div>

			<?php endif; ?>

		</div>
	</div>
	<hr class="bs-docs-separator">
<?php endforeach; ?>