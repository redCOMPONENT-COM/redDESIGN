<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_reddesign&view=config&layout=edit');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#general" data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_CONFIG_GENERAL_CONFIGURATION'); ?>
			</a>
		</li>
		<li class="">
			<a href="#images" data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_CONFIG_IMAGES_CONFIGURATION'); ?>
			</a>
		</li>
		<li class="">
			<a href="#permissions" data-toggle="tab">
				<?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?>
			</a>
		</li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="general">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('autoCustomize'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('autoCustomize'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('unit'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('unit'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('source_dpi'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('source_dpi'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('font_preview_text'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('font_preview_text'); ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="images">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('max_svg_file_size'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('max_svg_file_size'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('max_svg_backend_bg_width'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('max_svg_backend_bg_width'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('max_svg_backend_bg_height'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('max_svg_backend_bg_height'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('max_svg_frontend_bg_width'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('max_svg_frontend_bg_width'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('max_svg_frontend_bg_height'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('max_svg_frontend_bg_height'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('productionFilePadding'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('productionFilePadding'); ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="permissions">
			<div class="control-group">
				<div>
					<?php echo $this->form->getInput('rules'); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_reddesign" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="1" />
	<?php echo JHTML::_('form.token'); ?>
</form>
