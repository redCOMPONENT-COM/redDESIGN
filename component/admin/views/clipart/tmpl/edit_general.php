<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
?>
<div id="basic_configuration" class="span12">
	<div class="span6">
		<div class="control-group">
			<label class="control-label " for="name">
				<?php echo $this->form->getLabel('name'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('name'); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_CLIPART_NAME_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="categoryId">
				<?php echo $this->form->getLabel('categoryId'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('categoryId'); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="clipartFile">
				<?php echo $this->form->getLabel('clipartFile'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('clipartFile'); ?>
			</div>
		</div>

		<div class="control-group">
			<label for="state" class="control-label">
				<?php echo $this->form->getLabel('state'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('state'); ?>
			</div>
		</div>
	</div>
	<div class="span6">
		<?php if (!empty($this->item->clipartFile)) : ?>
			<div class="control-group">
				<h4>
					<?php echo JText::_('COM_REDDESIGN_CLIPART_PREVIEW') ?>
				</h4>

				<div>
					<img class="thumbnail" src="<?php echo JURI::root() . 'media/com_reddesign/cliparts/' . $this->item->clipartFile; ?>" />
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>