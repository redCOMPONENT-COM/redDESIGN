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

$extension = str_replace(array('jpg', 'svg'), array('jpeg', 'svg+xml'), JFile::getExt($file['mangled_filename']));

if ($file["mime_type"] == 'application/postscript')
{
	$file["mime_type"] = 'image/' . $extension;
}

if (empty($file) || empty($file["mime_type"]) || empty($file['original_filename']))
{
	die(JText::_('COM_REDDESIGN_ERROR_UPLOADING_IMAGE_IN_FRONTEND'));
}
?>
<div class="row-fluid">
	<div class="span12 col-md12">
		<div class="thumbnail clipart-container">
			<div
				class="thumbnailSVG-pointer"
				name="clipart<?php echo $areaId; ?>"
				style="width:<?php echo $clipartPreviewWidth ?>px; height:<?php echo $clipartPreviewHeight; ?>px;"></div>
			<object
				id="clipartUpload<?php echo $areaId;?>_0"
				name="clipart<?php echo $areaId;?>_0"
				class="thumbnailSVG"
				data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/uploaded/' . $file['mangled_filename']; ?>"
				cliparttype="<?php echo $extension; ?>"
				type="<?php echo $file["mime_type"]; ?>"
				width="<?php echo $clipartPreviewWidth ?>px"
				max-height="<?php echo $clipartPreviewHeight; ?>px">
			</object>
			<input
				type="hidden"
				class="change-selected-clipart"
				name="selectedClipart<?php echo $areaId ?>"
				value="0"
				/><br />
			<?php echo (string) $file['original_filename'];?>
		</div>

		<div class="clearfix"></div>
	</div>
</div>

<script type="text/javascript">
	jQuery('#clipartUpload<?php echo $areaId;?>_0').each(function () {
		var svgThumbnail = document.getElementById(jQuery(this).attr('id'));
		svgThumbnail.addEventListener("load", function() {
			setSVGElementScale(this);
		});

		// Some elements are already loaded
		setSVGElementScale(svgThumbnail);
	});
</script>
