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
<div class="control-group">
	<label class="control-label todo-label" for="enabled">
		<?php echo JText::_('COM_REDDESIGN_DESIGNBACKGROUND_FIELD_AVAILABLE_FONTS'); ?>
	</label>
	<div class="controls">
		<fieldset>
			<?php foreach ($this->fonts_list as $font) : ?>
				<label class="checkbox">
					<input
						type="checkbox"
						name="background_fonts[]"
						value="<?php echo $font->reddesign_font_id; ?>"
						/><?php echo $font->title; ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<span class="help-block"><?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?></span>
	</div>
</div>