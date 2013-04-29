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

JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

$lang = JFactory::getLanguage();
$option = 'com_reddesign';
?>

<div id="cpanel" class="span12">
	<div class="icon">
		<a href="index.php?option=<?php echo $option ?>&view=designtypes">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_designarea_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESING_CPANEL_ICON_DESGIGNTYPE_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESING_CPANEL_ICON_DESGIGNTYPE') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="index.php?option=<?php echo $option ?>&view=fonts">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_fonts_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESING_CPANEL_ICON_FONTS_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESING_CPANEL_ICON_FONTS') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="index.php?option=<?php echo $option ?>&view=help">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_help_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESING_CPANEL_ICON_HELP_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESING_CPANEL_ICON_HELP') ?><br/>
				</span>
		</a>
	</div>
	<div class="icon">
		<a href="index.php?option=<?php echo $option ?>&view=configuration">
			<img
				src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/reddesign_configuration_48.png"
				border="0" alt="<?php echo JText::_('COM_REDDESING_CPANEL_ICON_CONFIGURATION_ALT') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESING_CPANEL_ICON_CONFIGURATION') ?><br/>
				</span>
		</a>
	</div>
</div>

<div style="clear: both;"></div>