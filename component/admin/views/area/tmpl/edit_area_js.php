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
	var loadedSvg;
	var mouseDownX = 0;
	var mouseDownY = 0;

	/**
	 * Initiate snap.svg
	 */
	jQuery(document).ready(
		function ($) {
			<?php if (!empty($this->productionBackground->svg_file)) : ?>
				var snapForAreas = Snap("#svgForAreas");

				Snap.load(
					"<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file; ?>",
					function (f) {
						//snapForAreas.g().append(f);
						snapForAreas.append(f);

						var svgForAreasLoaded = jQuery("#svgForAreas").find("svg")[0];
						svgForAreasLoaded.setAttribute("width", previewWidth);
						svgForAreasLoaded.setAttribute("height", previewHeight);
						svgForAreasLoaded.setAttribute("id", "svgCanvas");
						//svgLoaded.setAttribute('preserveAspectRatio', 'xMinYMin meet');
						//svgLoaded.setAttribute("viewBox", "0 0 600 450");

						/*g = f.select("g");

						// Register events on document load.
						g.mousedown(OnMouseDown);
						g.mouseup(OnMouseUp);*/
					}
				);

				loadedSvg = Snap("#svgForAreas");
				loadedSvg.mousedown(OnMouseDown);
				loadedSvg.mouseup(OnMouseUp);
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
		var element = loadedSvg.rect(x, y, w, h);
		element.attr({
			fill: "none",
			stroke: "#F00"
		});
	}
</script>
