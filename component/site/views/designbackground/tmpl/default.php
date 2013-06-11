<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
?>

<img class="left" id="background"
				     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>"/>

<form>
	<textarea id="BackgroundTxt"></textarea>
	<div id="PreviewTxt"></div>
	<script>
		akeeba.jQuery(document).ready(function(){
			akeeba.jQuery('#BackgroundTxt').keyup(function(){
				akeeba.jQuery('#PreviewTxt').html(akeeba.jQuery('#BackgroundTxt').val());
			});
		});
	</script>
</form>

