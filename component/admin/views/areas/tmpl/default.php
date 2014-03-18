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

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->item->designtype_id . '&tab=design-areas';
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
			<th>
				<button type="button" class="btn btn-success btn-mini" onclick="saveOrder();">
					<span><?php echo JText::_('COM_REDDESIGN_COMMON_SAVE_ORDER'); ?></span>
				</button>
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
			<td class="span4 col-md4">
				<a href="#" onclick="selectAreaForEdit(<?php echo $area->id . ',\'' .
										$area->name . '\',' .
										$area->x1_pos . ',' .
										$area->y1_pos . ',' .
										$area->x2_pos . ',' .
										$area->y2_pos . ',' .
										$area->width . ',' .
										$area->height . ',' .
										$area->areaType; ?>
					);">
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
			<td>
				<?php if ($i != 0) : ?>
					<button class="btn btn-success fileinput-button btn-mini"
					        type="button"
					        onclick="orderUpDown(<?php echo $area->id ?>, <?php echo $area->ordering; ?>, 1);">
						<i class="icon-arrow-up"></i>
					</button>
				<?php endif; ?>

				<?php if ($i != (count($this->areas) - 1)) : ?>
					<button class="btn btn-success fileinput-button btn-mini"
					        type="button"
					        onclick="orderUpDown(<?php echo $area->id ?>, <?php echo $area->ordering; ?>, 0);">
						<i class="icon-arrow-down"></i>
					</button>
				<?php endif; ?>

				<input type="text" name="areasOrder[]" class="input-mini" value="<?php echo $area->ordering; ?>" />
				<input type="hidden"  name="areasCid[]" value="<?php echo $area->id; ?>" />
			</td>
		</tr>

		<tr id="areaSettingsRow<?php echo $area->id ?>" class="<?php echo 'row' . $m; ?> hide areaSettingsRow">
			<td colspan="6">
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
				}
				else
				{
					alert(jsonData.message);
				}
			},
			error: function (data) {
				console.log('function removeArea() Error');
			}
		});
	}

	/**
	 * Saves ordering.
	 */
	function saveOrder() {
		var areasCid = new Array();
		jQuery("input[name^='areasCid']").each(function() {areasCid.push(jQuery(this).val());});

		var areasOrder = new Array();
		jQuery("input[name^='areasOrder']").each(function() {areasOrder.push(jQuery(this).val());});

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=areas.saveOrderAjax&tmpl=component",
			data: {
				cid: areasCid,
				order: areasOrder
			},
			type: "post",
			success: function (data) {
				if (data == 1)
				{
					window.location.href = "<?php echo $return_url; ?>";
				}
				else
				{
					console.log('function saveOrder() Error');
				}
			},
			error: function (data) {
				console.log('function saveOrder() Error');
			}
		});
	}

	/**
	 * Moves an area up in the order on arrow up click.
	 *
	 * @param   areaId         int  Clickec Area Id.
	 * @param   previousOrder  int  Clickec Area previous order.
	 */
	function orderUpDown(areaId, previousOrder, isUp)
	{
		var areasCid = new Array();
		jQuery("input[name^='areasCid']").each(function() {areasCid.push(jQuery(this).val());});

		var areasOrder = new Array();
		jQuery("input[name^='areasOrder']").each(function() {areasOrder.push(jQuery(this).val());});

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=areas.orderUpDownAjax&tmpl=component",
			data: {
				areaId: areaId,
				previousOrder: previousOrder,
				cid: areasCid,
				order: areasOrder,
				isUp: isUp
			},
			type: "post",
			success: function (data) {
				if (data == 1)
				{
					window.location.href = "<?php echo $return_url; ?>";
				}
				else
				{
					console.log('function saveOrder() Error');
				}
			},
			error: function (data) {
				console.log('function saveOrder() Error');
			}
		});
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

		var areaType   = jQuery("#areaType").val(1).val();
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
				jQuery("#designAreaId").val(reddesign_area_id);
				changeAreaType();
				jQuery("#saveAreaSettings" + reddesign_area_id).button("reset");
			},
			error: function (data) {
				console.log('function saveAreaSettings() Error');
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

		var areaType   = jQuery("#areaType").val(2).val();
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
			type: "post",
			success: function (data) {
				jQuery("#designAreaId").val(reddesign_area_id);
				changeAreaType();
				jQuery("#saveAreaSettings" + reddesign_area_id).button("reset");
			},
			error: function (data) {
				console.log('function saveAreaClipartSettings() Error');
			}
		});
	}

</script>
