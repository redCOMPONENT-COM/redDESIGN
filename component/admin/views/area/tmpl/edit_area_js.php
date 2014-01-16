<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

if (isset($displayData))
{
	$this->areas = $displayData->items;
	$this->item = $displayData->item;
	$this->productionBackground = $displayData->productionBackground;
	$this->bgBackendPreviewWidth = $displayData->bgBackendPreviewWidth;
	$this->unit = $displayData->unit;
	$this->pxToUnit = $displayData->pxToUnit;
	$this->unitToPx = $displayData->unitToPx;
	$this->sourceDpi = $displayData->sourceDpi;
	$this->productionBgAttributes = $displayData->productionBgAttributes;
	$this->fontsOptions = $displayData->fontsOptions;
	$this->inputFieldOptions = $displayData->inputFieldOptions;
}

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->item->designtype_id . '&tab=design-areas';

?>

<script type="text/javascript">

	/**
	 * Initiate PX to Unit conversation variables
	 */
	var pxToUnit    = parseFloat('<?php echo $this->pxToUnit;?>');
	var unitToPx    = parseFloat('<?php echo $this->unitToPx;?>');
	var imageWidth  = parseFloat('<?php echo (!empty($this->productionBgAttributes->width) ? $this->productionBgAttributes->width : ''); ?>');
	var imageHeight = parseFloat('<?php echo (!empty($this->productionBgAttributes->height) ? $this->productionBgAttributes->height : ''); ?>');
	var previewWidth  = parseFloat('<?php echo (!empty($this->bgBackendPreviewWidth) ? $this->bgBackendPreviewWidth : ''); ?>');

	var ratio = previewWidth / imageWidth;
	var previewHeight = imageHeight * ratio;

	/**
	 * Initiate SVG area selector variables. Basically it is drawing rectangle.
	 */
	var rootSnapSvgObject;
	var mouseDownX = 0;
	var mouseDownY = 0;
	var currentRectangleId = "none";
	var currentGroupIdOnHover = "none";
	var insideGroup = "false";

	/**
	 * Initiate snap.svg
	 */
	jQuery(document).ready(
		function ($) {

			<?php if ($this->areas != '') : ?>
				<?php foreach ($this->areas as  $area) : ?>
				// Check div before add farbtastic
				if (jQuery("#colorPickerContainer<?php echo $area->id ?>")[0])
				{
					var colorPicker<?php echo $area->id ?> = jQuery.farbtastic("#colorPickerContainer<?php echo $area->id ?>");
					colorPicker<?php echo $area->id ?>.linkTo("#colorPickerSelectedColor<?php echo $area->id; ?>");
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

				jQuery(document).on("keyup", "#colorPickerSelectedColor<?php echo $area->id; ?>", function() {
					var hex = jQuery("#colorPickerSelectedColor<?php echo $area->id; ?>").val();
					loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
				});

				jQuery(document).on("mouseup", "#colorPickerContainer<?php echo $area->id; ?>", function() {
					var hex = jQuery("#colorPickerSelectedColor<?php echo $area->id; ?>").val();
					loadCMYKValues(hex, parseInt("<?php echo $area->id; ?>"));
				});

				jQuery("#allColors<?php echo $area->id; ?>").click(function () {
					jQuery("#colorsContainer<?php echo $area->id; ?>").toggle(!this.checked);
					jQuery("#addColorContainer<?php echo $area->id; ?>").toggle(!this.checked);
					jQuery("#selectedColorsPalette<?php echo $area->id; ?>").toggle(!this.checked);
				});


				jQuery("#addColorButton<?php echo $area->id ?>").click(function () {
					addColorToList(parseInt("<?php echo $area->id; ?>"))
				});
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if (!empty($this->productionBackground->svg_file)) : ?>
				rootSnapSvgObject = Snap("#svgForAreas");

				Snap.load(
					"<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file; ?>",
					function (f) {
						rootSnapSvgObject.append(f);

						var loadedSvgFromFile = jQuery("#svgForAreas").find("svg")[0];
						loadedSvgFromFile.setAttribute("width", previewWidth);
						loadedSvgFromFile.setAttribute("height", previewHeight);
						loadedSvgFromFile.setAttribute("id", "svgCanvas");

						var rootElement = document.getElementById("svgForAreas");
						rootElement.setAttribute("width", previewWidth);
						rootElement.setAttribute("height", previewHeight);
						rootElement.setAttribute("overflow", "hidden");

						rootSnapSvgObject.mousedown(onBegginDrawRectangle);

						rootSnapSvgObject.mouseup(endDrawRectangle);
					}
				);


			<?php endif; ?>
		}
	);

	function onBegginDrawRectangle(e)
	{
		if (insideGroup == "false")
		{
			var offset = jQuery("#svgForAreas").offset();
			mouseDownX = e.pageX - offset.left;
			mouseDownY = e.pageY - offset.top;

			rootSnapSvgObject.mousemove(onDrawingRectangle);

			var currentRectangle = drawRectangle(mouseDownX, mouseDownY, 0, 0, "transparent", "#CA202C", 3);
			currentRectangle.node.id = currentRectangle.id;
			currentRectangleId = currentRectangle.node.id;

			var currentSizer = drawRectangle(mouseDownX, mouseDownY, 10, 10, "#CA202C", "none", 0);
			currentSizer.node.id = "sizer" + currentRectangleId;
			currentSizer.hover(sizerIn, sizerOut);

			var group = rootSnapSvgObject.group(currentSizer, currentRectangle);
			group.node.id = "group" + currentRectangleId;
			group.hover(groupIn, groupOut);
			group.drag();

		}
	}

	function onDrawingRectangle(e)
	{
		var offset = jQuery("#svgForAreas").offset();
		var upX = e.pageX - offset.left;
		var upY = e.pageY - offset.top;

		var width = upX - mouseDownX;
		var height = upY - mouseDownY;

		var movingRect = rootSnapSvgObject.select("#" + currentRectangleId);
		movingRect.attr({
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0
		});

		var sizerX = mouseDownX + width;
		var sizerY = mouseDownY + height;
		var sizer = rootSnapSvgObject.select("#sizer" + currentRectangleId);
		sizer.attr({
			width: width > 0 ? 10 : 0,
			height: height > 0 ? 10 : 0,
			x: sizerX,
			y: sizerY
		});

	}

	function endDrawRectangle(e)
	{
		rootSnapSvgObject.unmousemove();
		currentRectangleId = "none";
	}

	function drawRectangle(x, y, w, h, fill, stroke, strokeWidth)
	{
		var rectangle = rootSnapSvgObject.rect(x, y, w, h);

		rectangle.attr({
			fill: fill,
			stroke: stroke,
			strokeWidth: strokeWidth
		});

		return rectangle;
	}

	function groupIn()
	{
		currentGroupIdOnHover = this.node.id;
		insideGroup = "true";
	}

	function groupOut()
	{
		currentGroupIdOnHover = "none";
		insideGroup = "false";
	}

	function sizerIn()
	{
		var sizerId = this.node.id;
		currentRectangleId = sizerId.replace("sizer", "");

		var group = rootSnapSvgObject.select("#group" + currentRectangleId);
		group.undrag();

		rootSnapSvgObject.mousedown(onBegginResizeRectangle);
	}

	function sizerOut()
	{
		//currentRectangleId = "none";
		var group = rootSnapSvgObject.select("#group" + currentRectangleId);
		group.drag();
		group.hover(groupIn, groupOut);
	}

	function onBegginResizeRectangle(e)
	{
		rootSnapSvgObject.mousemove(resizeRectangle);
		rootSnapSvgObject.mouseup(endResizingRectangle);
	}

	function resizeRectangle(e)
	{
		var offset = jQuery("#svgForAreas").offset();
		var upX = e.pageX - offset.left;
		var upY = e.pageY - offset.top;

		var width = upX - mouseDownX;
		var height = upY - mouseDownY;

		var movingRect = rootSnapSvgObject.select("#" + currentRectangleId);
		movingRect.attr({
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0
		});

		var sizerX = mouseDownX + width;
		var sizerY = mouseDownY + height;
		var sizer = rootSnapSvgObject.select("#sizer" + currentRectangleId);
		sizer.attr({
			width: width > 0 ? 10 : 0,
			height: height > 0 ? 10 : 0,
			x: sizerX,
			y: sizerY
		});
	}

	function endResizingRectangle(e)
	{
		rootSnapSvgObject.unmousemove();
	}

	/**
	 * Makes sure that the area has a name, alert otherwise
	 *
	 * @param update
	 */
	function preSaveArea(update) {
		if(!jQuery("#areaName").val())
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
	function saveArea(update)
	{
		jQuery("#saveAreaBtn").button("loading");

		var reddesign_area_id;
		var areaName	= jQuery("#areaName").val();
		var areaX1 		= jQuery("#areaX1").val();
		var areaY1 		= jQuery("#areaY1").val();
		var areaX2 		= jQuery("#areaX2").val();
		var areaY2 		= jQuery("#areaY2").val();
		var areaWidth  	= jQuery("#areaWidth").val();
		var areaHeight 	= jQuery("#areaHeight").val();

		var areaX1_in_px 		= (areaX1 * unitToPx * ratio).toFixed(0);
		var areaY1_in_px 		= (areaY1 * unitToPx * ratio).toFixed(0);
		var areaX2_in_px 		= (areaX2 * unitToPx * ratio).toFixed(0);
		var areaY2_in_px 		= (areaY2 * unitToPx * ratio).toFixed(0);
		var areaWidth_in_px 	= (areaWidth * unitToPx * ratio).toFixed(0);
		var areaHeight_in_px 	= (areaHeight * unitToPx * ratio).toFixed(0);

		if(update != 0)
		{
			// if update is not 0 than it holds reddesign_area_id and we are doing update of existing area
			reddesign_area_id = update;
		}
		else
		{
			reddesign_area_id = '';
		}

		var productionBackground = <?php echo (!empty($this->productionBackground->id) ? $this->productionBackground->id : 0); ?>;

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxSave",
			data: {
				'jform[id]': reddesign_area_id,
				'jform[name]': areaName,
				'jform[background_id]': productionBackground,
				'jform[x1_pos]': areaX1_in_px,
				'jform[y1_pos]': areaY1_in_px,
				'jform[x2_pos]': areaX2_in_px,
				'jform[y2_pos]': areaY2_in_px,
				'jform[width]': areaWidth_in_px,
				'jform[height]': areaHeight_in_px
			},
			type: "post",
			success: function (data)
			{
				var json = jQuery.parseJSON(data);

				setTimeout(function () {jQuery("#saveAreaBtn").button("reset")}, 500);

				if (json.status == 1)
				{
					if (update == 0)
					{
						//drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
						//addAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
						clearSelectionFields();
					}
					/* @Todo
					 else
					 {
					 jQuery("#areaDiv" + reddesign_area_id).remove();
					 drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
					 jQuery("#areaDiv" + reddesign_area_id).html(areaName + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
				 updateAreaRow(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.x2_pos, json.y2_pos, json.width, json.height);
				 }
				 */

					window.location.href = "<?php echo $return_url; ?>";
				}
				else
				{
					jQuery('#system-message-container').html(json.message);
				}
			},
			error: function (data)
			{
				console.log("Error: " + data);
			}
		});
	}

	/**
	 * Adds selected color to the list.
	 *
	 * @param areaId integer Area ID.
	 *
	 * @return void
	 */
	function addColorToList(areaId)
	{

		var selectedColor = jQuery("#colorPickerSelectedColor" + areaId).val();
		var colorCodes = jQuery("#colorCodes" + areaId).val();

		// Check if the same color is already added.
		if (colorCodes.indexOf(selectedColor) == -1)
		{
			// Create color div element.
			var element = '<div class="colorDiv" ' +
				'id="' + areaId + '-' + selectedColor.replace("#","") + '" ' +
				'style="background-color:' + selectedColor + ';" ' +
				'onclick="removeColorFromList(' + areaId + ', \'' + selectedColor + '\');">' +
				'<i class="glyphicon icon-remove"></i>' +
				'<input type="hidden" value="' + selectedColor + '" />' +
				'</div>';
			jQuery("#selectedColorsPalette" + areaId).append(element);

			// Update color codes hidden input field.
			if (colorCodes == "" || parseInt(colorCodes) == 1)
			{
				colorCodes = selectedColor;
			}
			else
			{
				colorCodes = colorCodes + "," + selectedColor;
			}

			jQuery("#colorCodes" + areaId).val(colorCodes);

			jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxUpdateColors",
				data: {
					id: areaId,
					color_code: colorCodes
				},
				type: "post",
				error: function (data) {
					console.log('function addColorToList() Error');
					console.log(data);
				}
			});
		}
		else
		{
			alert("<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_COLOR_ALREADY_ADDED'); ?>");
		}
	}


	/**
	 * Removes color from the list and from the database.
	 *
	 * @param areaId int Area ID.
	 * @param colorToRemove string Hexadecimal code of the color to be removed.
	 */
	function removeColorFromList(areaId, colorToRemove)
	{
		var colorCodes = jQuery("#colorCodes" + areaId).val();
		colorCodes = colorCodes.split(",");

		colorCodes = jQuery.grep(colorCodes, function(value) {
			return value != colorToRemove;
		});

		colorCodes = colorCodes.join(",");

		jQuery("#colorCodes" + areaId).val(colorCodes);

		jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&task=area.ajaxUpdateColors",
			data: {
				id: areaId,
				color_code: colorCodes
			},
			type: "post",
			error: function (data) {
				console.log('removeColorFromList() Error');
				console.log(data);
			}
		});

		jQuery("#" + areaId + "-" + colorToRemove.replace("#","")).remove();
	}

	/**
	 * Gets hexadecimal value of the color generated by entering values in CMYK fields.
	 *
	 * @param areaId int Area ID
	 *
	 * @return string hexadecimal value
	 */
	function getNewHexColor(areaId)
	{
		var c = jQuery("#C" + areaId).val();
		var m = jQuery("#M" + areaId).val();
		var y = jQuery("#Y" + areaId).val();
		var k = jQuery("#K" + areaId).val();

		var colorObject = new CMYK(c, m, y, k);
		var rgb = ColorConverter.toRGB(colorObject);

		return rgbToHex(rgb.r, rgb.g, rgb.b);
	}

	/**
	 * Converts hexadecimal value into RGB value
	 *
	 * @param r int Red value
	 * @param g int Green value
	 * @param b int Blue value
	 *
	 * @return string hexadecimal value
	 */
	function rgbToHex(r, g, b) {
		return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
	}

	/**
	 * Load CMYK values into related CMYK area fields.
	 * Gets them from a hexadecimal value.
	 *
	 * @param hex int Hexadecimal value.
	 * @param areaId int Area ID.
	 */
	function loadCMYKValues(hex, areaId) {
		var colorObject = new RGB(hexToRgb(hex).r, hexToRgb(hex).g, hexToRgb(hex).b);
		var cmyk = ColorConverter.toCMYK(colorObject);

		jQuery("#C" + areaId).val(cmyk.c);
		jQuery("#M" + areaId).val(cmyk.m);
		jQuery("#Y" + areaId).val(cmyk.y);
		jQuery("#K" + areaId).val(cmyk.k);
	}

	/**
	 * Converts hexadecimal value to RGB value.
	 *
	 * @param hex string Hexadecimal value.
	 *
	 * @return object with r,g and b values.
	 */
	function hexToRgb(hex) {
		// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : null;
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

		jQuery("#designAreaId").val(reddesign_area_id);
		jQuery("#areaName").val(title);
		if (areaBoxes[reddesign_area_id])
		{
			current_area_id = reddesign_area_id;
		}
		else
		{
			areaBoxes[reddesign_area_id] = new Array();
			areaBoxes[reddesign_area_id]['x'] = x1_pos;
			areaBoxes[reddesign_area_id]['x2'] = x2_pos;
			areaBoxes[reddesign_area_id]['y'] = y1_pos;
			areaBoxes[reddesign_area_id]['y2'] = y2_pos;
			current_area_id = reddesign_area_id;
			selectArea(x1_pos, y1_pos, x2_pos, y2_pos, width, height);
		}

		setCoordinatesToValues(x1_pos, y1_pos, x2_pos, y2_pos, width, height);

		jQuery("#areaDiv" + reddesign_area_id).html(title + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
	}

	function setCoordinatesToValues(x1_pos, y1_pos, x2_pos, y2_pos, width, height, showIt)
	{
		// Convert pixel to selected unit. Use ratio to calculate and display real mertics instead of scaled down.
		var x1_pos_in_unit = (parseFloat(x1_pos) / ratio) * pxToUnit;
		var y1_pos_in_unit = (parseFloat(y1_pos) / ratio) * pxToUnit;
		var x2_pos_in_unit = (parseFloat(x2_pos) / ratio) * pxToUnit;
		var y2_pos_in_unit = (parseFloat(y2_pos) / ratio) * pxToUnit;
		var width_in_unit  = (parseFloat(width) / ratio) * pxToUnit;
		var height_in_unit = (parseFloat(height) / ratio) * pxToUnit;

		jQuery("#areaX1").val(x1_pos_in_unit.toFixed(0));
		jQuery("#areaY1").val(y1_pos_in_unit.toFixed(0));
		jQuery("#areaX2").val(x2_pos_in_unit.toFixed(0));
		jQuery("#areaY2").val(y2_pos_in_unit.toFixed(0));
		jQuery("#areaWidth").val(width_in_unit.toFixed(0));
		jQuery("#areaHeight").val(height_in_unit.toFixed(0));

		if(rect && !showIt)
		{
			rect.attr({
				x: x1_pos,
				y: y1_pos,
				x2: x2_pos,
				y2: y2_pos,
				width: x2_pos - x1_pos,
				height: y2_pos - y1_pos //(dy - offset.top)
			});
			sizer.attr({
				x: x2_pos,
				y: y2_pos//(dy - offset.top)
			});
		}
	}

	/**
	 * Selects area with given parameters. Used onkeyup event in parameter input fields.
	 *
	 * @param x1
	 * @param y1
	 * @param x2
	 * @param y2
	 */
	/*selectArea = function(x1, y1, x2, y2, width, height) {
		var offset = jQuery("#svgForAreas").offset();

		rect = DrawRectangle(x1, y1, x2, y2);

		rect.attr({
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0,
			rectY: y1,
			rectX: x1,
			rectY2: y2,
			rectX: x2
		});

		sizer = rootSnapSvgObject.rect(x2, y2, 0, 0);

		this.rectW = width;
		this.rectH = height;
		this.rectY = y2;
		this.rectX = x2;

		var sizerX = (x2)-10;
		var sizerY = (y2)-10;

		sizer.attr({
			fill: "#CA202C",
			stroke: "none",
			width: width > 0 ? 10 : 0,
			height: height > 0 ? 10 : 0,
			x: sizerX,
			y: sizerY,
			sizerX: sizerX,
			sizerY: sizerY
		});

		elementsGroup = rootSnapSvgObject.group(rect, sizer);

		elementsGroup.drag();
		rect.hover(gotIn, gotOut);
		rect.drag(onRectMove, onRectStart, onRectEnd);
		sizer.hover(gotInSizer, gotOutSizer);
		sizer.drag(onSizerMove, onSizerStart, onSizerEnd);

		setCoordinatesToValues(x1, y1, x2, y2, x2 - x1, y2 - y1);
	}*/

	/**
	 * Updates selection with entered width.
	 */
	function selectAreaOnWidthKeyUp()
	{
		var width, selection, x2;

		width = jQuery("#areaWidth").val();

		// Convert width to a coordinate.
		width = parseFloat(width) * unitToPx * ratio;

		// Calculate X2 coordinate
		x2 = jQuery("#areaX1").val() + width;

		if (width > 0 && x2 < imageWidth)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['x2'] = x2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Updates selection with entered height.
	 */
	function selectAreaOnHeightKeyUp()
	{
		var height, selection, y2;

		height = jQuery("#areaHeight").val();

		// Convert height to a coordinate.
		height = parseFloat(height) * unitToPx * ratio;
		y2 = jQuery("#areaY1").val() + height;

		if (height > 0 && y2 < imageHeight)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['y2'] = y2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Updates selection with entered X1.
	 */
	function selectAreaOnX1KeyUp()
	{
		var x1, x2, selection;

		x1 = jQuery("#areaX1").val();

		// Convert X1 to pixels coordinate.
		x1 = parseFloat(x1) * unitToPx * ratio;

		// Calculate X2 coordinate.
		x2 = x1 + jQuery("#areaWidth").val();

		if(x1 > 0 && x2 < imageWidth)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['x'] = x1;
				areaBoxes[current_area_id]['x2'] = x2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Updates selection with entered Y1.
	 */
	function selectAreaOnY1KeyUp()
	{
		var y1, y2, selection;

		y1 = jQuery("#areaY1").val();

		// Convert X1 to pixels coordinate.
		y1 = parseFloat(y1) * unitToPx * ratio;

		// Calculate Y2 coordinate.
		y2 = y1 + jQuery("#areaHeight").val();

		if(y1 > 0 && y2 < imageHeight)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['y'] = y1;
				areaBoxes[current_area_id]['y2'] = y2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Updates selection with entered X2.
	 */
	function selectAreaOnX2KeyUp()
	{
		var x2, x1, selection;

		x2 = jQuery("#areaX2").val();

		// Convert X1 to pixels coordinate.
		x2 = parseFloat(x2) * unitToPx * ratio;

		// Calculate X1 coordinate.
		x1 = x2 - jQuery("#areaWidth").val();

		if(x2 < imageWidth && x1 > 0)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['x'] = x1;
				areaBoxes[current_area_id]['x2'] = x2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Updates selection with entered Y2.
	 */
	function selectAreaOnY2KeyUp()
	{
		var y2, y1, selection;

		y2 = jQuery("#areaY2").val();

		// Convert X1 to pixels coordinate.
		y2 = parseFloat(y2) * unitToPx * ratio;

		// Calculate X1 coordinate.
		y1 = y2 - jQuery("#areaHeight").val();

		if(y2 < imageHeight && y1 > 0)
		{
			if (current_area_id > 0)
			{
				areaBoxes[current_area_id]['y'] = y1;
				areaBoxes[current_area_id]['y2'] = y2;
				setCoordinatesToValues(areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y'],
					areaBoxes[current_area_id]['x2'],
					areaBoxes[current_area_id]['y2'],
					areaBoxes[current_area_id]['x2'] - areaBoxes[current_area_id]['x'],
					areaBoxes[current_area_id]['y2'] - areaBoxes[current_area_id]['y']);
			}
		}
	}

	/**
	 * Clears parameter input fields. Used when select area is not displayed anymore.
	 */
	function clearSelectionFields() {
		jQuery("#designAreaId").val("0");
		jQuery("#areaName").val("");
		jQuery("#areaX1").val("");
		jQuery("#areaY1").val("");
		jQuery("#areaX2").val("");
		jQuery("#areaY2").val("");
		jQuery("#areaWidth").val("");
		jQuery("#areaHeight").val("");
	}

	function populateFields(rectangle)
	{
		rectBoundingBox = rectangle.getBBox();
		jQuery("#areaX1").val(Math.round(rectBoundingBox.x));
		jQuery("#areaY1").val(Math.round(rectBoundingBox.y));
		jQuery("#areaX2").val(Math.round(rectBoundingBox.x2));
		jQuery("#areaY2").val(Math.round(rectBoundingBox.y2));
		jQuery("#areaWidth").val(Math.round(rectBoundingBox.width));
		jQuery("#areaHeight").val(Math.round(rectBoundingBox.height));
	}
</script>
