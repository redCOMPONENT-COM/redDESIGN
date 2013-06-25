<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

?>
<form id="background" name="background" method="post" action="index.php" enctype="multipart/form-data">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="background">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="return" value="http://google.com">
	<input type="hidden" name="reddesign_designtype_id" id="reddesign_designtype_id"
		   value="<?php echo $this->item->reddesign_designtype_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<div id="backgrounds-configuration">
		<h3><?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="bg_title" value="">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label " for="bg_eps_file">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_FILE'); ?>
			</label>

			<div class="controls">
				<input type="file" name="bg_eps_file" id="bg_eps_file" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="bg_isPDFbgimage">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE'); ?>
			</label>

			<div class="controls">
				<select name="isPDFbgimage" id="bg_isPDFbgimage">
					<option value="0" selected="selected"><?php echo JText::_('JNO'); ?></option>
					<option value="1"><?php echo JText::_('JYES'); ?></option>
				</select>
				<span
					class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_IS_PDF_BG_IMAGE_DESC'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label todo-label" for="bg_status">
				<?php echo JText::_('JPUBLISHED'); ?>
			</label>

			<div class="controls">
				<select name="status" id="bg_status">
					<option value="1" selected="selected"><?php echo JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="button" class="btn btn-primary" id="saveBgBtn"
			   value="<?php echo JText::_('COM_REDDESIGN_COMMON_UPLOAD'); ?>"/>
		<input type="button" class="btn" id="cancelBgBtn"
			   value="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>"/>
	</div>
</form>


<script type="text/javascript">
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#saveBgBtn', function () {
					akeeba.jQuery('#background').submit();
				}
			);
			akeeba.jQuery(document).on('click', '#cancelBgBtn', function () {
					akeeba.jQuery('#uploadBgForm').fadeOut("slow");
					akeeba.jQuery('#addBgBtn').parent().show();
				}
			);
		});
</script>

