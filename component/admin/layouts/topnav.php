<?php
/**
 * @package     Reddesign.Admin
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>
<span class="divider-vertical pull-left"></span>
<ul class="nav">
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_reddesign&view=designtypes') ?>">
			<?php echo JText::_('COM_REDDESIGN_TITLE_DESIGNTYPES') ?>
		</a>
	</li>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_reddesign&view=fonts') ?>">
			<?php echo JText::_('COM_REDDESIGN_TITLE_FONTS') ?>
		</a>
	</li>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_reddesign&view=config&layout=edit') ?>">
			<?php echo JText::_('COM_REDDESIGN_CONFIG_GENERAL_CONFIGURATION'); ?>
		</a>
	</li>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_redshop') ?>">
			redSHOP
		</a>
	</li>
</ul>
