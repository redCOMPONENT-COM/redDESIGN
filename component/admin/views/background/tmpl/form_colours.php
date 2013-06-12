<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

FOFTemplateUtils::addJS('media://com_reddesign/colorpicker/js/bootstrap-colorpicker.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/colorpicker/css/colorpicker.css');

?>

<div class="input-append color" data-color="rgb(255, 146, 180)" data-color-format="rgb">
	<input type="text" class="span2" value="" >
	<span class="add-on"><i style="background-color: rgb(255, 146, 180)"></i></span>
</div>

<?php
/*
The following script loads the color picker. Also adds the akeeba-bootstrap to BODY
Because Akeeba Strapper only loads the namespaced Twitter Bootstrap if you have wrapped
the output you want style with an element having the class akeeba-bootstrap.
see: libraries/strapper/strapper.php => public function bootstrap()
*/
?>
<script>
	akeeba.jQuery(document).ready(function() {
		akeeba.jQuery('body').addClass('akeeba-bootstrap');
		akeeba.jQuery('.color').colorpicker();
	});
</script>