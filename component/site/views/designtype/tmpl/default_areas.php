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
JHtml::_('rjquery.flexslider', '.flexslider', array(
	'slideshow' => false,
	'directionNav' => true,
	'minItems' => 4,
	'maxItems' => 4,
	'itemWidth' => 95,
	'prevText' => '',
	'nextText' => '',
	'animation' => 'slide',
	'animationLoop' => false)
);

RHelperAsset::load('lib/jquery-fileupload/jquery.fileupload.css', 'com_reddesign');

// The jQuery UI widget factory, can be omitted if jQuery UI is already included
RHelperAsset::load('lib/jquery-fileupload/vendor/jquery.ui.widget.js', 'com_reddesign');

// The Iframe Transport is required for browsers without support for XHR file uploads
RHelperAsset::load('lib/jquery-fileupload/jquery.iframe-transport.js', 'com_reddesign');

// The basic File Upload plugin
RHelperAsset::load('lib/jquery-fileupload/jquery.fileupload.js', 'com_reddesign');

// The File Upload processing plugin
RHelperAsset::load('lib/jquery-fileupload/jquery.fileupload-process.js', 'com_reddesign');

if (isset($displayData))
{
	$this->designType = $displayData->designType;
	$this->displayedAreas = $displayData->displayedAreas;
	$this->fonts = $displayData->fonts;
}

$config = ReddesignEntityConfig::getInstance();
$clipartPreviewWidth = $config->getMaxClipartPreviewWidth();
$clipartPreviewHeight = $config->getMaxClipartPreviewHeight();
?>

{RedDesignBreakDesignAreasTitle}
<h4 class="page-header">
	<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE'); ?>
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

		if ($this->designType->fontsizer == 'auto')
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

	<?php
	switch ($area->areaType) :

		case 1: // Text
	?>
			{RedDesignBreakDesignAreaInputTextLabel}
				<label for="textArea_<?php echo $area->id; ?>">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_INPUT_TEXT'); ?>
				</label>
			{RedDesignBreakDesignAreaInputTextLabel}

			{RedDesignBreakDesignAreaInputText}
				<?php if ($area->input_field_type == 1) : ?>
					<textarea id="textArea_<?php echo $area->id; ?>"
							  name="textArea<?php echo $area->name; ?>"
							  class="textAreaClass"
							  style="text-align: <?php echo $textAlign; ?>;"
							  placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
							  required="required"
							  <?php echo $maxChar; ?>
							  <?php echo $maxLine ?>><?php echo $area->default_text; ?></textarea>
				<?php else : ?>
					<input id="textArea_<?php echo $area->id; ?>"
						   name="textArea<?php echo $area->name; ?>"
						   class="textAreaClass"
						   type="text"
						   style="text-align: <?php echo $textAlign; ?>;"
						   placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
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

						echo JHtml::_(
							'select.genericlist',
							$defaultFonts,
							'fontArea' . $area->name,
							'class="inputbox reddesign-font-selection" onChange="customize(0);"',
							'value',
							'text',
							null,
							'fontArea' . $area->id
						);
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

							echo JHtml::_(
								'select.genericlist',
								$options,
								'fontArea' . $area->name,
								'class="inputbox reddesign-font-selection"',
								'value',
								'text',
								$firstFontSelected,
								'fontArea' . $area->id
							);
						}
					}
				?>
			{RedDesignBreakDesignAreaChooseFont}

			{RedDesignBreakDesignAreaChooseFontSizeLabel}
				<?php // Font Size Selection ?>
				<?php if ($this->designType->fontsizer == 'dropdown_numbers' && $this->designType->fontsizer == 'dropdown_labels') : ?>
					<label for="<?php echo 'fontSize' . $area->id; ?>">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONTSIZE'); ?>
					</label>
				<?php endif; ?>
			{RedDesignBreakDesignAreaChooseFontSizeLabel}

			{RedDesignBreakDesignAreaChooseFontSize}
				<?php
					$defaultFontSizeOutput = false;

					if ($this->designType->fontsizer === 'auto')
					{
						$area->defaultFontSize = 1;
					}
				?>
				<?php if ($this->designType->fontsizer === 'slider') : ?>
					<?php
						// Case 1: using slider selector for font size.
						RHelperAsset::load('bootstrap-slider.js', 'com_reddesign');
						RHelperAsset::load('slider.css', 'com_reddesign');

						if (empty($area->defaultFontSize) || $area->defaultFontSize == 0)
						{
							$area->defaultFontSize = 15;
						}

						if (empty($area->minFontSize) || $area->minFontSize == 0)
						{
							$area->minFontSize = 5;
						}

						if (empty($area->maxFontSize) || $area->maxFontSize == 0)
						{
							$area->maxFontSize = 555;
						}
					?>
					<input id="fontSizeSlider<?php echo $area->id; ?>"
						   class="fontSizeSlider reddesign-font-size-selection"
						   name="fontSizeSlider<?php echo $area->name; ?>"
						   value="<?php echo $area->defaultFontSize; ?>"
						   type="hidden"
						   data-slider-min="<?php echo $area->minFontSize; ?>"
						   data-slider-max="<?php echo $area->maxFontSize; ?>"
						   data-slider-value="[<?php echo $area->defaultFontSize; ?>]"
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
								'fontSize' . $area->name,
								'class="inputbox reddesign-font-size-selection"',
								'value',
								'text',
								$firstElement,
								'fontSize' . $area->id
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

				<?php if ($defaultFontSizeOutput) : ?>
					<input id="fontSize<?php echo $area->id; ?>"
					       name="fontSize<?php echo $area->name; ?>"
					       class="reddesign-font-size-selection"
					       type="hidden"
					       value="<?php echo (isset($area->defaultFontSize) && $area->defaultFontSize > 0) ? $area->defaultFontSize : 12; ?>"
						/>
				<?php endif; ?>

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
					<input id="colorCode<?php echo $area->id; ?>"
					       name="colorCode<?php echo $area->name; ?>"
					       class="color-code"
					       type="hidden"
					       value="#000000"
						>
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

								jQuery(".color-wheel").hide();
								jQuery(".cmyk-inputs").hide();

								jQuery(".colorPickerSelectedColor").click(function(){
									jQuery(".color-wheel").hide();
									jQuery(".cmyk-inputs").hide();

									if(jQuery(this).parent().parent().parent().parent().parent().parent().parent().hasClass("wheel-clicked")) {
										jQuery(this).parent().parent().parent().parent().find(".color-wheel").hide();
										jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").hide();
										jQuery(this).parent().parent().parent().parent().parent().parent().parent().removeClass("wheel-clicked")
									}
									else {
										jQuery("#areasContainer li").removeClass("wheel-clicked")
										jQuery(this).parent().parent().parent().parent().find(".color-wheel").show();
										jQuery(this).parent().parent().parent().parent().find(".cmyk-inputs").show();
										jQuery(this).parent().parent().parent().parent().parent().parent().parent().addClass("wheel-clicked");
									}
								});
							});
					</script>

					<div id="colorsContainer<?php echo $area->id ?>" class="col-md6 col-md-12">
						<div class="row">
							<div class="col-md-6 CMYKContainer">
								<div class="row">
									<label class="active-color" for="colorCode<?php echo $area->id; ?>">
										<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SELECTED_COLOR') ?><img src="/images/colorwheel.png" class="colorwheel" />
										<input id="colorCode<?php echo $area->id ?>"
										       name="colorCode<?php echo $area->name; ?>"
										       class="span12 col-md12 colorPickerSelectedColor colorCode color-code"
										       type="text"
										       value="#cfcfcf"
										/>
									</label>
									<div class="cmyk-inputs">
										<div class="input-prepend col-md-6">
											<span class="add-on">C</span>
											<input class="span8"
												   id="C<?php echo $area->id; ?>"
												   name="C<?php echo $area->id; ?>"
												   type="text"
												   value="10"
												   placeholder="C"
												>
										</div>
										<div class="input-prepend col-md-6">
											<span class="add-on">M</span>
											<input class="span8"
												   id="M<?php echo $area->id; ?>"
												   name="M<?php echo $area->id; ?>"
												   type="text"
												   value="10"
												   placeholder="M"
												>
										</div>
										<div class="input-prepend col-md-6">
											<span class="add-on">Y</span>
											<input class="span8"
												   id="Y<?php echo $area->id; ?>"
												   name="Y<?php echo $area->id; ?>"
												   type="text"
												   value="10"
												   placeholder="Y"
												>
										</div>
										<div class="input-prepend col-md-6">
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
							<div class="col-md-6 color-wheel">
								<label class="control-label">
									<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_PICKER'); ?>
									<div id="colorPickerContainer<?php echo $area->id; ?>" class="colorPickerContainer"></div>
								</label>
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
						<input id="colorCode<?php echo $area->id; ?>"
						       name="colorCode<?php echo $area->name; ?>"
						       class="colorCode<?php echo $area->id; ?> color-code"
						       type="hidden"
						       value="<?php echo $defaultColorVal; ?>"
							>
					</div>

				<?php endif; ?>
			{RedDesignBreakDesignAreaChooseColor}
			<?php
				break;
			?>

		<?php case 2: // Clipart ?>
			{RedDesignBreakDesignAreaChooseClipartLabel}
				<label>
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_CLIPART'); ?>
				</label>
			{RedDesignBreakDesignAreaChooseClipartLabel}

			{RedDesignBreakDesignAreaChooseClipart}
				<button id="featuredClipartsButton<?php echo $area->id; ?>"
				        type="button"
				        class="btn btn-success featured-cliparts">
						<span>
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_FEATURED'); ?>
						</span>
				</button>
				<button id="clipartBankButton<?php echo $area->id; ?>"
				        type="button"
				        class="btn load-clipart-bank">
					<span>
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_BANK'); ?>
					</span>
				</button>
				<button id="clipartUploadButton<?php echo $area->id; ?>"
	                 type="button"
	                 class="btn upload-clipart-button">
					<span>
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD'); ?>
					</span>
				</button>
				<div class="flexslider" id="featuredCliparts<?php echo $area->id; ?>">
					<ul class="slides">
						<?php
							$area->cliparts = ReddesignHelpersArea::getAreaFeaturedCliparts($area->id);
						?>
						<?php foreach ($area->cliparts as $clipart) : ?>
							<li>
								<div class="pull-left thumbnail clipart-container" stle="pointer-events: none;">
									<div class="thumbnailSVG-pointer"
										 name="clipart<?php echo $area->id; ?>"
										 style="width:<?php echo $clipartPreviewWidth ?>px; height:<?php echo $clipartPreviewHeight; ?>px;">
									</div>
									<object
										id="clipart<?php echo $area->id ?>_<?php echo $clipart->id;?>"
										name="clipart<?php echo $area->id ?>_<?php echo $clipart->id;?>"
										class="thumbnailSVG"
										data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/' . $clipart->clipartFile; ?>"
										type="image/svg+xml">
									</object>
									<input
										type="hidden"
										class="change-selected-clipart"
										name="selectedClipart<?php echo $area->id ?>"
										value="<?php echo $clipart->id; ?>"
										/>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<input type="hidden" id="selectedClipart<?php echo $area->id; ?>" value="">
				<div id="clipartBank<?php echo $area->id; ?>" style="display:none;">
				</div>
				<div id="clipartUpload<?php echo $area->id; ?>" style="display:none;">
					<div class="upload-clipart-info">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD_INFO'); ?>
					</div>

					<span class="btn btn-success fileinput-button">
					    <i class="glyphicon glyphicon-plus"></i>
					    <span><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_SELECT_FILE'); ?></span>
					    <input accept="image/*" id="uploadClipartFile<?php echo $area->id ?>" type="file" name="uploadClipartFile<?php echo $area->id; ?>" />
					</span>
					<button class="btn btn-success upload-clipart-file" type="button" id="uploadClipartFileSave<?php echo $area->id; ?>">
						<i class="icon-upload"></i> <?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD'); ?>
					</button>
					<br />
					<br />
					<div class="progress progress-striped image-progress">
						<div class="bar bar-success" style="width: 0"></div>
					</div>

					<div id="uploadedClipart<?php echo $area->id; ?>" class="files">

					</div>

				</div>
				<div class="clearfix"></div>
			{RedDesignBreakDesignAreaChooseClipart}
		<?php
			break;
		?>
	<?php endswitch; ?>

	{RedDesignBreakDesignAreaChooseHorizontalAlign}
	<?php if ($this->designType->fontsizer != 'auto') : ?>
		<?php
			if ($area->areaType == 1)
			{
				$horizontalAlignClass = 'horizontal-text-alignment';
			}
			elseif ($area->areaType == 2)
			{
				$horizontalAlignClass = 'horizontal-text-alignment-cliparts';
			}
		?>
		<input id="textAlign<?php echo $area->id; ?>"
		       name="textAlign<?php echo $area->name; ?>"
		       class="<?php echo $horizontalAlignClass; ?>"
		       type="hidden"
		       value="<?php echo $textAlign; ?>"
			/>
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
	<?php endif; ?>
	{RedDesignBreakDesignAreaChooseHorizontalAlign}

	{RedDesignBreakDesignAreaChooseVerticalAlign}
	<?php if ($this->designType->fontsizer != 'auto') : ?>
		<input id="verticalAlign<?php echo $area->id; ?>"
		       name="verticalAlign<?php echo $area->name; ?>"
		       class="vertical-text-alignment"
		       type="hidden"
		       value="<?php echo $area->verticalAlign; ?>"
			/>
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
	<?php endif; ?>
	{RedDesignBreakDesignAreaChooseVerticalAlign}

	<?php echo '{RedDesignBreakDesignArea' . $area->id . '}'; ?>

<?php endforeach;
