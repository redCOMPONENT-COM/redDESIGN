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
						fill: "transparent",
						stroke: "#CA202C",
						strokeWidth: 3
					});
					jQuery(element.node).attr('id', 'rct' + x + y);

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
</script>
