<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
?>
<div class="row">
	<div class="span9">
		<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_AREAS_TITLE') ?></h3>
		<?php foreach($this->productionBackgroundAreas as $area) : ?>
			<div class="control-group">
				<label class="control-label ">
					<?php echo $area->title; ?>
				</label>
				<div class="controls">
					<input
						type="text"
						name="textArea<?php echo $area->reddesign_area_id; ?>"
						id="textArea<?php echo $area->reddesign_area_id; ?>"
						value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label ">
					<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CHOOSE_FONT'); ?>
				</label>
				<div class="controls">
					<?php
					if (empty($area->font_id))
					{
						echo JText::_('COM_REDDESIGN_DESIGNTYPE_NO_FONTS');
					}
					else
					{
						$areaFontsIds 	= explode(',', $area->font_id);
						$options 		= array();

						foreach ($areaFontsIds as $key => $value) :
							$options[] = JHTML::_('select.option', $value, $this->fonts[$value]->title);
						endforeach;

						echo JHTML::_(
							'select.genericlist',
							$options,
							'fontArea' . $area->reddesign_area_id,
							'class="inputbox"',
							'value',
							'text',
							null
						);
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="form-actions">
			<button
				type="button"
				class="btn btn-success"
				data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE_LOADING') ?>"
				id="customizeDesign"
				><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_CUSTOMIZE'); ?></button>
			<button type="button" class="btn" id="orderDesign"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_ORDER'); ?></button>
		</div>
	</div>
</div>

<script type="">
	/**
	 * Add click event to Customize button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#orderDesign', function () {
					akeeba.jQuery('#task').val('orderProduct');
					akeeba.jQuery('#designform').submit();
				}
			);

		});
</script>