<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

dump($this->item, 'design');
dump($this->backgrounds, 'backgrounds');
dump($this->productionBackground, 'production');
dump($this->previewBackground, 'preview');
dump($this->productionBackgroundAreas, 'areas');
dump($this->fonts, 'fonts');
?>
<form id="designform" name="designform" method="post" action="index.php">
	<div class="row">
		<div class="span6">
			<img id="background"
				 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->productionBackground->image_path; ?>"/>
		</div>
		<div class="span3">
				<?php foreach($this->productionBackgroundAreas as $area) : ?>
					<div class="control-group">
						<label class="control-label ">
							<?php echo $area->title; ?>
						</label>
						<div class="controls">
							<input
								type="text"
								name="<?php echo $area->reddesign_area_id; ?>"
								id="bg_title" value="">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label ">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
						</label>
						<div class="controls">
							<?php
							$areaFontsIds 	= explode(',', $area->font_id);
							$options 		= array();

							foreach ($areaFontsIds as $key => $value) :
								$options[] = JHTML::_('select.option', $value, $this->fonts[$value]->title);
							endforeach;

							echo JHTML::_('select.genericlist', $options, 'font', 'class="inputbox"', 'value', 'text', null);
							?>
						</div>
					</div>
				<?php endforeach; ?>
				<button type="button" class="btn btn-success"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE'); ?></button>
			</div>
		</div>
	</div>
</form>
