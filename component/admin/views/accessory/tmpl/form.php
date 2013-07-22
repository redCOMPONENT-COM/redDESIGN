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
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="accessory">
	<input type="hidden" name="task" value="">
	<input type="hidden" id="reddesign_accessory_id" name="reddesign_accessory_id" value="<?php echo $this->item->reddesign_accessory_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1">

	<div id="basic_configuration" class="span12">
		<h3>
			<?php echo $this->pageTitle; ?>
		</h3>

		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_COMMON_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->title; ?>" maxlength="255" size="32" id="title" name="title">
			</div>
		</div>

		<div class="control-group">
			<label for="enabled" class="control-label">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'enabled', null, $this->item->enabled); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="isDefault">
				<?php echo JText::_('COM_REDDESIGN_ACCESSORY_DEFAULT'); ?>
			</label>
			<div class="controls">
				<?php echo JHTML::_('select.booleanlist', 'default', null, $this->item->default); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_ACCESSORY_DEFAULT_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="reddesign_accessorytype_id">
				<?php echo JText::_('COM_REDDESIGN_ACCESSORY_ACCESSORYTYPE'); ?>
			</label>
			<div class="controls">
				<?php echo $this->accessorytypes; ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_ACCESSORY_ACCESSORYTYPE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="image">
				<?php echo JText::_('COM_REDDESIGN_COMMON_IMAGE'); ?>
			</label>
			<div class="controls">
				<input type="file" name="image" id="image" value="">
				<a class="modal" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/' . $this->item->image); ?>">
					<?php echo $this->item->image; ?>
				</a>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="thumbnail">
				<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL'); ?>
			</label>
			<div class="controls">
				<input type="file" name="thumbnail" id="thumbnail" value="">
				<a class="modal" href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/accessories/thumbnails/' . $this->item->thumbnail); ?>">
					<?php echo $this->item->thumbnail; ?>
				</a>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="stock">
				<?php echo JText::_('COM_REDDESIGN_ACCESSORY_STOCK'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->stock; ?>" maxlength="255" size="32" id="stock" name="stock">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_ACCESSORY_PRICE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="price">
				<?php echo JText::_('COM_REDDESIGN_ACCESSORY_PRICE'); ?>
			</label>

			<div class="controls">
				<input type="text" value="<?php echo $this->item->price; ?>" maxlength="255" size="32" id="price" name="price">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_ACCESSORY_PRICE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="description">
				<?php echo JText::_('COM_REDDESIGN_COMMON_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<?php echo $this->editor->display('description', $this->item->description, 400, 400, 20, 20, false); ?>
			</div>
		</div>
	</div>
</form>