<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

if (isset($displayData))
{
	$this->item = $displayData->item;
	$this->backgrounds = $displayData->backgrounds;
	$this->relatedDesignTypeIds = $displayData->relatedDesignTypeIds;
	$this->productionBackground = $displayData->productionBackground;
	$this->attributesBackgrounds = $displayData->attributesBackgrounds;
}

$background_id = null;

$input = JFactory::getApplication()->input;

?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="frames" class="thumbnails">

		<?php foreach($this->attributesBackgrounds as $background) : ?>

		<?php endforeach; ?>

		<input type="hidden"
			   name="background_id"
			   id="background_id"
			   value="<?php echo $background_id;?>"
			/>

		<input type="hidden"
			   name="production_background_id"
			   id="production_background_id"
			   value="<?php echo $this->productionBackground->id;?>"
			/>

	</ul>
</div>
