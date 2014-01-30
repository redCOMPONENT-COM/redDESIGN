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

$action = JRoute::_('index.php?option=com_reddesign&view=clipart');
?>
<form id="adminForm" name="adminForm" class="form-validate form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#general" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_COMMON_GENERAL'); ?></a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<?php echo $this->loadTemplate('general') ?>
		</div>
	</div>

	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="clipart">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<?php echo JHtml::_('form.token'); ?>
</form>
