<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

FOFTemplateUtils::addJS('media://com_reddesign/jpicker/jpicker-1.1.6.min.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/jpicker/css/jPicker-1.1.6.css');
FOFTemplateUtils::addCSS('media:///com_reddesign/jpicker/jPicker.css');

?>

<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function()
		{
			akeeba.jQuery('.Multiple').jPicker.defaults.images.clientPath='<?php echo JURI::root() ?>media/com_reddesign/jpicker/images/';
			akeeba.jQuery('.Multiple').jPicker();
		});
	function addColor(){
		akeeba.jQuery('#addcolorpicker').before('<p><input class="Multiple" name="fontcolors[]" type="text" value="000000" /><a class="btn btn-danger" onclick="javascript:removeColor(this)"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_COLOURS_REMOVE_COLOR'); ?></a><br /></p>');
		akeeba.jQuery('.Multiple').last().jPicker();
	}
	function removeColor(elem){
		$toremove = $(elem);
		$toremove.parentNode.remove();
	}
</script>
	<p>
		<input class="Multiple" name="fontcolors[]" type="text" value="000000" /><br />
	</p>
	<p id="addcolorpicker">
		<a class="btn" onclick="javascript:addColor()">
			<?php echo JText::_('COM_REDDESIGN_BACKGROUND_COLOURS_ADD_COLOR'); ?>
		</a>
	</p>
</div>