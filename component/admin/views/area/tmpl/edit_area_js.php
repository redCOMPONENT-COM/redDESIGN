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
	var svgCanvas;
	var svgPath;

	/**
	 * Initiate snap.svg
	 */
	jQuery(document).ready(
		function ($) {
			<?php if (!empty($this->productionBackground->svg_file)) : ?>
				var snapForAreas = Snap("#svgForAreas");

				Snap.load(
					"<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file; ?>", function (f) {
						snapForAreas.append(f);

						jQuery("#svgForAreas").find("svg")
							.attr("width", previewWidth)
							.attr("height", previewHeight)
							.attr("id", "svgCanvas")
							.mousedown(OnMouseDown)
							.mouseup(OnMouseUp);

				});
			<?php endif; ?>
		}
	);

	function OnMouseDown(e){
		var offset = jQuery("#svgCanvas").offset();
		mouseDownX = e.pageX - offset.left;
		mouseDownY = e.pageY - offset.top;
	}

	function OnMouseUp(e){
		var offset = jQuery("#svgCanvas").offset();
		var upX = e.pageX - offset.left;
		var upY = e.pageY - offset.top;

		var width = upX - mouseDownX;
		var height = upY - mouseDownY;

		DrawRectangle(mouseDownX, mouseDownY, width, height);
	}

	function DrawRectangle(x, y, w, h){
		var svgElement = document.getElementById("svgForAreas");
		var paper = Snap(svgElement);
		paper.rect(x, y, w, h).attr({
			fill: "#FFF",
			stroke: "#F00"
		});
	}

	function createSvgElement(tagName)
	{
		return document.createElementNS("http://www.w3.org/2000/svg", tagName);
	}
</script>
