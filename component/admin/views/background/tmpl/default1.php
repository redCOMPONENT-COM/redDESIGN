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

$action = JRoute::_('index.php?option=com_reddesign&view=background');
?>
<form id="adminForm" name="adminForm" class="form-validate form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >
	<div id="backgrounds-configuration">

		<h3 id="backgroundTitle"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label ">
				<?php echo $this->form->getLabel('name'); ?>
			</label>

			<div class="controls">
				<?php echo $this->form->getInput('name'); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="enabled" class="control-label">
				<?php echo $this->form->getLabel('state'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('state'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="bg_eps_file">
				<?php echo $this->form->getLabel('bg_eps_file'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('bg_eps_file'); ?>
			</div>
		</div>

		<div class="control-group" id="isProductionBgContainer">
			<div class="controls">
				<?php echo $this->form->getInput('isProductionBg'); ?>
				<?php echo $this->form->getLabel('isProductionBg'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_IS_PRODUCTION_BACKGROUND_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group" id="isPreviewBgContainer">
			<div class="controls">
				<?php echo $this->form->getInput('isPreviewBg'); ?>
				<?php echo $this->form->getLabel('isPreviewBg'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_IS_PREVIEW_BACKGROUND_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group" id="isDefaultPreviewContainer">
			<div class="controls">
				<?php echo $this->form->getInput('isDefaultPreview'); ?>
				<?php echo $this->form->getLabel('isDefaultPreview'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_DEFAULT_PREVIEW_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group" id="useCheckerboardContainer">
			<div class="controls">
				<?php echo $this->form->getInput('useCheckerboard'); ?>
				<?php echo $this->form->getLabel('useCheckerboard'); ?>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_USE_CHECKERBOARD_DESC'); ?>
				</span>
			</div>
		</div>

		<div class="control-group previewbg" style="display: none">
			<label class="control-label " for="thumbnail">
				<?php echo $this->form->getLabel('thumbnail'); ?>
			</label>
			<div class="controls">
				<?php echo $this->form->getInput('thumbnail'); ?>&nbsp;<a href="#" class="modal" id="BgThumbnailLink"></a>
				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_COMMON_THUMBNAIL_DESC'); ?>
				</span>
			</div>
		</div>

	</div>

	<div class="form-actions">
		<input type="button" class="btn btn-success" id="saveBgBtn" value="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>"/>
		<input type="button" class="btn" id="cancelBgBtn" value="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>"/>
	</div>

	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="background">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="designtype_id" id="designtype_id" value="<?php echo $this->item->designtype_id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<?php echo JHtml::_('form.token'); ?>
</form>
