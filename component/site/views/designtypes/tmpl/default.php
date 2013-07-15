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
    <?php foreach($this->items as $designtype) : ?>
    <div class="control-group">
        <div class="control-label">
           <img id="dsigntype"
				 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/') . $designtype->sample_thumb; ?>" class="img-polaroid"/>
        </div>
	<label class="controls">
           <a href="index.php?option=com_reddesign&id=<?php echo $designtype->reddesign_designtype_id ?>"><strong><?php echo $designtype->title; ?></strong></a> <br/> <?php echo $designtype->description; ?>
        </label>
    </div>
    <?php endforeach; ?>
</div>

