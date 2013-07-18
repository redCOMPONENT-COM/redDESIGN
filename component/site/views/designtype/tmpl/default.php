<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

/* @Todo: remove this dumps once frontend is finished */

dump($this->item, 'design');
dump($this->backgrounds, 'backgrounds');
dump($this->productionBackground, 'production');
dump($this->previewBackground, 'preview');
dump($this->previewBackgrounds, 'preview backgrounds');
dump($this->productionBackgroundAreas, 'areas');
dump($this->fonts, 'fonts');


?>
<h1><?php echo $this->item->title; ?></h1>
<form id="designform" name="designform" method="post" action="index.php">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#product" id="productLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_PRODUCT_TAB'); ?></a></li>
		<li><a href="#customize" id="customizeLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_CUSTOMIZE_TAB'); ?></a></li>
		<li><a href="#accessories" id="accessoriesLink" data-toggle="tab"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_ACCESSORIES_TAB'); ?></a></li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="product">
			<?php echo $this->loadTemplate('product'); ?>
		</div>
		<div class="tab-pane" id="customize">
			<div class="row">
				<div class="span5">
					<img id="background"
						 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->previewBackground->image_path; ?>"/>
				</div>
				<div class="span4">
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
			"fontArea" : 	akeeba.jQuery('#fontArea<?php echo $area->reddesign_area_id; ?>').val(),
			"fontColor" :	"#000000",
			"fontSize" :	"22"
		});
		<?php endforeach; ?>

		design.background = "2"; // @ToDo

		design.id = "1"; // @ToDo

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