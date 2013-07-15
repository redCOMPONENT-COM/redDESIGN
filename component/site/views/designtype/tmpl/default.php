<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

/* @Todo: remove this dumps once frontend is finished */
/*
dump($this->item, 'design');
dump($this->backgrounds, 'backgrounds');
dump($this->productionBackground, 'production');
dump($this->previewBackground, 'preview');
dump($this->productionBackgroundAreas, 'areas');
dump($this->parts, 'parts');
dump($this->fonts, 'fonts');
*/

?>
<h1><?php echo $this->item->title; ?></h1>
<form id="designform" name="designform" method="post" action="index.php">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#product" id="productLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_PRODUCT_TAB'); ?></a></li>
		<li><a href="#customize" id="customizeLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_CUSTOMIZE_TAB'); ?></a></li>
		<li><a href="#parts" id="partsLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_PARTS_TAB'); ?></a></li>
		<li><a href="#accessories" id="accessoriesLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_ACCESSORIES_TAB'); ?></a></li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="product">
			<?php echo $this->loadTemplate('product'); ?>
		</div>
		<div class="tab-pane" id="customize">
			<?php echo $this->loadTemplate('customize'); ?>
		</div>
		<div class="tab-pane" id="parts">
			<?php echo $this->loadTemplate('parts'); ?>
		</div>
		<div class="tab-pane" id="accessories">
			<?php echo $this->loadTemplate('accessories'); ?>
		</div>
	</div>
</form>
