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
	var imageWidth  = parseFloat('<?php echo $this->productionBgAttributes->width; ?>');
	var imageHeight = parseFloat('<?php echo $this->productionBgAttributes->height; ?>');
	var previewWidth  = parseFloat('<?php echo $this->bgBackendPreviewWidth; ?>');

	var ratio = previewWidth / imageWidth;
	var previewHeight = imageHeight * ratio;

	/**
	 * Initiate SVG area selector variables. Basically it is drawing rectangle.
	 */
	var rootSnapSvgObject;
	/*var mouseDownX = 0;
	var mouseDownY = 0;*/

	var mouseDownX = 0;
	var mouseDownY = 0;
	var elemClicked;
	var rect;
	var insideElement = "false";

	/**
	 * Initiate snap.svg
	 */
	jQuery(document).ready(
		function ($) {
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
					}
				);

				// Start, move, and up are the drag functions.
				start = function() {
					// storing original coordinates
					this.ox = this.attr("x");
					this.oy = this.attr("y");
					this.attr({
						opacity: 1
					});

					if (this.attr("y") < 60 && this.attr("x") < 60)
					{
						this.attr({
							fill: "#000"
						});
					}
				};

				move = function(dx, dy) {
					// Move will be called with dx and dy.
					if (this.attr("y") > previewHeight || this.attr("x") > previewWidth)
					{
						this.attr({
							x: this.ox + dx,
							y: this.oy + dy
						});
					}
					else
					{
						nowX = Math.min(previewWidth, this.ox + dx);
						nowY = Math.min(previewHeight, this.oy + dy);
						nowX = Math.max(0, nowX);
						nowY = Math.max(0, nowY);
						this.attr({
							x: nowX,
							y: nowY
						});
						if (this.attr("fill") != "#000") this.attr({
							fill: "#000"
						});
					}
				};

				up = function() {
					// Restoring state.
					this.attr({
						opacity: .5
					});

					if (this.attr("y") < 60 && this.attr("x") < 60)
					{
						this.attr({
							fill: "#AEAEAE"
						});
					}
				};

				gotIn = function() {
					insideElement = "true";
				};

				gotOut = function() {
					insideElement = "false";
				};

				function DrawRectangle(x, y, w, h) {
					var element = rootSnapSvgObject.rect(x, y, w, h);
					element.attr({
						fill: "gray",
						opacity: .5,
						stroke: "#CA202C",
						strokeWidth: 3
					});
					jQuery(element.node).attr('id', 'rct' + x + y);

					//element.drag(move, start, up);
					element.drag();

					element.hover(gotIn, gotOut);

					element.click(function(e) {
						elemClicked = jQuery(element.node).attr('id');
					});

					return element;
				}

				jQuery("#svgForAreas").mousedown(function(e) {
					if (insideElement == "false")
					{
						// Prevent text edit cursor while dragging in webkit browsers
						e.originalEvent.preventDefault();

						var offset = jQuery("#svgForAreas").offset();
						mouseDownX = e.pageX - offset.left;
						mouseDownY = e.pageY - offset.top;

						rect = DrawRectangle(mouseDownX, mouseDownY, 0, 0);

						jQuery("#svgForAreas").mousemove(function(e) {
							var offset = jQuery("#svgForAreas").offset();
							var upX = e.pageX - offset.left;
							var upY = e.pageY - offset.top;

							var width = upX - mouseDownX;
							var height = upY - mouseDownY;

							rect.attr( { "width": width > 0 ? width : 0,
								"height": height > 0 ? height : 0 } );

						});
					}
				});

				jQuery("#svgForAreas").mouseup(function(e) {
					jQuery("#svgForAreas").unbind('mousemove');

					var BBox = rect.getBBox();

					if ( BBox.width == 0 && BBox.height == 0 )
					{
						rect.remove();
					}
				});
			<?php endif; ?>
		}
	);
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

		var productionBackground = <?php echo $this->productionBackground->id; ?>;

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

</script>
