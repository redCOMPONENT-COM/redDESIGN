<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

?>

<div id="cpanel" class="span12">
	<?php if (!extension_loaded('imagick')) : ?>
		<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
	<?php endif; ?>
	<div class="icon">
		<a href="index.php?option=com_reddesign&view=designtypes">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_designarea_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_DESGIGNTYPE_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_DESGIGNTYPE') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="index.php?option=com_reddesign&view=backgrounds">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_designarea_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESIGN_BACKGROUNDS_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESIGN_BACKGROUNDS') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="index.php?option=com_reddesign&view=fonts">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_fonts_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_FONTS_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_FONTS') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="http://wiki.redcomponent.com/index.php?title=redDESIGN:Table_of_Contents" target="_blank">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_help_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_HELP_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESIGN_CPANEL_ICON_HELP') ?><br/>
				</span>
		</a>
	</div>
</div>

<div style="clear: both;"></div>