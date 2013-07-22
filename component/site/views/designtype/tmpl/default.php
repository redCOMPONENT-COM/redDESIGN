<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
JHTML::_('behavior.modal');
?>

<h1><?php echo $this->item->title; ?></h1>
<form id="designform" name="designform" method="post" action="index.php">
	<input type="hidden" name="option" value="com_reddesign">
	<input type="hidden" name="view" value="designtype">
	<input type="hidden" name="task" value="">
	<input type="hidden" id="reddesign_designtype_id" name="reddesign_designtype_id" value="<?php echo $this->item->reddesign_designtype_id; ?>">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1">

	<?php echo $this->loadTemplate('product'); ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#customize" id="customizeLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_CUSTOMIZE_TAB'); ?></a></li>
		<li><a href="#accessories" id="accessoriesLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_ACCESSORIES_TAB'); ?></a></li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="customize">
			<div class="row-fluid">
				<div class="span6">
					<img id="background"
						 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->previewBackground->image_path; ?>"/>
				</div>
				<div class="span5 offset1 well">
					<?php if (1 < count($this->previewBackgrounds)) : ?>
						<?php echo $this->loadTemplate('frames'); ?>
					<?php endif; ?>
					<?php echo $this->loadTemplate('areas'); ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="accessories">
			<?php echo $this->loadTemplate('accessories'); ?>
		</div>
	</div>
</form>

<script>
	/**
	 * Add click event to Customize button
	 */
	akeeba.jQuery(document).ready(
		function () {
			akeeba.jQuery(document).on('click', '#customizeDesign', function () {
					// Add spinner to button
					akeeba.jQuery(this).button('loadingo');
					setTimeout(
						function() {
							akeeba.jQuery(this).button('reset');
						},
						3000
					);

					akeeba.jQuery('#background')
						.attr('src', '<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/images/spinner.gif'); ?>');
					customize();
				}
			);
			akeeba.jQuery(document).on('click', '.accessory-option', function () {
					var total = 0;
					akeeba.jQuery('.accessory-option:checked').each(function () {
							total += parseInt(akeeba.jQuery(this).val());
						});
					akeeba.jQuery('#total').html(total + 'USD$');
				}
			);

			<?php foreach ($this->productionBackgroundAreas as  $area) : ?>
				var reddesign_area_id = parseInt(<?php echo $area->reddesign_area_id;?>);
				akeeba.jQuery('#colorSelector' + reddesign_area_id).ColorPicker({
					designId:reddesign_area_id,
					color: '#000000',
					onChange: function (hsb, hex, rgb, reddesign_area_id) {
						akeeba.jQuery('#colorSelector' +reddesign_area_id+ ' div').css('backgroundColor', '#' + hex);
						document.getElementById('colorCode'+reddesign_area_id).value = hex; // Edited
					}
				});

				//setup before functions
				var typingTimer;
				var doneTypingInterval = 400;

				//on keyup, start the countdown
				akeeba.jQuery('#textArea'+reddesign_area_id).keyup(function(){
				    clearTimeout(typingTimer);
				    typingTimer = setTimeout(customize, doneTypingInterval);

				});
			<?php endforeach; ?>
		}
	);

	/**
	 * Sends customize data to server and retreives the resulting image
	 *
	 * @param update
	 */
	function customize() {
		var design = {
			areas: []
		};
		var reddesign_designtype_id = akeeba.jQuery('#reddesign_designtype_id').val();
		var reddesign_background_id = akeeba.jQuery('#reddesign_background_id').val();
		<?php foreach($this->productionBackgroundAreas as $area) : ?>

		design.areas.push({
			"id" : 			'<?php echo $area->reddesign_area_id; ?>',
			"textArea" :	akeeba.jQuery('#textArea<?php echo $area->reddesign_area_id; ?>').val(),
			"fontArea" : 	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val(),
			"fontColor" :	akeeba.jQuery('#colorCode<?php echo $area->reddesign_area_id; ?>').val(),
			"fontSize" :	22,
			"fontTypeId" :	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val()
		});
		<?php endforeach; ?>
		design = JSON.stringify({Design: design });
		akeeba.jQuery.ajax({
			url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=designtype&task=ajaxGetDesign&format=raw",
			data: {reddesign_designtype_id: reddesign_designtype_id, reddesign_background_id: reddesign_background_id, designarea : design},
			type: "post",
			success: function(data) {
				var json = akeeba.jQuery.parseJSON(data);
				d = new Date();
				akeeba.jQuery('img#background').attr('src', json.image+"?"+d.getTime());
				console.log(data);
			},
			error: function(errMsg) {
				alert('<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_AJAX_ERROR'); ?>');
			}
		});
	}

	/**
	 * Set selected color for designarea.
	 *
	 * @param reddesign_area_id
	 * @param colorCode
	 */
	function setColorCode(reddesign_area_id, colorCode)
	{
		document.getElementById('colorCode'+reddesign_area_id).value = colorCode;
		akeeba.jQuery('#fontColor'+reddesign_area_id+ ' div').css('backgroundColor', '#' + colorCode);
		akeeba.jQuery('#fontColor'+reddesign_area_id).show();
		customize();
	}

	/**
	 * Set selected background for designarea.
	 *
	 * @param reddesign_background_id
	 */
	function setBackground(reddesign_background_id)
	{
		document.getElementById('reddesign_background_id').value = reddesign_background_id;
		customize();
	}
</script>
