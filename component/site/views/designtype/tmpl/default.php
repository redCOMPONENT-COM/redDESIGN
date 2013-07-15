<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

/* @Todo: remove this dumps once frontend is finished
dump($this->item, 'design');
dump($this->backgrounds, 'backgrounds');
dump($this->productionBackground, 'production');
dump($this->previewBackground, 'preview');
dump($this->productionBackgroundAreas, 'areas');
dump($this->fonts, 'fonts');
*/
?>
<form id="designform" name="designform" method="post" action="index.php">
	<div class="row">
		<div class="span5">
			<img id="background"
				 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->previewBackground->image_path; ?>"/>
		</div>
		<div class="span4">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#customize" id="customizeLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_CUSTOMIZE_TAB'); ?></a></li>
				<li><a href="#parts" id="partsLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_PARTS_TAB'); ?></a></li>
				<li><a href="#accessories" id="accessoriesLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_ACCESSORIES_TAB'); ?></a></li>
			</ul>

			<div id="my-tab-content" class="tab-content">
				<div class="tab-pane active" id="customize">
					<?php echo $this->loadTemplate('customize'); ?>
				</div>
				<div class="tab-pane" id="parts">
					<?php echo $this->loadTemplate('parts'); ?>
				</div>
				<div class="tab-pane" id="accessories">
					<?php echo $this->loadTemplate('accessories'); ?>
				</div>
			</div>
		</div>
	</div>
</form>
