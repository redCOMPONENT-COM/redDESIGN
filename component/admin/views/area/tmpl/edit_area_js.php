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

						var svgForAreasLoaded = jQuery("#svgForAreas").find("svg")[0];
						//svgLoaded.setAttribute("viewBox", "0 0 600 450");
						svgForAreasLoaded.setAttribute("width", previewWidth);
						svgForAreasLoaded.setAttribute("height", previewHeight);
						//svgLoaded.setAttribute('preserveAspectRatio', 'xMinYMin meet');
						svgForAreasLoaded.setAttribute("id", "svgCanvas");
				});
			<?php endif; ?>
		}


	);

	function startDrawTouch(event)
	{
		var touch = event.changedTouches[0];
		svgPath =  createSvgElement("rect");
		svgPath.setAttribute("fill", "none");
		svgPath.setAttribute("shape-rendering", "geometricPrecision");
		svgPath.setAttribute("stroke-linejoin", "round");
		svgPath.setAttribute("stroke", "#000000");

		svgPath.setAttribute("d", "M" + touch.clientX  + "," + touch.clientY);
		svgCanvas.appendChild(svgPath);
	}

	function continueDrawTouch(event)
	{
		if (svgPath)
		{
			var touch = event.changedTouches[0];
			var newSegment = svgPath.createSVGPathSegLinetoAbs(touch.clientX, touch.clientY);
			svgPath.pathSegList.appendItem(newSegment);
		}
	}

	function endDrawTouch(event)
	{
		if (svgPath)
		{
			var pathData = svgPath.getAttribute("d");
			var touch = event.changedTouches[0];
			pathData = pathData + " L" + touch.clientX + "," + touch.clientY
			svgPath.setAttribute("d", pathData);
			svgPath = null;
		}
	}

	function createSvgElement(tagName)
	{
		return document.createElementNS("http://www.w3.org/2000/svg", tagName);
	}
</script>
