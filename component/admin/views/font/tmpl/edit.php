<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$action = JRoute::_('index.php?option=com_reddesign&view=font');
?>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#general" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_COMMON_GENERAL'); ?></a>
		</li>
		<li>
			<a href="#chars" data-toggle="tab"><?php echo JText::_('COM_REDSHOPB_ADDRESS_LABEL'); ?></a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<?php echo $this->loadTemplate('general') ?>
		</div>
		<div class="tab-pane" id="chars">
			<?php echo $this->loadTemplate('chars') ?>
		</div>
	</div>

	<input type="hidden" name="from_company" value="<?php echo $fromCompany ?>">
	<input type="hidden" name="from_department" value="<?php echo $fromDepartment ?>">
	<input type="hidden" name="option" value="com_redshopb">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
