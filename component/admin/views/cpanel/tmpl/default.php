<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @version   $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

$lang = JFactory::getLanguage();
$option = 'com_reddesign';
?>


<h2><?php echo JText::_('COM_REDDESING_CPANEL') ?></h2>


<div class="icon">
	<a href="index.php?option=<?php echo $option ?>&view=help">
		<img
			src="<?php echo rtrim(JURI::base(), '/'); ?>/../media/com_reddesign/assets/images/help32.png"
			border="0" alt="<?php echo JText::_('COM_REDDESING_CPANEL_HELP_ICON') ?>"/>
				<span>
					<?php echo JText::_('COM_REDDESING_CPANEL_HELP_ICON') ?><br/>
				</span>
	</a>
</div>

<div style="clear: both;"></div>