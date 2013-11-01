<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>

<script type="text/javascript">

/**
 * Initiate PX to Unit conversation variables
 */
var pxToUnit    = parseFloat('<?php echo $this->pxToUnit;?>');
var unitToPx    = parseFloat('<?php echo $this->unitToPx;?>');
var ratio       = parseFloat('<?php echo $this->ratio; ?>');
var imageWidth  = parseFloat('<?php echo $this->imageWidth; ?>') * unitToPx * ratio;
var imageHeight = parseFloat('<?php echo $this->imageHeight; ?>') * unitToPx * ratio;
var selectionObjectInstance;

/**
 * Initiate imgAreaSelect plugin
 */
akeeba.jQuery(document).ready(
	function ($) {

		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			onInit: clearSelectionFields,
			onSelectEnd: populateSelectionFields
		});

});

akeeba.jQuery(function() {
	akeeba.jQuery(".colorPickerContainer").colorpicker({
		parts: ['map', 'bar', 'cmyk', 'preview', 'footer'],
		layout: {
			map:		[0, 0, 3, 1],	// Left, Top, Width, Height (in table cells).
			bar:		[3, 0, 1, 4],
			preview:	[4, 0, 1, 1],
			cmyk:		[4, 0, 1, 2]
		}
	});
});

/**
 * Selects area with given parameters. Used onkeyup event in parameter input fields.
 *
 * @param x1
 * @param y1
 * @param x2
 * @param y2
 */
function selectArea(x1, y1, x2, y2) {
	selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
		instance: true,
		handles: true,
		x1: x1,
		y1: y1,
		x2: x2,
		y2: y2
	});
}

/**
 * Updates selection with entered width.
 */
function selectAreaOnWidthKeyUp()
{
	var width, selection, x2;

	width = akeeba.jQuery("#areaWidth").val();
	selection = selectionObjectInstance.getSelection();

	// Convert width to a coordinate.
	width = parseFloat(width) * unitToPx * ratio;

	// Calculate X2 coordinate
	x2 = selection.x1 + width;

	if (width > 0 && x2 < imageWidth)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered height.
 */
function selectAreaOnHeightKeyUp()
{
	var height, selection, y2;

	height = akeeba.jQuery("#areaHeight").val();
	selection = selectionObjectInstance.getSelection();

	// Convert height to a coordinate.
	height = parseFloat(height) * unitToPx * ratio;
	y2 = selection.y1 + height;

	if (height > 0 && y2 < imageHeight)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: selection.y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered X1.
 */
function selectAreaOnX1KeyUp()
{
	var x1, x2, selection;

	x1 = akeeba.jQuery("#areaX1").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	x1 = parseFloat(x1) * unitToPx * ratio;

	// Calculate X2 coordinate.
	x2 = x1 + selection.width;

	if(x1 > 0 && x2 < imageWidth)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered Y1.
 */
function selectAreaOnY1KeyUp()
{
	var y1, y2, selection;

	y1 = akeeba.jQuery("#areaY1").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	y1 = parseFloat(y1) * unitToPx * ratio;

	// Calculate Y2 coordinate.
	y2 = y1 + selection.height;

	if(y1 > 0 && y2 < imageHeight)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered X2.
 */
function selectAreaOnX2KeyUp()
{
	var x2, x1, selection;

	x2 = akeeba.jQuery("#areaX2").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	x2 = parseFloat(x2) * unitToPx * ratio;

	// Calculate X1 coordinate.
	x1 = x2 - selection.width;

	if(x2 < imageWidth && x1 > 0)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: x1,
			y1: selection.y1,
			x2: x2,
			y2: selection.y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Updates selection with entered Y2.
 */
function selectAreaOnY2KeyUp()
{
	var y2, y1, selection;

	y2 = akeeba.jQuery("#areaY2").val();
	selection = selectionObjectInstance.getSelection();

	// Convert X1 to pixels coordinate.
	y2 = parseFloat(y2) * unitToPx * ratio;

	// Calculate X1 coordinate.
	y1 = y2 - selection.height;

	if(y2 < imageHeight && y1 > 0)
	{
		selectionObjectInstance = akeeba.jQuery("img#background").imgAreaSelect({
			instance: true,
			handles: true,
			x1: selection.x1,
			y1: y1,
			x2: selection.x2,
			y2: y2
		});

		populateFields(selectionObjectInstance.getSelection());
	}
}

/**
 * Populate fields from entered values.
 *
 * @param selection
 */
function populateFields(selection)
{
	// Convert pixel to selected unit. Use ratio to calculate and display real metrics instead of scaled down.
	var x1_pos_in_unit = (parseFloat(selection.x1) / ratio) * pxToUnit;
	var y1_pos_in_unit = (parseFloat(selection.y1) / ratio) * pxToUnit;
	var x2_pos_in_unit = (parseFloat(selection.x2) / ratio) * pxToUnit;
	var y2_pos_in_unit = (parseFloat(selection.y2) / ratio) * pxToUnit;
	var width_in_unit  = (parseFloat(selection.width) / ratio) * pxToUnit;
	var height_in_unit = (parseFloat(selection.height) / ratio) * pxToUnit;

	akeeba.jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaWidth").val(width_in_unit.toFixed(0));
	akeeba.jQuery("#areaHeight").val(height_in_unit.toFixed(0));
}

/**
 * Populates parameter fields from selected area
 *
 * @param img
 * @param selection
 */
function populateSelectionFields(img, selection) {
	if(selection.width == 0 || selection.height == 0)
	{
		clearSelectionFields();
		updateImageAreas();
	}
	else
	{
		// Convert pixel to selected unit. Use ratio to calculate and display real metrics instead of scaled down.
		var x1_pos_in_unit = (parseFloat(selection.x1) / ratio) * pxToUnit;
		var y1_pos_in_unit = (parseFloat(selection.y1) / ratio) * pxToUnit;
		var x2_pos_in_unit = (parseFloat(selection.x2) / ratio) * pxToUnit;
		var y2_pos_in_unit = (parseFloat(selection.y2) / ratio) * pxToUnit;
		var width_in_unit  = (parseFloat(selection.width) / ratio) * pxToUnit;
		var height_in_unit = (parseFloat(selection.height) / ratio) * pxToUnit;

		akeeba.jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
		akeeba.jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
		akeeba.jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
		akeeba.jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
		akeeba.jQuery("#areaWidth").val(width_in_unit.toFixed(0));
		akeeba.jQuery("#areaHeight").val(height_in_unit.toFixed(0));
	}
}

/**
 * Clears parameter input fields. Used when select area is not displayed anymore.
 */
function clearSelectionFields() {
	akeeba.jQuery("#designAreaId").val("0");
	akeeba.jQuery("#areaName").val("");
	akeeba.jQuery("#areaX1").val("");
	akeeba.jQuery("#areaY1").val("");
	akeeba.jQuery("#areaX2").val("");
	akeeba.jQuery("#areaY2").val("");
	akeeba.jQuery("#areaWidth").val("");
	akeeba.jQuery("#areaHeight").val("");
}

/**
 * Makes sure that the area has a name, alert otherwise
 *
 * @param update
 */
function preSaveArea(update) {
	if(!akeeba.jQuery("#areaName").val())
	{
		alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_AREA_NAME'); ?>");
	}
	else
	{
		saveArea(update);
	}
}

/**
 * Saves area into the DB via AJAX. And prepares image for another selection.
 *
 * @param update
 */
function saveArea(update) {
	akeeba.jQuery("#saveAreaBtn").button("loading");

	var reddesign_area_id;
	var areaName	= akeeba.jQuery("#areaName").val();
	var areaX1 	= akeeba.jQuery("#areaX1").val();
	var areaY1 	= akeeba.jQuery("#areaY1").val();
	var areaX2 	= akeeba.jQuery("#areaX2").val();
	var areaY2 	= akeeba.jQuery("#areaY2").val();
	var areaWidth  = akeeba.jQuery("#areaWidth").val();
	var areaHeight = akeeba.jQuery("#areaHeight").val();

	var areaX1_in_px = (areaX1 * unitToPx * ratio).toFixed(0);
	var areaY1_in_px = (areaY1 * unitToPx * ratio).toFixed(0);
	var areaX2_in_px = (areaX2 * unitToPx * ratio).toFixed(0);
	var areaY2_in_px = (areaY2 * unitToPx * ratio).toFixed(0);
	var areaWidth_in_px = (areaWidth * unitToPx * ratio).toFixed(0);
	var areaHeight_in_px = (areaHeight * unitToPx * ratio).toFixed(0);

	if(update != 0)
	{
		// if update is not 0 than it holds reddesign_area_id and we are doing update of existing area
		reddesign_area_id = update;
	}
	else
	{
		reddesign_area_id = '';
	}

	akeeba.jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxSave&format=raw",
		data: {
			reddesign_area_id: reddesign_area_id,
			title: areaName,
			reddesign_background_id: <?php echo $this->productionBackground->reddesign_background_id; ?>,
			x1_pos: areaX1_in_px,
			y1_pos: areaY1_in_px,
			x2_pos: areaX2_in_px,
			y2_pos: areaY2_in_px,
			width: areaWidth_in_px,
			height: areaHeight_in_px
		},
		type: "post",
		success: function (data) {
			var json = akeeba.jQuery.parseJSON(data);

			if(update == 0)
			{
				drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
				addAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
				clearAreaSelection();
				clearSelectionFields();
			}
			else
			{
				akeeba.jQuery("#areaDiv" + reddesign_area_id).remove();
				drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
				akeeba.jQuery("#areaDiv" + reddesign_area_id).html(areaName + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
				updateAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
			}

			setTimeout(function () {akeeba.jQuery("#saveAreaBtn").button("reset")}, 500);
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * Draws saved area onto an image.
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param width
 * @param height
 */
function drawArea(reddesign_area_id, title, x1_pos, y1_pos, width, height) {
	width -= 2;
	height -= 3;

	akeeba.jQuery("#backgroundImageContainer").append(
		'<div id="areaDiv' + reddesign_area_id + '" ' +
			'style="position: absolute; ' +
			'width: ' + width + 'px; ' +
			'height: ' + height + 'px; ' +
			'left: ' + x1_pos + 'px; ' +
			'top: ' + y1_pos + 'px; ' +
			'color: rgb(91, 91, 169); border: 2px solid rgb(91, 91, 169);"' +
			'>' + title + '</div>');
}

/**
 * Adds area row to the template table
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function addAreaRow(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	var lastClass = akeeba.jQuery("#areasTBody tr").last().attr("class");
	var rowClass;

	if (lastClass == "row0")
	{
		rowClass = "row1";
	}
	else
	{
		rowClass = "row0";
	}
	akeeba.jQuery('#noAreaMessage').remove();
	akeeba.jQuery("#areasTBody").append(
		'<tr id="areaRow' + reddesign_area_id + '" class="' + rowClass + '">' +
			'<td>' + reddesign_area_id + '</td>' +
			'<td>' +
			'<a href="#" onclick="selectAreaForEdit(' + reddesign_area_id + ',\'' +
			title + '\',' +
			x1_pos + ',' +
			y1_pos + ',' +
			x2_pos + ',' +
			y2_pos + ',' +
			width  + ',' +
			height + ')">' +
			'<strong>' + title + '</strong>' +
			'</a>' +
			'</td>' +
			'<td>' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong> ' +
			(width * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong> ' +
			(height * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong> ' +
			(x1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong> ' +
			(y1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong> ' +
			(x2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong> ' +
			(y2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-primary btn-mini" onclick="showAreaSettings(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>' +
			'</button>' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-danger btn-mini" onclick="removeArea(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>' +
			'</button>' +
			'</td>' +
			'</tr>' +
			'<tr id="areaSettingsRow' + reddesign_area_id + '"	class="' + rowClass + ' hide areaSettingsRow">' +
			'<td colspan="5" >' +

			'<div id="row">' +

			'<div class="span3">' +

			<?php if($this->item->fontsizer != 'auto') : ?>
			'<div class="control-group">' +
			'<label for="areaFontAlignment' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT') ?>' +
			'</label>' +
			'<select id="areaFontAlignment' + reddesign_area_id + '" name="areaFontAlignment' + reddesign_area_id + '"></select>' +
			'</div>' +
			<?php endif; ?>

			'<div class="control-group">' +
			'<label for="areaFonts' + reddesign_area_id + '[]">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALLOWED_FONTS') ?>' +
			'</label>' +
			'<select id="areaFonts' + reddesign_area_id + '" name="areaFonts' + reddesign_area_id + '[]" multiple="multiple"></select>' +

			'</div>' +

			'</div>' +

			<?php if($this->item->fontsizer != 'auto') : ?>

			'<div class="span2">' +

			<?php if($this->item->fontsizer == 'dropdown_numbers' || $this->item->fontsizer == 'dropdown_labels') : ?>
			'<div class="control-group">' +
			'<label for="fontsizerDropdown' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES') ?>' +
			'</label>' +
			'<textarea class="input-small" style="resize: none;" id="fontsizerDropdown' + reddesign_area_id + '"rows="7"></textarea>' +
			'<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ENTER_FONT_SIZES_DESC') ?></span>' +
			'</div>' +
			<?php elseif($this->item->fontsizer == 'slider') : ?>
			'<div class="control-group">' +
			'<label for="fontsizerSliderDefault' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_FONT_SIZE') ?>' +
			'</label>' +
			'<input class="input-small" ' +
			'type="text" ' +
			'value="" ' +
			'maxlength="3" ' +
			'id="fontsizerSliderDefault' + reddesign_area_id + '" ' +
			'name="fontsizerSliderDefault' + reddesign_area_id + '" ' +
			'/>' +
			'</div>' +
			'<div class="control-group">' +
			'<label for="fontsizerSliderMin' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MIN_FONT_SIZE') ?>' +
			'</label>' +
			'<input class="input-small" ' +
			'type="text" ' +
			'value="" ' +
			'maxlength="3" ' +
			'id="fontsizerSliderMin' + reddesign_area_id + '" ' +
			'name="fontsizerSliderMin' + reddesign_area_id + '" ' +
			'/>' +
			'</div>' +
			'<div class="control-group">' +
			'<label for="fontsizerSliderMax' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAX_FONT_SITE') ?>' +
			'</label>' +
			'<input class="input-small" ' +
			'type="text" ' +
			'value="" ' +
			'maxlength="3" ' +
			'id="fontsizerSliderMax' + reddesign_area_id + '" ' +
			'name="fontsizerSliderMax' + reddesign_area_id + '" ' +
			'/>' +
			'</div>' +
			<?php endif; ?>

			'</div>' +

			<?php endif; ?>

			'<div class="span3">' +

			'<div class="control-group">' +
			'<label for="inputFieldType' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_INPUT_FIELD_TYPE') ?>' +
			'</label>' +
			'<input id="inputFieldType' + reddesign_area_id + '[]0" ' +
			'type="radio" ' +
			'checked="checked" ' +
			'value="0" ' +
			'name="inputFieldType' + reddesign_area_id + '[]" ' +
			'onclick="changeInputFieldType(' + reddesign_area_id + ');" ' +
			'/>' +
			'<label id="inputFieldType' + reddesign_area_id + '[]0-lbl" class="radiobtn" for="inputFieldType' + reddesign_area_id + '[]0">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTBOX'); ?>' +
			'</label>' +
			'<input id="inputFieldType1[]' + reddesign_area_id + '" ' +
			'type="radio" ' +
			'value="1" ' +
			'name="inputFieldType' + reddesign_area_id + '[]" ' +
			'onclick="changeInputFieldType(' + reddesign_area_id + ');" ' +
			'/>' +
			'<label id="inputFieldType1[]' + reddesign_area_id + '-lbl" class="radiobtn" for="inputFieldType' + reddesign_area_id + '[]1">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTAREA'); ?>' +
			'</label>' +
			'</div>' +

			'<div class="control-group">' +
			'<label for="defaultText' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DEFAULT_TEXT') ?>' +
			'</label>' +
			'<div id="defaultTextContainer' + reddesign_area_id + '">' +
			'<textarea class="input-small" ' +
			'style="resize: none;" ' +
			'id="defaultText' + reddesign_area_id + '" ' +
			'name="defaultText' + reddesign_area_id + '" ' +
			'></textarea>' +
			'</div>' +
			'</div>' +

			'<div class="control-group">' +
			'<label for="maximumCharsAllowed' + reddesign_area_id + '">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_CHARS') ?>' +
			'</label>' +
			'<input class="input-small" ' +
			'type="text" ' +
			'value="" ' +
			'id="maximumCharsAllowed' + reddesign_area_id + '" ' +
			'name="maximumCharsAllowed' + reddesign_area_id + '" ' +
			'/>' +
			'</div>' +

			'<div class="control-group">' +
			'<label id="maximumLinesAllowedLabel' + reddesign_area_id + '" for="maximumLinesAllowed' + reddesign_area_id + '" style="display: none;">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_MAXIMUM_LINES') ?>' +
			'</label>' +
			'<input class="input-small" ' +
			'type="text" ' +
			'value="" ' +
			'id="maximumLinesAllowed' + reddesign_area_id + '" ' +
			'name="maximumLinesAllowed' + reddesign_area_id + '" ' +
			'style="display: none;" ' +
			'/>' +
			'</div>' +

			'</div>' +

			'<div class="span3">' +

			'<div class="control-group">'+
			'<label class="control-label ">'+
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR'); ?>'+
			'</label>'+
			'<div class="controls">'+
			'<input  class="inputbox" ' +
			'type="radio" ' +
			'name="allColor' + reddesign_area_id + '" ' +
			'value ="0" ' +
			'onclick="hideColorPicker(' + reddesign_area_id + ');" ' +
			'checked="checked">' +
			'<label class="radiobtn"  for="allColor' + reddesign_area_id + '">' +
			'<?php echo JText::_('JNO'); ?>' +
			'</label>&nbsp;' +
			'<input class="inputbox" ' +
			'type="radio" ' +
			'name="allColor' + reddesign_area_id + '" ' +
			'value ="1" ' +
			'onclick="hideColorPicker(' + reddesign_area_id + ');">' +
			'<label class="radiobtn"  for="allColor' + reddesign_area_id + '">' +
			'<?php echo JText::_('JYES'); ?>' +
			'</label>' +
			'<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR_DESC'); ?></span>'+
			'</div>' +
			'</div>' +
			'<div id="allowedColorsRow' + reddesign_area_id + '">' +
			'<div class="control-group">' +
			'<div class="controls">' +
			'<input type="text" ' +
			'class="input-small" ' +
			'value="ff0000" ' +
			'id="color_code' + reddesign_area_id + '" ' +
			'name="color_code' + reddesign_area_id + '" ' +
			'/>&nbsp;' +
			'<button type="button" class="btn btn-small btn-success" onclick="addNewcolor(' + reddesign_area_id + ');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_ADD_COLOR'); ?></span>' +
			'</button>' +
			'</div>' +
			'</div>' +
			'<div class="control-group">'+
			'<label class="control-label ">'+
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_ALLOWED_COLOR'); ?>'+
			'</label>'+
			'<div class="controls">'+
			'<table class="loadcolor" id="extra_table' + reddesign_area_id + '" cellpadding="2" cellspacing="2">'+
			'</table>' +
			'</div>' +
			'</div>' +
			'</div>' +

			'</div>' +

			'<div class="colorPickerContainer">' +
			'<div id="colorPicker' + reddesign_area_id + '" >' +
			'<div class="control-group" >' +
			'<label class="control-label ">' +
			'<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_TEXT'); ?>' +
			'</label>' +
			'<div class="controls">' +
			'<p id="colorpickerHolderC' + reddesign_area_id + '"></p>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +

			'<div class="row span12 offset5">' +

			'<button id="saveAreaSettings' + reddesign_area_id + '" ' +
			'type="button" ' +
			'class="btn btn-success" ' +
			'data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>" ' +
			'onclick="saveAreaSettings(' + reddesign_area_id + ');">' +
			'<span>' +
			'<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>' +
			'</span>' +
			'</button>' +

			'<button type="button" ' +
			'class="btn" ' +
			'onclick="showAreaSettings(' + reddesign_area_id + ');">' +
			'<span>' +
			'<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>' +
			'</span>' +
			'</button>' +

			'</div>' +

			'</td>' +
			'</tr>'
	);


	<?php foreach($this->alignmentOptions as  $alginmentOption) : ?>
	akeeba.jQuery('#areaFontAlignment' + reddesign_area_id).append(
		'<option value="<?php echo $alginmentOption->value; ?>">' +
			'<?php echo $alginmentOption->text; ?>' +
			'</option>'
	);
	<?php endforeach; ?>

	<?php foreach($this->fontsOptions as  $fontsOption) : ?>
	akeeba.jQuery('#areaFonts' + reddesign_area_id).append(
		'<option value="<?php echo $fontsOption->value; ?>">' +
			'<?php echo $fontsOption->text; ?>' +
			'</option>');
	<?php endforeach; ?>

	akeeba.jQuery('#colorpickerHolderC' + reddesign_area_id).ColorPicker({flat: true,
		onChange: function (hsb, hex, rgb) {
			document.getElementById('color_code'+reddesign_area_id).value = hex; // Edited
		}});
}

/**
 * Updates area row in the template table
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function updateAreaRow(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	akeeba.jQuery("#areaRow" + reddesign_area_id).html(
		'<td>' + reddesign_area_id + '</td>' +
			'<td>' +
			'<a href="#" onclick="selectAreaForEdit(' + reddesign_area_id + ',\'' +
			title + '\',' +
			x1_pos + ',' +
			y1_pos + ',' +
			x2_pos + ',' +
			y2_pos + ',' +
			width  + ',' +
			height + ')">' +
			'<strong>' + title + '</strong>' +
			'</a>' +
			'</td>' +
			'<td>' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong> ' +
			(width * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong> ' +
			(height * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong> ' +
			(x1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong> ' +
			(y1_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong> ' +
			(x2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?>, ' +
			'<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong> ' +
			(y2_pos * pxToUnit).toFixed(0) + '<?php echo $this->unit; ?> ' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-primary btn-mini" onclick="showAreaSettings(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_SETTINGS'); ?></span>' +
			'</button>' +
			'</td>' +
			'<td>' +
			'<button type="button" class="btn btn-danger btn-mini" onclick="removeArea(\'' + reddesign_area_id + '\');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>' +
			'</button>' +
			'</td>'
	);
}

/**
 * Uses AJAX to update image with areas
 */
function updateImageAreas() {
	var json;

	akeeba.jQuery("#backgroundImageContainer div").remove();

	akeeba.jQuery.ajax({
		data: {
			reddesign_background_id: <?php echo $this->productionBackground->reddesign_background_id; ?>
		},
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxGetAreas&format=raw",
		success: function (data) {
			json = akeeba.jQuery.parseJSON(data);
			akeeba.jQuery.each( json, function( key, value ) {
				drawArea(value.reddesign_area_id, value.title, value.x1_pos, value.y1_pos, value.width, value.height)
			});
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * Selects area for edit and populates field data accordingly
 *
 * @param reddesign_area_id
 * @param title
 * @param x1_pos
 * @param y1_pos
 * @param x2_pos
 * @param y2_pos
 * @param width
 * @param height
 */
function selectAreaForEdit(reddesign_area_id, title, x1_pos, y1_pos, x2_pos, y2_pos, width, height) {
	akeeba.jQuery("#designAreaId").val(reddesign_area_id);
	akeeba.jQuery("#areaName").val(title);

	// Convert pixel to selected unit. Use ratio to calculate and display real mertics instead of scaled down.
	var x1_pos_in_unit = (parseFloat(x1_pos) / ratio) * pxToUnit;
	var y1_pos_in_unit = (parseFloat(y1_pos) / ratio) * pxToUnit;
	var x2_pos_in_unit = (parseFloat(x2_pos) / ratio) * pxToUnit;
	var y2_pos_in_unit = (parseFloat(y2_pos) / ratio) * pxToUnit;
	var width_in_unit  = (parseFloat(width) / ratio) * pxToUnit;
	var height_in_unit = (parseFloat(height) / ratio) * pxToUnit;

	akeeba.jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
	akeeba.jQuery("#areaWidth").val(width_in_unit.toFixed(0));
	akeeba.jQuery("#areaHeight").val(height_in_unit.toFixed(0));

	selectArea(x1_pos, y1_pos, x2_pos, y2_pos);

	akeeba.jQuery("#areaDiv" + reddesign_area_id).html(title + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
}

/**
 * Function for cancel button
 */
function cancelArea() {
	clearSelectionFields();
	clearAreaSelection();
	updateImageAreas();
}

/**
 * Deletes an area.
 *
 * @param reddesign_area_id
 */
function removeArea(reddesign_area_id) {
	akeeba.jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxRemove&format=raw",
		data: {
			reddesign_area_id: reddesign_area_id
		},
		type: "post",
		success: function (data) {
			akeeba.jQuery("#areaRow" + reddesign_area_id).remove();
			akeeba.jQuery("#areaSettingsRow" + reddesign_area_id).remove();
			updateImageAreas();
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * This function helps jquery slide effect because it doesn't work well with table rows
 *
 * @param reddesign_area_id
 */
function showAreaSettings(reddesign_area_id) {
	akeeba.jQuery(".areaSettingsRow").hide();
	akeeba.jQuery("#areaSettingsRow" + reddesign_area_id).slideToggle("slow");
}

/**
 * Saves settings for an area
 *
 * @param reddesign_area_id
 */
function saveAreaSettings(reddesign_area_id) {
	akeeba.jQuery("#saveAreaSettings" + reddesign_area_id).button("loading");

	var areaFontAlignment = akeeba.jQuery("#areaFontAlignment" + reddesign_area_id).val();
	var fontsizerDropdown = akeeba.jQuery("#fontsizerDropdown" + reddesign_area_id).val();
	var fontsizerSliderDefault = akeeba.jQuery("#fontsizerSliderDefault" + reddesign_area_id).val();
	var fontsizerSliderMin = akeeba.jQuery("#fontsizerSliderMin" + reddesign_area_id).val();
	var fontsizerSliderMax = akeeba.jQuery("#fontsizerSliderMax" + reddesign_area_id).val();
	var inputFieldType = akeeba.jQuery("#inputFieldType" + reddesign_area_id).val();
	var maximumCharsAllowed = akeeba.jQuery("#maximumCharsAllowed" + reddesign_area_id).val();
	var maximumLinesAllowed = akeeba.jQuery("#maximumLinesAllowed" + reddesign_area_id).val();
	var areaFonts = akeeba.jQuery('[name="areaFonts' + reddesign_area_id + '[]"]').val();
	var colorCodes = akeeba.jQuery('[name="colorCodes' + reddesign_area_id + '[]"]');
	var allowAllColor = akeeba.jQuery("input[name='allColor"+reddesign_area_id+"']:checked").val();
	var defaultText = akeeba.jQuery("#defaultText" + reddesign_area_id).val();

	if(allowAllColor==1)
	{
		colorCodes = 1;
		akeeba.jQuery("#extra_table"+reddesign_area_id).html("");
	}
	else
	{
		var arr = [];
		for (var i = 0; i < colorCodes.length ; i++) {
			var colorCode = "#"+colorCodes[i].value;
			arr.push(colorCode);
		}
		colorCodes = arr.join(",");
	}

	akeeba.jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxSave&format=raw",
		data: {
			reddesign_area_id: reddesign_area_id,
			textalign: areaFontAlignment,
			font_id: areaFonts,
			font_size: fontsizerDropdown,
			defaultFontSize: fontsizerSliderDefault,
			minFontSize: fontsizerSliderMin,
			maxFontSize: fontsizerSliderMax,
			input_field_type: inputFieldType,
			maxchar: maximumCharsAllowed,
			maxline: maximumLinesAllowed,
			color_code: colorCodes,
			default_text: defaultText
		},
		type: "post",
		success: function (data) {
			setTimeout(function () {akeeba.jQuery("#saveAreaSettings" + reddesign_area_id).button("reset")}, 500);
		},
		error: function (data) {
			alert(data);
		}
	});
}

/**
 * Adds new color row
 *
 * @param reddesign_area_id
 */
function addNewcolor(reddesign_area_id)
{
	var color_code = akeeba.jQuery("#color_code" + reddesign_area_id).val();
	if(color_code!="")
	{
		colorcode = "#"+color_code;
	}

	akeeba.jQuery("#extra_table"+reddesign_area_id).append(
		'<tr>' +
			'<td>' +
			'<div class="colorSelector_list" >' +
			'<div style=" background-color:' + colorcode + '" >&nbsp;</div>' +
			'</div>' +
			'</td>' +
			'<td>' +
			'<div>' + colorcode + '</div>' +
			'<input type="hidden" ' +
			'value="' + color_code +'" ' +
			'class="colorCodes' + reddesign_area_id + '" ' +
			'name="colorCodes' + reddesign_area_id + '[]"  ' +
			'id="colorCodes' + reddesign_area_id + '">' +
			'</td>' +
			'<td>' +
			'<div>' +
			'<input type="hidden" name="colour_id'+reddesign_area_id+'[]" id="colour_id'+reddesign_area_id+'" value="'+color_code+'">'+
			'<button type="button" class="btn btn-small btn-danger delete" onclick="deleteColor(this,' + reddesign_area_id + ');">' +
			'<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>' +
			'</button>' +
			'</div>'+
			'</td>'+
			'</tr>'
	);
}

/**
 * Hides colorpicker
 *
 * @param reddesign_area_id
 */
function hideColorPicker(reddesign_area_id)
{
	if(akeeba.jQuery("input[name='allColor"+reddesign_area_id+"']:checked").val()==1)
	{
		akeeba.jQuery("#colorPicker"+reddesign_area_id).hide();
		akeeba.jQuery("#allowedColorsRow"+reddesign_area_id).hide();
	}
	else
	{
		akeeba.jQuery("#colorPicker"+reddesign_area_id).show();
		akeeba.jQuery("#allowedColorsRow"+reddesign_area_id).show();
	}
}

/**
 * Controls what needs to be shown regarding to input field type.
 *
 * @param reddesign_area_id
 */
function changeInputFieldType(reddesign_area_id)
{
	var selectedType = akeeba.jQuery("#inputFieldType" + reddesign_area_id).val();

	if (selectedType == 1)
	{
		akeeba.jQuery("#maximumLinesAllowedContainer" + reddesign_area_id).css("display", "inline");
	}
	else
	{
		akeeba.jQuery("#maximumLinesAllowedContainer" + reddesign_area_id).css("display", "none");
	}
}

/**
 * Delete color code from DB and remove that color row
 *
 * @param r
 * @param reddesign_area_id
 */
function deleteColor(r,reddesign_area_id)
{
	var i=r.parentNode.parentNode.parentNode.rowIndex;
	document.getElementById('extra_table'+reddesign_area_id).deleteRow(i);

	// Do AJAX Delete
	saveAreaSettings(reddesign_area_id);
	// End
}


</script>

<style type="text/css">
	#backgroundImageContainer {
		position: relative;
	}

	<?php foreach($this->areas as $area) : ?>
	#areaDiv<?php echo $area->reddesign_area_id; ?> {
		position: absolute;
		top: <?php echo $area->y1_pos;?>px;
		left: <?php echo $area->x1_pos;?>px;
		width: <?php echo $area->width;?>px;
		height: <?php echo $area->height;?>px;
		color: #5B5BA9;
		border: 2px solid #5B5BA9;
	}
	<?php endforeach; ?>
</style>