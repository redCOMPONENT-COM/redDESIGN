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
	$this->unitConversionRatio = $displayData->unitConversionRatio;
	$this->sourceDpi = $displayData->sourceDpi;
	$this->productionBgAttributes = $displayData->productionBgAttributes;
	$this->fontsOptions = $displayData->fontsOptions;
	$this->inputFieldOptions = $displayData->inputFieldOptions;
	$this->selectedFontsDeclaration = $displayData->selectedFontsDeclaration;
}

$return_url = JURI::base() . 'index.php?option=com_reddesign&view=designtype&layout=edit&id=' . $this->item->designtype_id . '&tab=design-areas';

?>

<script type="text/javascript">

	/**
	 * Initiate PX to Unit conversation variables
	 */
	var unit = "<?php echo $this->unit;?>";
	var imageWidth  = parseFloat("<?php echo (!empty($this->productionBgAttributes->width) ? $this->productionBgAttributes->width : ''); ?>");
	var imageHeight = parseFloat("<?php echo (!empty($this->productionBgAttributes->height) ? $this->productionBgAttributes->height : ''); ?>");
	var previewWidth  = parseFloat("<?php echo (!empty($this->bgBackendPreviewWidth) ? $this->bgBackendPreviewWidth : ''); ?>");

	var unitConversionRatio = parseFloat("<?php echo $this->unitConversionRatio;?>");
	var scalingImageForPreviewRatio = previewWidth / imageWidth;
	var previewHeight = imageHeight * scalingImageForPreviewRatio;

	/**
	 * Initiate SVG area selector variables. Basically it is drawing rectangle.
	 */
	var rootSnapSvgObject;
	var mouseDownX = 0;
	var mouseDownY = 0;
	var insideGroup = "false";
	var current_area_id = '';
	var areaBoxes = new Array();

	var lx = 0,
		ly = 0,
		ox = 0,
		oy = 0;

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


			jQuery("#selectorControls .area-textbox-control").each(function (idx, ele) {
				jQuery(ele).keyup(function () {
					onAreaValuesChange(ele);
				});
			});

			<?php if (!empty($this->productionBackground->svg_file)) : ?>
				rootSnapSvgObject = Snap("#svgForAreas");
			jQuery.ajax({
				url: "<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file; ?>",
				dataType: "text",
				xhrFields: {
					onprogress: function (e) {
						if (e.lengthComputable) {
							var loadedPercentage = e.loaded / e.total * 100;
							$('#backgroundImageContainer .progress .bar-success')
								.css('width', '' + (loadedPercentage) + '%')
								.html(loadedPercentage + '% <?php echo JText::_('COM_REDDESIGN_COMMON_PROGRESS_LOADED', true); ?>');
						}
					}
				},
				beforeSend: function (xhr) {
					jQuery('#backgroundImageContainer .progress').show().addClass('active');
					jQuery('#backgroundImageContainer .progress .bar-success').css('width', '0%');
				},
				success: function (response) {
					jQuery('#backgroundImageContainer .progress').removeClass('active');
					if(typeof response === 'undefined' || response == false){
						jQuery('#backgroundImageContainer .progress').append('<div class="bar bar-danger" style="width: ' + (100 - parseInt(jQuery('#backgroundImageContainer .progress .bar-success').css('width'))) + '%;"></div>');
					}
					else{
						jQuery('#backgroundImageContainer .progressbar-holder').fadeOut(3000);
					}
					var styleDeclaration = Snap.parse('<defs><style type="text/css"><?php echo $this->selectedFontsDeclaration; ?></style></defs>');
					rootSnapSvgObject.append(styleDeclaration);
					rootSnapSvgObject.append(Snap.parse(response));

					var loadedSvgFromFile = jQuery("#svgForAreas").find("svg")[0];
					loadedSvgFromFile.setAttribute("width", previewWidth);
					loadedSvgFromFile.setAttribute("height", previewHeight);
					loadedSvgFromFile.setAttribute("id", "svgCanvas");

					var rootElement = document.getElementById("svgForAreas");
					rootElement.setAttribute("width", previewWidth);
					rootElement.setAttribute("height", previewHeight);
					rootElement.setAttribute("overflow", "hidden");

					rootSnapSvgObject.group().node.id = "areaBoxesLayer";

					rootSnapSvgObject.mousedown(begginDrawRectangle);
					rootSnapSvgObject.mouseup(endDrawRectangle);
				}
			});
			<?php endif; ?>
		}
	);

	function begginDrawRectangle(e)
	{
		if (insideGroup == "false")
		{
			// Remove previous selections.
			rootSnapSvgObject.select("#areaBoxesLayer").remove();

			var offset = jQuery("#svgForAreas").offset();
			mouseDownX = (e.pageX - offset.left);
			mouseDownY = (e.pageY - offset.top);

			rootSnapSvgObject.mousemove(drawingRectangle);

			var currentRectangle = drawRectangle(mouseDownX, mouseDownY, 0, 0, "white", 0.7, "#CA202C", 3);
			currentRectangle.hover(rectangleIn, rectangleOut);

			current_area_id = currentRectangle.id;
			areaBoxes[current_area_id] = new Array();
			areaBoxes[current_area_id]['x'] = mouseDownX;
			areaBoxes[current_area_id]['y'] = mouseDownY;
			areaBoxes[current_area_id]['width'] = 0;
			areaBoxes[current_area_id]['height'] = 0;
			areaBoxes[current_area_id]['rect'] = currentRectangle;

			areaBoxes[current_area_id]['rect'].node.id = areaBoxes[current_area_id]['rect'].id;
			areaBoxes[current_area_id]['rectId'] = areaBoxes[current_area_id]['rect'].node.id;

			areaBoxes[current_area_id]['sizer'] = drawRectangle(mouseDownX, mouseDownY, 10, 10, "#CA202C", 1, "none", 0);
			areaBoxes[current_area_id]['sizer'].node.id = "sizer" + areaBoxes[current_area_id]['rectId'];
			areaBoxes[current_area_id]['sizer'].hover(sizerIn, sizerOut);
			areaBoxes[current_area_id]['sizer'].mousedown(begginResizeRectangle);

			var group = rootSnapSvgObject.group(areaBoxes[current_area_id]['sizer'], areaBoxes[current_area_id]['rect']);
			group.node.id = "group" + areaBoxes[current_area_id]['rectId'];
			group.hover(groupIn, groupOut);

			rootSnapSvgObject.group().node.id = "areaBoxesLayer";
			rootSnapSvgObject.select("#areaBoxesLayer").append(group);
		}
	}

	function drawingRectangle(e)
	{
		var offset = jQuery("#svgForAreas").offset();
		var upX = e.pageX - offset.left;
		var upY = e.pageY - offset.top;

		var width = upX - mouseDownX;
		var height = upY - mouseDownY;

		var movingRect = areaBoxes[current_area_id]['rect'];
		movingRect.attr({
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0
		});

		var sizerX = mouseDownX + width;
		var sizerY = mouseDownY + height;
		var sizer = areaBoxes[current_area_id]['sizer'];
		sizer.attr({
			width: width > 0 ? 10 : 0,
			height: height > 0 ? 10 : 0,
			x: sizerX,
			y: sizerY
		});

		areaBoxes[current_area_id]['x2'] = areaBoxes[current_area_id]['x'] + width;
		areaBoxes[current_area_id]['y2'] = areaBoxes[current_area_id]['y'] + height;
		areaBoxes[current_area_id]['width'] = width;
		areaBoxes[current_area_id]['height'] = height;
	}

	function endDrawRectangle(e)
	{
		rootSnapSvgObject.unmousemove();

		var movingRect = areaBoxes[current_area_id]['rect'];

		populateFieldsWithCoordinatesFromImage(
			movingRect.attr('x'),
			movingRect.attr('y'),
			parseFloat(movingRect.attr('x')) + parseFloat(movingRect.attr('width')),
			parseFloat(movingRect.attr('y')) + parseFloat(movingRect.attr('height')),
			movingRect.attr('width'),
			movingRect.attr('height')
		);
	}

	function drawRectangle(x, y, w, h, fill, opacity, stroke, strokeWidth)
	{
		var rectangle = rootSnapSvgObject.rect(x, y, w, h);

		rectangle.attr({
			fill: fill,
			opacity: opacity,
			stroke: stroke,
			strokeWidth: strokeWidth
		});

		return rectangle;
	}

	function groupIn()
	{
		insideGroup = "true";

	}

	function groupOut()
	{
		insideGroup = "false";
	}

	function sizerIn()
	{
		this.attr({cursor: "crosshair"});
		areaBoxes[current_area_id]['rectId'] = this.node.id.replace("sizer", "");
	}

	function sizerOut()
	{
		this.attr({cursor: "auto"});
	}

	function rectangleIn()
	{
		this.attr({cursor: "move"});

		var group = this.parent();
		group.drag(draggingGroup, startDraggingGroup, endDraggingGroup);
	}

	function rectangleOut()
	{
		this.attr({cursor: "auto"});

		var group = this.parent();
		group.undrag();
	}

	function begginResizeRectangle(e)
	{
		rootSnapSvgObject.mousemove(resizeRectangle);
		rootSnapSvgObject.mouseup(endResizingRectangle);
	}

	function resizeRectangle(e)
	{
		var offset = jQuery("#areaBoxesLayer").offset();
		var upX = e.pageX - offset.left;
		var upY = e.pageY - offset.top;

		var width = upX - mouseDownX;
		var height = upY - mouseDownY;

		var movingRect = areaBoxes[current_area_id]['rect'];
		movingRect.attr({
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0
		});

		var sizer = areaBoxes[current_area_id]['sizer'];
		var sizerX = areaBoxes[current_area_id]['x'] + width;
		var sizerY = areaBoxes[current_area_id]['y'] + height;
		sizer.attr({
			width: width > 0 ? 10 : 0,
			height: height > 0 ? 10 : 0,
			x: sizerX,
			y: sizerY
		});

		areaBoxes[current_area_id]['x2'] = areaBoxes[current_area_id]['x'] + width;
		areaBoxes[current_area_id]['y2'] = areaBoxes[current_area_id]['y'] + height;
		areaBoxes[current_area_id]['width'] = width;
		areaBoxes[current_area_id]['height'] = height;
	}

	function endResizingRectangle()
	{
		rootSnapSvgObject.unmousemove();
		var movingRect = areaBoxes[current_area_id]['rect'];

		populateFieldsWithCoordinatesFromImage(
			movingRect.attr("x"),
			movingRect.attr("y"),
			parseFloat(movingRect.attr("x")) + parseFloat(movingRect.attr("width")),
			parseFloat(movingRect.attr("y")) + parseFloat(movingRect.attr("height")),
			movingRect.attr("width"),
			movingRect.attr("height")
		);
	}

	function startDraggingGroup(x, y, e) {

	}

	function draggingGroup(dx, dy, x, y) {
		lx = dx + ox;
		ly = dy + oy;
		this.transform('t' + lx + ',' + ly);
	}

	function endDraggingGroup(e) {
		ox = lx;
		oy = ly;

		updateFieldsAfterDrag(this);
	}

	/**
	 * We have to use translated axises from the group's matrix in order to calculate
	 * new position of the rectangle moved. The rectangle is part of the group.
	 * Group's matrix is consisted from [a b c d e f], e and f gives the translated axis in
	 * the x and y respectively. Which means that new position of an element inside group can be
	 * determined like: x + e and y + f.
	 *
	 * @param   group  The group which was dragged.
	 */
	function updateFieldsAfterDrag(group)
	{
		var movingRect = rootSnapSvgObject.select("#" + areaBoxes[current_area_id]['rectId']);

		var x1 = parseFloat(movingRect.attr("x")) + group.matrix.e;
		var y1 = parseFloat(movingRect.attr("y")) + group.matrix.f;
		var x2 = x1 + parseFloat(movingRect.attr("width"));
		var y2 = y1 + parseFloat(movingRect.attr("height"));

		populateFieldsWithCoordinatesFromImage(x1, y1, x2, y2, movingRect.attr("width"), movingRect.attr("height"));
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
		var areaName   = jQuery("#areaName").val();
		var areaX1     = (jQuery("#areaX1").val()).replace(unit, "");
		var areaY1     = (jQuery("#areaY1").val()).replace(unit, "");
		var areaX2     = (jQuery("#areaX2").val()).replace(unit, "");
		var areaY2     = (jQuery("#areaY2").val()).replace(unit, "");
		var areaWidth  = (jQuery("#areaWidth").val()).replace(unit, "");
		var areaHeight = (jQuery("#areaHeight").val()).replace(unit, "");

		var areaX1_in_px     = areaX1 * unitConversionRatio;
		var areaY1_in_px     = areaY1 * unitConversionRatio;
		var areaX2_in_px     = areaX2 * unitConversionRatio;
		var areaY2_in_px     = areaY2 * unitConversionRatio;
		var areaWidth_in_px  = areaWidth * unitConversionRatio;
		var areaHeight_in_px = areaHeight * unitConversionRatio;

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
						clearSelectionFields();
					}

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
	function addColorToList(areaId) {
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

	function onAreaValuesChange(obj) {
		var fieldValue = jQuery(obj).val();
		fieldValue = parseFloat(fieldValue.replace(unit, ""));
		fieldValue = fieldValue * unitConversionRatio * scalingImageForPreviewRatio;

		var x1 = jQuery("#areaX1").val();
		x1 = parseFloat(x1.replace(unit, ""));
		x1 = x1 * unitConversionRatio * scalingImageForPreviewRatio;

		var x2 = jQuery("#areaX2").val();
		x2 = parseFloat(x2.replace(unit, ""));
		x2 = x2 * unitConversionRatio * scalingImageForPreviewRatio;

		var y1 = jQuery("#areaY1").val();
		y1 = parseFloat(y1.replace(unit, ""));
		y1 = y1 * unitConversionRatio * scalingImageForPreviewRatio;

		var y2 = jQuery("#areaY2").val();
		y2 = parseFloat(y2.replace(unit, ""));
		y2 = y2 * unitConversionRatio * scalingImageForPreviewRatio;

		var width = jQuery("#areaWidth").val();
		width = parseFloat(width.replace(unit, ""));
		width = width * unitConversionRatio * scalingImageForPreviewRatio;

		var height = jQuery("#areaHeight").val();
		height = parseFloat(height.replace(unit, ""));
		height = height * unitConversionRatio * scalingImageForPreviewRatio;

		// If canvas is empty draw new rectangle.
		if(current_area_id == '')
		{
			x1 = 1; y1 = 1; height = 25; width = 25; x2 = x1 + width; y2 = y1 + height;
			// Set other coordinates.
			switch(jQuery(obj).prop('name'))
			{
				case 'areaWidth':
					width = fieldValue;
					x2 = x1 + width;
					break;
				case 'areaHeight':
					height = fieldValue;
					y2 = y1 + height;
					break;

				case 'areaX1':
					x1 = fieldValue;
					x2 = x1 + width;
					break;
				case 'areaY1':
					y1 = fieldValue;
					y2 = y1 + height;
					break;
				case 'areaX2':
					x2 = fieldValue;
					width = x2 - x1;
					break;
				case 'areaY2':
					y2 = fieldValue;
					height = y2 - y1;
					break;
			}

			var currentRectangle = drawRectangle(x1, y1, width, height, "white", 0.7, "#CA202C", 3);
			currentRectangle.hover(rectangleIn, rectangleOut);

			current_area_id = currentRectangle.id;
			areaBoxes[current_area_id] = new Array();
			areaBoxes[current_area_id]['x'] = x1;
			areaBoxes[current_area_id]['y'] = y1;
			areaBoxes[current_area_id]['x2'] = x2;
			areaBoxes[current_area_id]['y2'] = y2;
			areaBoxes[current_area_id]['width'] = width;
			areaBoxes[current_area_id]['height'] = height;
			areaBoxes[current_area_id]['rect'] = currentRectangle;

			areaBoxes[current_area_id]['rect'].node.id = areaBoxes[current_area_id]['rect'].id;
			areaBoxes[current_area_id]['rectId'] = areaBoxes[current_area_id]['rect'].node.id;

			areaBoxes[current_area_id]['sizer'] = drawRectangle(x2, y2, 10, 10, "#CA202C", 1, "none", 0);
			areaBoxes[current_area_id]['sizer'].node.id = "sizer" + areaBoxes[current_area_id]['rectId'];
			areaBoxes[current_area_id]['sizer'].hover(sizerIn, sizerOut);
			areaBoxes[current_area_id]['sizer'].mousedown(begginResizeRectangle);

			var group = rootSnapSvgObject.group(areaBoxes[current_area_id]['sizer'], areaBoxes[current_area_id]['rect']);
			group.node.id = "group" + areaBoxes[current_area_id]['rectId'];
			group.hover(groupIn, groupOut);

			rootSnapSvgObject.group().node.id = "areaBoxesLayer";
			rootSnapSvgObject.select("#areaBoxesLayer").append(group);
		}
		else
		{
			switch(jQuery(obj).prop('name'))
			{
				case 'areaWidth':
					width = fieldValue;
					x2 = x1 + fieldValue;
					break;
				case 'areaHeight':
					height = fieldValue;
					y2 = y1 + fieldValue;
					break;
				case 'areaX1':
					x1 = fieldValue;
					x2 = width + fieldValue;
					break;
				case 'areaY1':
					y1 = fieldValue;
					y2 = height + fieldValue;
					break;
				case 'areaX2':
					x2 = fieldValue;
					x1 = fieldValue - width;
					break;
				case 'areaY2':
					y2 = fieldValue;
					y1 = fieldValue - height;
					break;
			}

			areaBoxes[current_area_id]['x'] = x1;
			areaBoxes[current_area_id]['y'] = y1;
			areaBoxes[current_area_id]['x2'] = x2;
			areaBoxes[current_area_id]['y2'] = y2;
			areaBoxes[current_area_id]['width'] = width;
			areaBoxes[current_area_id]['height'] = height;
		}

		setPositionToCurrentRectangle();

		switch(jQuery(obj).prop('name'))
		{
			case 'areaWidth':
				width = Number.NaN;
				break;
			case 'areaHeight':
				height = Number.NaN;
				break;
			case 'areaX1':
				x1 = Number.NaN;
				break;
			case 'areaY1':
				y1 = Number.NaN;
				break;
			case 'areaX2':
				x2 = Number.NaN;
				break;
			case 'areaY2':
				y2 = Number.NaN;
				break;
		}

		populateFieldsWithCoordinatesFromImage(x1, y1, x2, y2, width, height);
	}

	function setPositionToCurrentRectangle() {
		var x = areaBoxes[current_area_id]['x'];
		var y = areaBoxes[current_area_id]['y'];
		var width = areaBoxes[current_area_id]['width'];
		var height = areaBoxes[current_area_id]['height'];

		x = isNaN(x) ? 0 : x;
		y = isNaN(y) ? 0 : y;
		width = isNaN(width) ? 25 : width;
		height = isNaN(height) ? 25 : height;

		var movingRect = areaBoxes[current_area_id]['rect'];
		movingRect.attr({
			x: x > 0 ? x : 0,
			y: y > 0 ? y : 0,
			width: width > 0 ? width : 0,
			height: height > 0 ? height : 0
		});

		var sizer = areaBoxes[current_area_id]['sizer'];
		sizer.attr({
			x: x + width,
			y: y + height
		});
	}

	/**
	 * Removes color from the list and from the database.
	 *
	 * @param areaId int Area ID.
	 * @param colorToRemove string Hexadecimal code of the color to be removed.
	 */
	function removeColorFromList(areaId, colorToRemove) {
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
	function getNewHexColor(areaId) {
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

		if (typeof areaBoxes[reddesign_area_id] !== "undefined")
		{
			current_area_id = reddesign_area_id;
		}
		else
		{
			areaBoxes[reddesign_area_id] = new Array();
			areaBoxes[reddesign_area_id]['title'] = title;
			areaBoxes[reddesign_area_id]['x'] = x1_pos;
			areaBoxes[reddesign_area_id]['x2'] = x2_pos;
			areaBoxes[reddesign_area_id]['y'] = y1_pos;
			areaBoxes[reddesign_area_id]['y2'] = y2_pos;
			areaBoxes[reddesign_area_id]['width'] = width;
			areaBoxes[reddesign_area_id]['height'] = height;
			current_area_id = reddesign_area_id;
		}
		selectArea(x1_pos, y1_pos, x2_pos, y2_pos, width, height);
		populateFieldsWithCoordinatesFromAreasList(x1_pos, y1_pos, x2_pos, y2_pos, width, height);

		var textElement = Snap.parse(
			'<text fill="black" font-size="14px" x="'
				+ (parseFloat(areaBoxes[current_area_id]['rect'].attr('x')) + 4)
				+ '" y="' + (parseFloat(areaBoxes[current_area_id]['rect'].attr('y')) + 18)
				+ '">' + title + '</text>');

		areaBoxes[current_area_id]['group'].append(textElement);

		jQuery("#areaDiv" + reddesign_area_id).html(title + '<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_EDITING_AREA'); ?>');
	}

	/**
	 * Populates input fields with coordinates from drawing on the SVG image.
	 */
	function populateFieldsWithCoordinatesFromImage(x1_pos, y1_pos, x2_pos, y2_pos, width, height)
	{
		// Convert pixel to selected unit. Use scalingImageForPreviewRatio to calculate and display real measures instead of scaled down.

		if (!isNaN(x1_pos))
		{
			x1_pos = (parseFloat(x1_pos) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaX1").val(x1_pos.toFixed(2) + unit);
		}

		if (!isNaN(y1_pos))
		{
			y1_pos = (parseFloat(y1_pos) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaY1").val(y1_pos.toFixed(2) + unit);
		}

		if (!isNaN(x2_pos))
		{
			x2_pos = (parseFloat(x2_pos) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaX2").val(x2_pos.toFixed(2) + unit);
		}

		if (!isNaN(y2_pos))
		{
			y2_pos = (parseFloat(y2_pos) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaY2").val(y2_pos.toFixed(2) + unit);
		}

		if (!isNaN(width))
		{
			width = (parseFloat(width) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaWidth").val(width.toFixed(2) + unit);
		}

		if (!isNaN(height))
		{
			height = (parseFloat(height) / scalingImageForPreviewRatio) / unitConversionRatio;
			jQuery("#areaHeight").val(height.toFixed(2) + unit);
		}
	}

	/**
	 * Populates input fields from the areas list.
	 */
	function populateFieldsWithCoordinatesFromAreasList(x1_pos, y1_pos, x2_pos, y2_pos, width, height)
	{
		// Convert pixel to selected unit. Use scalingImageForPreviewRatio to calculate and display real mertics instead of scaled down.
		var x1_pos_in_unit = parseFloat(x1_pos) / unitConversionRatio;
		var y1_pos_in_unit = parseFloat(y1_pos) / unitConversionRatio;
		var x2_pos_in_unit = parseFloat(x2_pos) / unitConversionRatio;
		var y2_pos_in_unit = parseFloat(y2_pos) / unitConversionRatio;
		var width_in_unit  = parseFloat(width) / unitConversionRatio;
		var height_in_unit = parseFloat(height) / unitConversionRatio;

		jQuery("#areaX1").val(x1_pos_in_unit.toFixed(2) + unit);
		jQuery("#areaY1").val(y1_pos_in_unit.toFixed(2) + unit);
		jQuery("#areaX2").val(x2_pos_in_unit.toFixed(2) + unit);
		jQuery("#areaY2").val(y2_pos_in_unit.toFixed(2) + unit);
		jQuery("#areaWidth").val(width_in_unit.toFixed(2) + unit);
		jQuery("#areaHeight").val(height_in_unit.toFixed(2) + unit);
	}

	/**
	 * Selects area with given parameters. Used onkeyup event in parameter input fields.
	 *
	 * @param x1
	 * @param y1
	 * @param x2
	 * @param y2
	 * @param width
	 * @param height
	 */
	function selectArea(x1, y1, x2, y2, width, height) {
		// Remove previous selections.
		rootSnapSvgObject.select("#areaBoxesLayer").remove();

		x1 *= scalingImageForPreviewRatio;
		y1 *= scalingImageForPreviewRatio;
		x2 *= scalingImageForPreviewRatio;
		y2 *= scalingImageForPreviewRatio;
		width *= scalingImageForPreviewRatio;
		height *= scalingImageForPreviewRatio;

		var currentRectangle = drawRectangle(x1, y1, width, height, "white", 0.7, "#CA202C", 3);
		currentRectangle.id = "rect" + current_area_id;
		currentRectangle.hover(rectangleIn, rectangleOut);

		areaBoxes[current_area_id] = new Array();
		areaBoxes[current_area_id]['x'] = x1;
		areaBoxes[current_area_id]['y'] = y1;
		areaBoxes[current_area_id]['x2'] = x2;
		areaBoxes[current_area_id]['y2'] = y2;
		areaBoxes[current_area_id]['width'] = width;
		areaBoxes[current_area_id]['height'] = height;
		areaBoxes[current_area_id]['rect'] = currentRectangle;

		areaBoxes[current_area_id]['rect'].node.id = "area" + areaBoxes[current_area_id]['rect'].id;
		areaBoxes[current_area_id]['rectId'] = areaBoxes[current_area_id]['rect'].node.id;

		areaBoxes[current_area_id]['sizer'] = drawRectangle(x2, y2, 10, 10, "#CA202C", 1, "none", 0);
		areaBoxes[current_area_id]['sizer'].node.id = "sizer" + areaBoxes[current_area_id]['rectId'];
		areaBoxes[current_area_id]['sizer'].hover(sizerIn, sizerOut);
		areaBoxes[current_area_id]['sizer'].mousedown(begginResizeRectangle);

		var group = rootSnapSvgObject.group(areaBoxes[current_area_id]['sizer'], areaBoxes[current_area_id]['rect']);
		group.node.id = "group" + areaBoxes[current_area_id]['rectId'];
		group.hover(groupIn, groupOut);
		areaBoxes[current_area_id]['group'] = group;

		rootSnapSvgObject.group().node.id = "areaBoxesLayer";
		rootSnapSvgObject.select("#areaBoxesLayer").append(group);
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

	/**
	 * Controls what needs to be shown regarding to input field type.
	 *
	 * @param areaId
	 */
	function changeInputFieldType(areaId)
	{
		var selectedType = jQuery("#inputFieldType" + areaId).val();

		if (selectedType == 1)
		{
			jQuery("#maximumLinesAllowedContainer" + areaId).css("display", "inline");
		}
		else
		{
			jQuery("#maximumLinesAllowedContainer" + areaId).css("display", "none");
		}
	}
</script>
