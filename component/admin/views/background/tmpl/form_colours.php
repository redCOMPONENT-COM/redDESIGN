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
