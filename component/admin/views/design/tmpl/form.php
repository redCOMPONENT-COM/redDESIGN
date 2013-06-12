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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"
      class="form-horizontal">
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
				<?php
				$model = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel');

				$items = $model->savestate(0)->limit(0)->limitstart(0)->getItemList();

				$options = array();

				if (count($items)) foreach ($items as $item)
				{
					$options[] = JHTML::_('select.option', $item->reddesign_background_id, $item->title);
				}

				array_unshift($options, JHTML::_('select.option', 0, '- ' . JText::_('COM_REDDESIGN_SELECT_BACKGROUND') . ' -'));

				//$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
				$onchange = ' onchange="javascript:"';

				echo JHtml::_('select.genericlist', $options, 'reddesign_background_id', $onchange, 'value', 'text', $this->item->reddesign_background_id);
				?>

				<span class="help-block">
					<?php echo JText::_('COM_REDDESIGN_BACKGROUND_DESC'); ?>
				</span>

				<div id="background-image">
					<img class="left" id="background"
					     src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->item->jpegpreviewfile; ?>"/>
				</div>
			</div>
		</div>
	</div>
</form>