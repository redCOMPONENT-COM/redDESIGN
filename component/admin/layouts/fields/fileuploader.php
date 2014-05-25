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

// Blueimp Gallery styles.
RHelperAsset::load('libraries/jquery-fileupload/blueimp-gallery.min.css', 'com_reddesign');

// CSS to style the file input field as button and adjust the Bootstrap progress bars.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload.css', 'com_reddesign');
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-ui.css', 'com_reddesign');

?>

	<!-- Redirect browsers with JavaScript disabled to the origin page -->
	<noscript>&lt;input type="hidden" name="redirect" value="http://blueimp.github.io/jQuery-File-Upload/"&gt;</noscript>
	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class="row fileupload-buttonbar">
		<div class="col-lg-7">
			<!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple="">
                </span>
			<button type="submit" class="btn btn-primary start">
				<i class="glyphicon glyphicon-upload"></i>
				<span>Start upload</span>
			</button>
			<button type="reset" class="btn btn-warning cancel">
				<i class="glyphicon glyphicon-ban-circle"></i>
				<span>Cancel upload</span>
			</button>
			<button type="button" class="btn btn-danger delete">
				<i class="glyphicon glyphicon-trash"></i>
				<span>Delete</span>
			</button>
			<input type="checkbox" class="toggle">
			<!-- The global file processing state -->
			<span class="fileupload-process"></span>
		</div>
		<!-- The global progress state -->
		<div class="col-lg-5 fileupload-progress fade">
			<!-- The global progress bar -->
			<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="progress-bar progress-bar-success" style="width:0%;"></div>
			</div>
			<!-- The extended global progress state -->
			<div class="progress-extended">&nbsp;</div>
		</div>
	</div>
	<!-- The table listing the files available for upload/download -->
	<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
		<div class="slides"></div>
		<h3 class="title"></h3>
		<a class="prev">‹</a>
		<a class="next">›</a>
		<a class="close">×</a>
		<a class="play-pause"></a>
		<ol class="indicator"></ol>
	</div>


<?php
/*
// The jQuery UI widget factory, can be omitted if jQuery UI is already included.
RHelperAsset::load('libraries/jquery-fileupload/jquery.ui.widget.js', 'com_reddesign');

// The Templates plugin is included to render the upload/download listings.
RHelperAsset::load('libraries/jquery-fileupload/tmpl.min.js', 'com_reddesign');

// The Load Image plugin is included for the preview images and image resizing functionality.
RHelperAsset::load('libraries/jquery-fileupload/load-image.min.js', 'com_reddesign');

// The Canvas to Blob plugin is included for image resizing functionality.
RHelperAsset::load('libraries/jquery-fileupload/canvas-to-blob.min.js', 'com_reddesign');

// Blueimp Gallery script.
RHelperAsset::load('libraries/jquery-fileupload/jquery.blueimp-gallery.min.js', 'com_reddesign');

// The Iframe Transport is required for browsers without support for XHR file uploads.
RHelperAsset::load('libraries/jquery-fileupload/jquery.iframe-transport.js', 'com_reddesign');

// The basic File Upload plugin.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload.js', 'com_reddesign');

// The File Upload processing plugin.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-process.js', 'com_reddesign');

// The File Upload image preview & resize plugin.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-image.js', 'com_reddesign');

// The File Upload validation plugin.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-validate.js', 'com_reddesign');

// The File Upload user interface plugin.
RHelperAsset::load('libraries/jquery-fileupload/jquery.fileupload-ui.js', 'com_reddesign');

// The main application script.
RHelperAsset::load('libraries/jquery-fileupload/main.js', 'com_reddesign');*/