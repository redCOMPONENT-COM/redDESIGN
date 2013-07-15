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

<script>
	/**
	 * Add click event to Customize button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#customizeDesign', function () {
					// Add spinner to button
					akeeba.jQuery(this).button('loading');
					setTimeout(function() {
						akeeba.jQuery(this).button('reset');
					}, 3000);

					akeeba.jQuery('#background').attr('src', '<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/images/spinner.gif'); ?>');
					customize();
				}
			);
		});

	/**
	 * Sends customize data to server and retreives the resulting image
	 *
	 * @param update
	 */
	function customize() {
		var design = {
			areas: []
		};
		<?php foreach($this->productionBackgroundAreas as $area) : ?>

		design.areas.push({
			"id" : 			'<?php echo $area->reddesign_area_id; ?>',
			"textArea" :	akeeba.jQuery('#textArea<?php echo $area->reddesign_area_id; ?>').val(),
			"fontArea" : 	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val()
		});
		<?php endforeach; ?>

		akeeba.jQuery.ajax({
			type: "POST",
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=designtype&task=ajaxGetDesign&format=raw",
			data: JSON.stringify({ Design: design }),
			contentType: "application/json; charset=utf-8",
			success: function(data) {
				var json = akeeba.jQuery.parseJSON(data);
				akeeba.jQuery('#background').attr('src', json.image);
				console.log(data);
			},
			failure: function(errMsg) {
				alert('<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AJAX_ERROR'); ?>');
			}
		});
	}
</script>