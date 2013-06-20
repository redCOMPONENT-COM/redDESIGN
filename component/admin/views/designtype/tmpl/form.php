<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHTML::_('behavior.framework');
?>

<ul class="nav nav-tabs">
	<li><a href="#general" data-toggle="tab">General tab</a></li>
	<li><a href="#backgrounds" data-toggle="tab">Backgrounds</a></li>
	<li><a href="#design-areas" data-toggle="tab">Design areas</a></li>
	<li><a href="#fonts" data-toggle="tab">Fonts</a></li>
	<li><a href="#fonts-sizes" data-toggle="tab">Fonts sizes</a></li>
	<li><a href="#color" data-toggle="tab">Color</a></li>
</ul>
<div id="my-tab-content" class="tab-content">
	<div class="tab-pane active" id="general">
		<?php echo $this->loadTemplate('general'); ?>
	</div>
	<div class="tab-pane" id="backgrounds">
		<?php echo $this->loadTemplate('backgrounds'); ?>
	</div>
	<div class="tab-pane" id="design-areas">
		<?php echo $this->loadTemplate('designareas'); ?>
	</div>
	<div class="tab-pane" id="fonts">
		<?php echo $this->loadTemplate('fonts'); ?>
	</div>
	<div class="tab-pane" id="fonts-sizes">
		<?php echo $this->loadTemplate('fontsizes'); ?>
	</div>
	<div class="tab-pane" id="color">
		<?php echo $this->loadTemplate('color'); ?>
	</div>
</div>
