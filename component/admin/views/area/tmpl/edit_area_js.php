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
	$this->fontsOptions = $displayData->fontsOptions;
	$this->unit = $displayData->unit;
	$this->pxToUnit = $displayData->pxToUnit;
	$this->unitToPx = $displayData->unitToPx;
	$this->ratio = $displayData->ratio;
	$this->imageWidth = $displayData->imageWidth;
	$this->imageHeight = $displayData->imageHeight;
	$this->inputFieldOptions = $displayData->inputFieldOptions;
	$this->params = $displayData->params;
}

$canvasWidth = $this->params->get('max_svg_backend_bg_width', 600);
$canvasHeight = $this->params->get('max_svg_backend_bg_height', 400);
?>

<script type="text/javascript">

	/**
	 * Initiate PX to Unit conversation variables
	 */
	var pxToUnit    = parseFloat('<?php echo $this->pxToUnit;?>');
	var unitToPx    = parseFloat('<?php echo $this->unitToPx;?>');
	var imageWidth  = parseFloat('<?php echo $this->imageWidth; ?>') * unitToPx;
	var imageHeight = parseFloat('<?php echo $this->imageHeight; ?>') * unitToPx;

	/**
	 * Initiate area selector variables.
	 */
	var drawNodes = [];
	var sketchpad = null;
	var start = null;
	var outline = null;
	var offset = null;

	/**
	 * Initiate SVG plugin
	 */
	jQuery(document).ready(
		function ($) {
			<?php if (!empty($this->productionBackground->svg_file)) : ?>
				jQuery("#svgCanvas").svg();
				var svg = jQuery("#svgCanvas").svg("get");
				svg.load(
					"<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file; ?>",
					{
						addTo: false,
						changeSize: true,
						onLoad: loadDone
					}
				);
			<?php endif; ?>
		}
	);

	/**
	 * Function after SVG plugin and SVG file are loaded
	 *
	 */
	function loadDone()
	{
		// Set preview size from the configuration.
		var svg = jQuery("#svgCanvas").svg("get");
		//svg.root().setAttribute("width", "<?php echo $canvasWidth;?>");
		svg.root().setAttribute("height", "<?php echo $canvasHeight;?>");

		sketchpad = svg;
		var surface = svg.rect(0, 0, "100%", "100%", {id: "svgCanvas", fill: "transparent"});
		jQuery(surface).mousedown(startDrag).mousemove(dragging).mouseup(endDrag);
	}

	/* Remember where we started */
	function startDrag(event) {
		offset = (jQuery.browser.msie ? {left: 0, top: 0} : jQuery("#svgCanvas").offset());

		if (!jQuery.browser.msie) {
			offset.left -= document.documentElement.scrollLeft || document.body.scrollLeft;
			offset.top -= document.documentElement.scrollTop || document.body.scrollTop;
		}

		start = {X: event.clientX, Y: event.clientY};
		event.preventDefault();
	}

	/* Provide feedback as we drag */
	function dragging(event) {
		if (!start) {
			return;
		}

		if (!outline) {
			outline = sketchpad.rect(0, 0, 0, 0, {fill: "none", stroke: "#ca202c", strokeWidth: 15, strokeDashArray: "2,2"});
			jQuery(outline).mouseup(endDrag);
		}

		var changeX = Math.min(event.clientX - offset.left, start.X);
		var changeY = Math.min(event.clientY - offset.top, start.Y);
		var width = Math.abs(event.clientX - offset.left - start.X);
		var height = Math.abs(event.clientY - offset.top - start.Y);

		sketchpad.change(outline, {
			x: changeX,
			y: changeY,
			width: width,
			height: height
		});
		event.preventDefault();
	}

	/* Draw where we finish */
	function endDrag(event) {
		if (!start) {
			return;
		}
		jQuery(outline).remove();
		outline = null;
		drawShape(start.X, start.Y, event.clientX - offset.left, event.clientY - offset.top);
		start = null;
		event.preventDefault();
	}

	/* Draw the selected element on the canvas */
	function drawShape(x1, y1, x2, y2) {
		var left = Math.min(x1, x2);
		var top = Math.min(y1, y2);
		var right = Math.max(x1, x2);
		var bottom = Math.max(y1, y2);
		var settings = {fill: "none", stroke: "#ca202c", strokeWidth: 15};
		var node = sketchpad.rect(left, top, right - left, bottom - top, settings);

		drawNodes[drawNodes.length] = node;

		jQuery(node).mousedown(startDrag).mousemove(dragging).mouseup(endDrag);
		jQuery("#svgCanvas").focus();
	}
</script>
