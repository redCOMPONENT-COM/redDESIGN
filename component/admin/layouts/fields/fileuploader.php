<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = (object) $displayData;

/** @var JFormField $field */
$field = $data->field;

RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload.css', 'com_reddesign');

// The jQuery UI widget factory, can be omitted if jQuery UI is already included
RHelperAsset::load('libraries/jquery-fileupload/vendor/jquery.ui.widget.js', 'com_reddesign');

// The Iframe Transport is required for browsers without support for XHR file uploads
RHelperAsset::load('libraries/jquery-fileupload/jquery.iframe-transport.js', 'com_reddesign');

// The basic File Upload plugin
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload.js', 'com_reddesign');

if (isset($field->element['loadFileProcessingLib']) && $field->element['loadFileProcessingLib'] == true)
{
	// The File Upload processing plugin
	RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-process.js', 'com_reddesign');
}

if (isset($field->element['loadPreviewResizeLib']) && $field->element['loadPreviewResizeLib'] == true)
{
	// The File Upload image preview & resize plugin
	RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-image.js', 'com_reddesign');
}

if (isset($field->element['loadValidationLib']) && $field->element['loadValidationLib'] == true)
{
	// The File Upload validation plugin
	RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-validate.js', 'com_reddesign');
}

?>

<span class="btn btn-success fileinput-button">
	<i class="glyphicon glyphicon-plus"></i>
	<span><?php echo JText::_('COM_REDDESIGN_COMMON_SELECT_FILES'); ?></span>
	<input id="<?php echo $field->id ?>" type="file" name="<?php echo $field->name; ?>[]" multiple>
</span>
<br/>
<br/>
<div class="progress progress-striped image-progress">
	<div class="bar bar-success" style="width: 0%"></div>
</div>

<div id="files" class="files"></div>
<script>
	/*jslint unparam: true */
	/*global window, jQuery */
	jQuery(function () {
		"use strict";
		// Change this to the location of your server-side upload handler:
		var url = window.location.hostname;
		jQuery("#<?php echo $field->id ?>").fileupload({
			url: url,
			dataType: "json",
			done: function (e, data) {
				jQuery.each(data.result.files, function (index, file) {
					jQuery("<p/>").text(file.name).appendTo("#files");
				});
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				jQuery("#progress .progress-bar").css(
					"width",
					progress + "%"
				);
			}
		}).prop("disabled", !jQuery.support.fileInput)
			.parent().addClass(jQuery.support.fileInput ? undefined : "disabled");
	});
</script>