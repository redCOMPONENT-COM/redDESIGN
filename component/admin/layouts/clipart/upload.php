<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = $displayData;
$areaId = $data['areaId'];
$file = $data['file'];
$config = ReddesignEntityConfig::getInstance();
$clipartPreviewWidth = $config->getMaxClipartPreviewWidth();
$clipartPreviewHeight = $config->getMaxClipartPreviewHeight();
?>
<div class="row-fluid">
	<div class="span12">
		<div class="thumbnail clipart-container">
			<div
				class="thumbnailSVG-pointer"
				name="clipart<?php echo $areaId; ?>"
				style="width:<?php echo $clipartPreviewWidth; ?>px; height:<?php echo $clipartPreviewHeight; ?>px;"></div>
			<object
				id="clipartUpload<?php echo $areaId ;?>_0"
				name="clipart<?php echo $areaId ;?>_0"
				class="thumbnailSVG"
				data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/uploaded/' . $file['mangled_filename']; ?>"
				type="image/svg+xml">
			</object>
			<input
				type="hidden"
				class="change-selected-clipart hide"
				name="selectedClipart<?php echo $areaId ?>"
				value="0"
				/><br />
			<?php echo (string) $file['original_filename'] ;?>
		</div>

		<div class="clearfix"></div>
	</div>
</div>
<script type="text/javascript">
	jQuery('#clipartUpload<?php echo $areaId ;?>_0').each(function () {
		var svgThumbnail = document.getElementById(jQuery(this).attr('id'));
		svgThumbnail.addEventListener("load", function() {
			setSVGElementScale(this);
		});
		// Some elements are already loaded
		setSVGElementScale(svgThumbnail);
	});
</script>
