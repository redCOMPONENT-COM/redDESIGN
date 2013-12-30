<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

?>
<form enctype="multipart/form-data"
	action="index.php?option=com_reddesign&task=designtype.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" value="">
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<div id="basic_configuration" class="span12">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('name'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('name'); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('state'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('state'); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('fontsizer'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('fontsizer'); ?>
			</div>
		</div>

	</div>
</form>
