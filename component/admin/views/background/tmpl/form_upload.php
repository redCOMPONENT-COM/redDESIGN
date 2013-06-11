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
	<label class="control-label " for="fontfile">
		<?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_EPSFILE'); ?>
	</label>
	<div class="controls">
		<input type="file" name="epsfile" id="epsfile" value="<?php echo $this->item->epsfile; ?>">
		<span
			class="help-block"><?php echo JText::_('COM_REDDESIGN_BACKGROUND_FIELD_EPSFILE_DESC'); ?>
		</span>
	</div>
</div>