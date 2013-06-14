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


<style type="text/css">
	#container {
		width: <?php echo $this->bckgrnd_width; ?>px;
		height: <?php echo $this->bckgrnd_height; ?>px;
		position: relative;
	}

	#image {
		position: absolute;
		left: 0;
		top: 0;
	}

	#text {
		z-index: 100;
		position: absolute;
		color: black;
		font-family: <?php echo $this->font->slug; ?>;
		font-size: 24px;
		font-weight: bold;
		left: <?php echo $this->background->area_x1; ?>px;
		top: <?php echo $this->background->area_y1; ?>px;
	}

	@font-face {
		font-family: <?php echo $this->font->slug; ?>;
		src: url(<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/fonts/') . $this->font->fontfile; ?>);
	}

</style>

<script type="text/javascript">
	function text() {
		jQuery('#text').text(jQuery('#image-text').val());
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal" style="position:relative;">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="design">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="reddesign_design_id"
	       value="<?php echo $this->item->reddesign_design_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<div class="span12">
		<h3><?php echo JText::_('COM_REDDESIGN_DESIGN_TITLE'); ?></h3>

		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_DESIGN_FIELD_TITLE'); ?>
			</label>

			<div class="controls">
				<input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>"
				       class="inputbox required" size="50">
					<span
						class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGN_FIELD_TITLE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label todo-label" for="enabled">
				<?php echo JText::_('JSTATUS'); ?>
			</label>

			<div class="controls">
				<?php echo JHTML::_(
					'select.booleanlist',
					'enabled',
					'class="inputbox"',
					$this->item->enabled,
					JText::_('JPUBLISHED'),
					JText::_('JUNPUBLISHED')
				);
				?>
				<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label " for="title">
				<?php echo JText::_('COM_REDDESIGN_BACKGROUND'); ?>
			</label>

			<div class="controls">
				<?php echo $this->backgrounds; ?>

				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_DESC'); ?>
				</span>

				<div id="background-image">

					<div id="container">
						<img id="image" alt="<?php echo $this->background->title; ?>"
						     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->background->jpegpreviewfile; ?>"/>

						<p id="text">
							Hello World!
						</p>
					</div>

					<div id="image-inputs">
						<div class="control-group">
							<label for="image-text" class="control-label">
								<?php echo JText::_('COM_REDDESIGN_ENTER_TEXT') ?>
							</label>

							<div class="controls">
								<input type="text" class="left" id="image-text" name="image-text"
								       size="50"
								       value=""
								       onkeyup="javascript:text();">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
