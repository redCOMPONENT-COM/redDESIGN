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
JHtml::_('rjquery.select2', 'select');

RHelperAsset::load('jquery.imgareaselect.pack.js');
RHelperAsset::load('imgareaselect-animated.css');
?>
<script>
	/**
	 * Function to clear area selection drawn over other tabs due to auto zIndex.
	 * Without this function, area selections apear on top of all tabs.
	 */
	function clearAreaSelection() {
		var imageAreaSelection = jQuery("img#background").imgAreaSelect({ instance: true });

		if(typeof imageAreaSelection != 'undefined')
		{
			imageAreaSelection.cancelSelection();
		}
	}
</script>

<?php if (!extension_loaded('gd') && !function_exists('gd_info')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_GD_LIBRARY'); ?></p>
<?php endif; ?>

<?php if (!extension_loaded('imagick')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
<?php endif; ?>

<ul class="nav nav-tabs">
	<li class="active">
		<a href="#general" id="generalLink" data-toggle="tab" onclick="clearAreaSelection();">
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_GENERAL_TAB'); ?>
		</a>
	</li>
	<?php if (!empty($this->item->id)) : ?>
		<li>
			<a href="#backgrounds"  id="backgroundsLink" data-toggle="tab" onclick="clearAreaSelection();">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS'); ?>
			</a>
		</li>
		<li>
			<a href="#design-areas" id="design-areasLink"  data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_DESIGN_AREAS'); ?>
			</a>
		</li>
	<?php endif; ?>
</ul>

<div id="my-tab-content" class="tab-content">
	<div class="tab-pane active" id="general">
		<?php echo $this->loadTemplate('general'); ?>
	</div>
	<?php if (!empty($this->item->id)) : ?>
		<div class="tab-pane" id="backgrounds">
			<?php echo $this->loadTemplate('background'); ?>
			<?php echo $this->loadTemplate('backgrounds'); ?>
		</div>
		<div class="tab-pane" id="design-areas">
			<?php echo $this->loadTemplate('designareas'); ?>
		</div>
	<?php endif; ?>
</div>