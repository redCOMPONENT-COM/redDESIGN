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
<script type="text/javascript">
	/**
	 * Initiate imgAreaSelect plugin
	 */
	akeeba.jQuery(document).ready(
		function ($) {
			<?php foreach ($this->productionBackgroundAreas as  $area) : ?>
			var reddesign_area_id = parseInt(<?php echo $area->reddesign_area_id;?>);
			akeeba.jQuery('#colorSelector' + reddesign_area_id).ColorPicker({
				designId:reddesign_area_id,
				color: '#000000',
				onChange: function (hsb, hex, rgb, reddesign_area_id) {
					akeeba.jQuery('#colorSelector' +reddesign_area_id+ ' div').css('backgroundColor', '#' + hex);
					document.getElementById('colorCode'+reddesign_area_id).value = hex; // Edited
				}
			});
			<?php endforeach; ?>
		});
	/**
	 * Set selected color for designarea.
	 *
	 * @param reddesign_area_id
	 * @param colorCode
	 */
	function setColorCode(reddesign_area_id, colorCode)
	{
		document.getElementById('colorCode'+reddesign_area_id).value = colorCode;
		akeeba.jQuery('#fontColor'+reddesign_area_id+ ' div').css('backgroundColor', '#' + colorCode);
		akeeba.jQuery('#fontColor'+reddesign_area_id).show();
	}
</script>
<div class="row">
	<div class="span9">
		<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?></h3>
		<?php foreach($this->productionBackgroundAreas as $area) : ?>
			<div class="control-group">
				<label class="control-label ">
					<strong><?php echo $area->title; ?></strong>
				</label>
				<div class="controls">
					<input
						type="text"
						name="textArea<?php echo $area->reddesign_area_id; ?>"
						placeholder="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AREAS_TYPE_TEXT'); ?>"
						id="textArea<?php echo $area->reddesign_area_id; ?>"
						value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
				</label>
				<div class="controls">
					<?php
					if (empty($area->font_id))
					{
						echo JText::_('COM_REDDESIGN_DESIGNTYPE_NO_FONTS');
					}
					else
					{
						$areaFontsIds 	= explode(',', $area->font_id);
						$options 		= array();

						foreach ($areaFontsIds as $key => $value) :
							$options[] = JHTML::_('select.option', $value, $this->fonts[$value]->title);
						endforeach;

						echo JHTML::_(
							'select.genericlist',
							$options,
							'fontArea' . $area->reddesign_area_id,
							'class="inputbox"',
							'value',
							'text',
							null
						);
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<?php if (!empty($area->color_code)) : ?>
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_COLOR_CODE'); ?>
				</label>
				<?php endif; ?>
				<div class="controls">
					<?php if (empty($area->color_code)) : ?>
						<input type="hidden" class="colorCode<?php echo $area->reddesign_area_id?>" name="colorCode<?php echo $area->reddesign_area_id?>" value="000000" id="colorCode<?php echo $area->reddesign_area_id?>">
					<?php elseif ($area->color_code == 1) : ?>
						<div id="colorSelector<?php echo $area->reddesign_area_id;?>" class="colorSelector"><div style="background-color: #000000"></div></div>
						<input type="hidden" class="colorCode<?php echo $area->reddesign_area_id?>" name="colorCode<?php echo $area->reddesign_area_id?>" value="000000" id="colorCode<?php echo $area->reddesign_area_id?>">
						<div id="ColorPickerNote"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLORPICKER_NOTE'); ?></div>
					<?php else : ?>
						<div id="loadColors" style="width:300px;">
						<table class="table" cellpadding="0" cellspacing="0" border="0" >
							<tr valign="top" class="color">
							<?php
							$i = 0;

							if (strpos($area->color_code, "#") !== false)
							{
								$colorData = explode(",", $area->color_code);

								for ($j = 0; $j < count($colorData); $j++)
								{
									$i++;
									$defaultColor = $colorData[0];
									$defaultColorVal = str_replace("#","", $colorData[0]);
									$colorCodeVal = str_replace("#", "", $colorData[$j]);
								?>
									<td>
										<div class="colorSelector_list" >
											<div onClick="setColorCode(<?php echo $area->reddesign_area_id?>,'<?php echo $colorCodeVal;?>');" style="background-color:<?php echo $colorData[$j]?>;cursor:pointer;">&nbsp;</div>										</div>
										</div>
									</td>
									<?php
									if ($i % 5 == 0)
									{
										echo '</tr><tr>';
									}
								}
							}
							?>
							</tr>
						</table>
						</div>
					<?php endif; ?>
				</div>
				<?php if (!empty($area->color_code) && $area->color_code != 1) : ?>
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DEFAULT_COLOR_CODE'); ?>
				</label>
				<div class="controls">
					<div class="colorSelector_list" id="fontColor<?php echo $area->reddesign_area_id?>" >
						<div  style="background-color:<?php echo $defaultColor;?>;cursor:pointer;">&nbsp;</div>
					</div>
					<input type="hidden" class="colorCode<?php echo $area->reddesign_area_id?>" name="colorCode<?php echo $area->reddesign_area_id?>" value="<?php echo $defaultColorVal;?>" id="colorCode<?php echo $area->reddesign_area_id?>">
				</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<div class="form-actions">
			<button
				type="button"
				class="btn btn-success"
				data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE_LOADING') ?>"
				id="customizeDesign"
				><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE'); ?></button>
			<button type="button" class="btn" id="orderDesign"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_ORDER'); ?></button>
		</div>
	</div>
</div>

<script type="">
	/**
	 * Add click event to Customize button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#orderDesign', function () {
					akeeba.jQuery('#task').val('orderProduct');
					akeeba.jQuery('#designform').submit();
				}
			);

		});
</script>
