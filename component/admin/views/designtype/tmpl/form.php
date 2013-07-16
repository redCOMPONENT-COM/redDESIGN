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

FOFTemplateUtils::addJS('media://com_reddesign/assets/js/jquery.imgareaselect.pack.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/assets/css/imgareaselect-animated.css');

// If is a new design don't show tabs
if (empty($this->item->reddesign_designtype_id)) :
	echo $this->loadTemplate('general');
else :
?>

<script>
	/* This function set the active Tab based on &tab= url value. General is the default. See view.html.php  */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery('#<?php echo $this->activeTab; ?>').addClass('active');
			akeeba.jQuery('#<?php echo $this->activeTab; ?>Link').parent().addClass('active');
		}
	);

    /**
     * Function to clear area selection drawn over other tabs due to auto zIndex
	 */
	function clearAreaSelection() {
		var imageAreaSelection = akeeba.jQuery("img#background").imgAreaSelect({ instance: true });
		imageAreaSelection.cancelSelection();
	}
</script>

<ul class="nav nav-tabs">
	<li><a href="#general"      id="generalLink"       data-toggle="tab" onclick="clearAreaSelection();"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_GENERAL_TAB'); ?></a></li>
	<li><a href="#backgrounds"  id="backgroundsLink"   data-toggle="tab" onclick="clearAreaSelection();"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS'); ?></a></li>
	<li><a href="#design-areas" id="design-areasLink"  data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_DESIGN_AREAS'); ?></a></li>
</ul>

<div id="my-tab-content" class="tab-content">
	<div class="tab-pane" id="general">
		<?php echo $this->loadTemplate('general'); ?>
	</div>
	<div class="tab-pane" id="backgrounds">
		<?php echo $this->loadTemplate('backgrounds'); ?>
	</div>
	<div class="tab-pane" id="design-areas">
		<?php echo $this->loadTemplate('designareas'); ?>
	</div>
</div>

<?php endif;
