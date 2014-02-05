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
$this->unitConversionRatio = $displayData->unitConversionRatio;
$this->unit = $displayData->unit;

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
					$area->height . ',' .
					$area->areaType; ?>);">
					<strong><?php echo $area->name; ?></strong>
				</a>
			</td>
			<td>
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong>
				<?php echo round($area->width / $this->unitConversionRatio, 2) . $this->unit; ?>,
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong>
				<?php echo round($area->height / $this->unitConversionRatio, 2) . $this->unit; ?>,
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong>
				<?php echo round($area->x1_pos / $this->unitConversionRatio, 2) . $this->unit; ?>,
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong>
				<?php echo round($area->y1_pos / $this->unitConversionRatio, 2) . $this->unit; ?>,
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong>
				<?php echo round($area->x2_pos / $this->unitConversionRatio, 2) . $this->unit; ?>,
				<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong>
				<?php echo round($area->y2_pos / $this->unitConversionRatio, 2) . $this->unit; ?>
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
			<td colspan="5">
				<?php
				$areaType = ReddesignHelpersArea::getAreaType($area->areaType);
				echo RLayoutHelper::render('area.' . $areaType['name'] . 'options', array(
						'area' => $area,
						'designType' => $this->item,
					)
				);
				?>
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

		var areaType   = jQuery("#areaType").val(1);
		var areaFontAlignment = jQuery("#areaFontAlignment" + reddesign_area_id).val();
		var areaVerticalAlignment = jQuery("#areaVerticalAlignment" + reddesign_area_id).val();
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
				'jform[areaType]': areaType,
				'jform[name]': areaName,
				'jform[textalign]': areaFontAlignment,
				'jform[verticalAlign]': areaVerticalAlignment,
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

	/**
	 * Saves settings for an area
	 *
	 * @param reddesign_area_id
	 */
	function saveAreaClipartSettings(reddesign_area_id, areaName) {
		jQuery("#saveAreaClipartSettings" + reddesign_area_id).button("loading");

		var areaType   = jQuery("#areaType").val(2);
		var areaAlignment = jQuery("#areaAlignment" + reddesign_area_id).val();
		var areaVerticalAlignment = jQuery("#areaVerticalAlignment" + reddesign_area_id).val();
		var areaCliparts = jQuery("#areaCliparts" + reddesign_area_id).val();

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxSave",
			data: {
				'jform[id]': reddesign_area_id,
				'jform[textalign]': areaAlignment,
				'jform[verticalAlign]': areaVerticalAlignment,
				'jform[areaCliparts]': areaCliparts
			},
			type: "get",
			success: function (data) {
				jQuery("#designAreaId").val(reddesign_area_id);
				changeAreaType();
				jQuery("#saveAreaSettings" + reddesign_area_id).button("reset")
			},
			error: function (data) {
				console.log('function saveAreaClipartSettings() Error');
				console.log(data);
			}
		});
	}

</script>
