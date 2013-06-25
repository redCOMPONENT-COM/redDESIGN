<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

$this->document->addScript(FOFTemplateUtils::parsePath('media://com_reddesign/assets/js/jquery.imgareaselect.pack.js'));
$this->document->addScript(FOFTemplateUtils::parsePath('media://com_reddesign/assets/js/areaselect.js'));
?>

<div id="fonts-settings" class="span12">
	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FONTS'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FONTS_DESC'); ?></span>
	<div class="control-group">
		<label class="control-label" for="backgrounds-fonts">
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('select.genericlist',
								$this->backgroundsDropDownOptions,
								'backgrounds-fonts',
								' onclick="selectBackgroundFonts()" ',
								'value',
								'text'); ?>
			<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_FONT_BACKGROUNDS_DESC'); ?></span>
		</div>
		<div id="fonts-areas">
			<?php
			$areas[] = JHTML::_('select.option', '0', JText::_('COM_REDDESIGN_COMMON_SELECT'));
			$areas[] = JHTML::_('select.option', '1', 'Area 1');
			$areas[] = JHTML::_('select.option', '2', 'Area 2');
			$areas[] = JHTML::_('select.option', '3', 'Area 3');
			?>

			<label class="control-label" for="backgrounds-fonts">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_SELECT_AREA'); ?>
			</label>
			<div class="controls">
				<?php echo JHtml::_('select.genericlist', $areas, 'backgrounds-fonts', ' onclick="selectAreaFonts()" ', 'value', 'text'); ?>
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_SELECT_AREA_DESC'); ?></span>
			</div>
			<div id="font-settings">

			</div>
		</div>
	</div>
</div>