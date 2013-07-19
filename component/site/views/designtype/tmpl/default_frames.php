<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<?php foreach($this->previewBackgrounds as $frame) : ?>
<div class="row-fluid top-buffer">
	<div class="span3">
		<a href="#">
			<img
				class="img-polaroid"
				src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/thumbnails/') . $frame->thumbnail; ?>"/>
		</a>
	</div>
	<div class="span8 offset1">
		<a href="#"><?php echo $frame->title ?></a>
	</div>
</div>
<?php endforeach; ?>
