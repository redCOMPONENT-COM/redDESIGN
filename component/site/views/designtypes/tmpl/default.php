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
<div class="form-horizontal">
    <?php foreach($this->items as $designtype) :
			if ($this->params->get('navigation_link'))
			{
				/*
					@Todo: update this link when finished details designtype view
				*/
				$link  = JRoute::_('index.php?option=com_reddesign&id=' . $designtype->reddesign_designtype_id);
			}
			else
			{
				$link  = JRoute::_('index.php?option=com_reddesign&id=' . $designtype->reddesign_designtype_id);
			}
	?>
	<div class="control-group">
		<div class="control-label">
			<img id="dsigntype"
		src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/') . $designtype->sample_thumb; ?>" class="img-polaroid"/>
		</div>
		<label class="controls">
			<a href="<?php echo $link ?>">
				<span><strong><?php echo $designtype->title; ?></strong></span>
			</a>
			<br/><?php echo substr($designtype->description, 0, 150); ?>
			<p class="readmore">
				<a href="">Read more: <?php echo $designtype->title; ?></a>
			</p>
		</label>
	</div>
    <?php endforeach; ?>
</div>

