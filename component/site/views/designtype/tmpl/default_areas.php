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

<div class="row-fluid">
	<h4 class="page-header">
		<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?>
	</h4>
	<?php foreach($this->productionBackgroundAreas as $area) : ?>

		<div class="control-group span4">
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

		<div class="span4">
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
				</label>
				<div class="controls">
					<?php
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
				</div>
			</div>
			<div class="control-group">

				<?php if ($this->item->fontsizer != 'auto') : ?>
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONTSIZE'); ?>
				</label>
				<?php endif; ?>

				<div class="controls">
				<?php
					if ($this->item->fontsizer === 'slider')
					{
						FOFTemplateUtils::addJS('media://com_reddesign/assets/js/bootstrap-slider.js');
						FOFTemplateUtils::addCSS('media://com_reddesign/assets/css/slider.css');
				?>
						<input type="hidden" class="span2" value="<?php echo $area->defaultFontSize?>" data-slider-min="<?php echo $area->minFontSize?>" data-slider-max="<?php echo $area->maxFontSize?>" data-slider-value="[<?php echo $area->defaultFontSize?>]" id="fontSize<?php echo $area->reddesign_area_id?>" >
				<?php
					}
					elseif ($this->item->fontsizer === 'dropdown')
					{
						$areaFontSizes 	= explode(',', $area->font_size);
						$options 		= array();

						foreach ($areaFontSizes as $key => $value)
						{
							$options[] = JHTML::_('select.option', $value, $value);
						}

						echo JHTML::_('select.genericlist', $options, 'fontSize' . $area->reddesign_area_id, 'class="inputbox" onChange="customize(0);"', 'value', 'text', null);
					}
				?>
				</div>
			</div>
		</div>

		<div class="control-group span3">

			<?php if (!empty($area->color_code)) : ?>
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_COLOR_CODE'); ?>
			</label>
			<?php endif; ?>

			<div class="controls">

				<?php if (empty($area->color_code)) : ?>
					<input type="hidden" name="colorCode<?php echo $area->reddesign_area_id?>" value="000000" id="colorCode<?php echo $area->reddesign_area_id?>">
				<?php elseif ($area->color_code == 1) : ?>
					<div id="color-selector<?php echo $area->reddesign_area_id;?>" class="colorSelector">
							<div style="background-color: #000000"></div>
					</div>
					<div class="help-block">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLORS_HELP'); ?>
					</div>
					<input type="hidden" name="colorCode<?php echo $area->reddesign_area_id?>" value="000000" id="colorCode<?php echo $area->reddesign_area_id?>">
				<?php else : ?>
					<div id="loadColors">
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
										$defaultColorVal = str_replace('#', '', $colorData[0]);
										$colorCodeVal = str_replace('#', '', $colorData[$j]);
							?>
										<td>
											<div class="colorSelector_list" >
												<div onClick="setColorCode(<?php echo $area->reddesign_area_id?>,'<?php echo $colorCodeVal;?>');" style="background-color:<?php echo $colorData[$j]?>;cursor:pointer;">&nbsp;</div>										</div>
											</div>
										</td>
							<?php
										if ($i % 4 == 0)
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
</div>

<script type="">
	/**
	 * Add click event to Customize button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#orderDesign', function () {
					akeeba.jQuery('#task').val('orderProduct');
					var design = {
						areas: [],
						accessories: []
					};

					<?php foreach($this->productionBackgroundAreas as $area) : ?>
					design.areas.push({
						"id" : 			'<?php echo $area->reddesign_area_id; ?>',
						"textArea" :	akeeba.jQuery('#textArea<?php echo $area->reddesign_area_id; ?>').val(),
						"fontArea" : 	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val(),
						"fontColor" :	akeeba.jQuery('#colorCode<?php echo $area->reddesign_area_id; ?>').val(),
						"fontSize" :	akeeba.jQuery('#fontSize<?php echo $area->reddesign_area_id; ?>').val(),
						"fontTypeId" :	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val()
					});
					<?php endforeach; ?>

					<?php if($this->accessorytypes) : ?>
						<?php foreach ($this->accessorytypes as $accessorytype) : ?>
							<?php foreach ($accessorytype->accessories as $accessory) : ?>
							if(akeeba.jQuery("#AccessoryId<?php echo $accessory->reddesign_accessory_id; ?>").is(':checked'))
							{
								design.accessories.push({
										"id" : akeeba.jQuery('#AccessoryId<?php echo $accessory->reddesign_accessory_id; ?>:checked').val()
								});
							}
							<?php endforeach; ?>
						<?php endforeach; ?>
					<?php endif; ?>

					design = JSON.stringify({Design: design });
					akeeba.jQuery('#designAreas').val(design);
					akeeba.jQuery('#designform').submit();
				}
			);

		});
</script>
