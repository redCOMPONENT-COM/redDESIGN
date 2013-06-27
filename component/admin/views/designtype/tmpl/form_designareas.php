<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

FOFTemplateUtils::addJS('media://com_reddesign/assets/js/jquery.imgareaselect.pack.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/assets/css/imgareaselect-animated.css');
?>

<?php if (empty($this->productionBackground)) : ?>

	<h3><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND'); ?></h3>
	<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_NO_BACKGROUND_DESC'); ?></span>

<?php else : ?>

	<script type="text/javascript">

        /**
         * Populates parameter fields from selected area
		 *
		 * @param img
         * @param selection
         */
		function populateSelectionFields(img, selection) {
			if(selection.width == 0 || selection.height == 0)
			{
				clearSelectionFields();
			}
			else
			{
				akeeba.jQuery('#areaX1').val(selection.x1);
				akeeba.jQuery('#areaY1').val(selection.y1);
				akeeba.jQuery('#areaX2').val(selection.x2);
				akeeba.jQuery('#areaY2').val(selection.y2);
				akeeba.jQuery('#areaWidth').val(selection.width);
				akeeba.jQuery('#areaHeight').val(selection.height);
			}
		}

        /**
         * Clears parameter input fields. Used when select area is not displayed anymore.
		 */
		function clearSelectionFields() {
			akeeba.jQuery('#areaName').val('');
			akeeba.jQuery('#areaX1').val('');
			akeeba.jQuery('#areaY1').val('');
			akeeba.jQuery('#areaX2').val('');
			akeeba.jQuery('#areaY2').val('');
			akeeba.jQuery('#areaWidth').val('');
			akeeba.jQuery('#areaHeight').val('');
		}

        /**
         * Selects area with given parameters. Used onkeyup event in parameter input fields.
		 *
		 * @param x1
         * @param y1
         * @param x2
         * @param y2
         * @param width
         * @param height
         */
		function selectArea(x1, y1, x2, y2, width, height) {
			akeeba.jQuery('img#background').imgAreaSelect({
				handles: true,
				x1: x1,
				y1: y1,
				x2: x2,
				y2: y2,
				area_width: width,
				area_height: height
			});
		}

        /**
         * Initiate imgAreaSelect plugin
		 */
		akeeba.jQuery(document).ready(
			function ($) {
				akeeba.jQuery('img#background').imgAreaSelect({
					handles: true,
					onInit: clearSelectionFields,
					onSelectEnd: populateSelectionFields
			});
		});

        /**
         * Saves area into the DB via AJAX. And prepares image for another selection.
		 *
		 * @param update
         */
		function saveArea(update) {
			var areaName	= akeeba.jQuery('#areaName').val();
			var areaX1 	= akeeba.jQuery('#areaX1').val();
			var areaY1 	= akeeba.jQuery('#areaY1').val();
			var areaX2 	= akeeba.jQuery('#areaX2').val();
			var areaY2 	= akeeba.jQuery('#areaY2').val();
			var areaWidth  = akeeba.jQuery('#areaWidth').val();
			var areaHeight = akeeba.jQuery('#areaHeight').val();

			akeeba.jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxSave&format=raw",
				data: {
					title: areaName,
					reddesign_background_id: <?php echo $this->productionBackground->reddesign_background_id; ?>,
					x1_pos: areaX1,
					y1_pos: areaY1,
					x2_pos: areaX2,
					y2_pos: areaY2,
					width: areaWidth,
					height: areaHeight
				},
				type: "post",
				success: function (data) {
					var json = akeeba.jQuery.parseJSON(data);
					akeeba.jQuery("#ajaxMessageAreas").removeClass();
					akeeba.jQuery("#ajaxMessageAreas").addClass("alert alert-success");
					akeeba.jQuery("#ajaxMessageAreas").html(json.message);
					akeeba.jQuery("#ajaxMessageAreas").fadeIn("slow");
					akeeba.jQuery("#ajaxMessageAreas").fadeOut(3000);

					if(update == 0)
					{
						drawArea(json.reddesign_area_id, json.title, json.x1_pos, json.y1_pos, json.width, json.height);
						clearSelectionFields();
						clearAreaSelection();
					}
				},
				error: function (data) {
					akeeba.jQuery("#ajaxMessageAreas").removeClass();
					akeeba.jQuery("#ajaxMessageAreas").addClass("alert alert-error");
					akeeba.jQuery("#ajaxMessageAreas").html(data);
					akeeba.jQuery("#ajaxMessageAreas").fadeIn("slow");
					akeeba.jQuery("#ajaxMessageAreas").fadeOut(3000);
				}
			});
		}

        /**
         * Draws saved area onto an image.
		 *
		 * @param reddesign_area_id
         * @param title
         * @param x1_pos
         * @param y1_pos
         * @param width
         * @param height
         */
		function drawArea(reddesign_area_id, title, x1_pos, y1_pos, width, height) {
			akeeba.jQuery("#backgroundImageContainer").append('<div id="areaDiv' + reddesign_area_id + '">' + title + '</div>');
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("position", "absolute");
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("top", y1_pos);
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("left", x1_pos);
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("width", width);
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("height", height);
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("color","#5B5BA9");
			akeeba.jQuery("#areaDiv" + reddesign_area_id).css("border","2px solid #5B5BA9");
		}

        /**
         * Uses AJAX to update image with areas
		 */
		function updateImageAreas() {
			akeeba.jQuery("#backgroundImageContainer div").remove();

			akeeba.jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxGetAreas&format=raw",
				success: function (data) {
					var json = akeeba.jQuery.parseJSON(data);
					akeeba.jQuery.each( json, function( key, value ) {
						drawArea(value.reddesign_area_id, value.title, value.x1_pos, value.y1_pos, value.width, value.height)
					});
				},
				error: function (data) {
					akeeba.jQuery("#ajaxMessageAreas").removeClass();
					akeeba.jQuery("#ajaxMessageAreas").addClass("alert alert-error");
					akeeba.jQuery("#ajaxMessageAreas").html(data);
					akeeba.jQuery("#ajaxMessageAreas").fadeIn("slow");
					akeeba.jQuery("#ajaxMessageAreas").fadeOut(3000);
				}
			});
		}

        /**
         * Function for cancel button
		 */
		function cancelArea() {
			clearSelectionFields();
			clearAreaSelection();
			updateImageAreas();
		}

        /**
         * Deletes an area.
		 *
		 * @param reddesign_area_id
         */
		function removeArea(reddesign_area_id) {
			akeeba.jQuery.ajax({
				url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxRemove&format=raw",
				data: {
					reddesign_area_id: reddesign_area_id
				},
				type: "post",
				success: function (data) {
					akeeba.jQuery("#areaRow" + reddesign_area_id).remove();
					updateImageAreas();
					akeeba.jQuery("#ajaxMessageAreas").removeClass();
					akeeba.jQuery("#ajaxMessageAreas").addClass("alert alert-success");
					akeeba.jQuery("#ajaxMessageAreas").html(data);
					akeeba.jQuery("#ajaxMessageAreas").fadeIn("slow");
					akeeba.jQuery("#ajaxMessageAreas").fadeOut(3000);
				},
				error: function (data) {
					akeeba.jQuery("#ajaxMessageAreas").removeClass();
					akeeba.jQuery("#ajaxMessageAreas").addClass("alert alert-error");
					akeeba.jQuery("#ajaxMessageAreas").html(data);
					akeeba.jQuery("#ajaxMessageAreas").fadeIn("slow");
					akeeba.jQuery("#ajaxMessageAreas").fadeOut(3000);
				}
			});
		}
	</script>

	<style type="text/css">
		#backgroundImageContainer {
			position: relative;
		}

		<?php foreach($this->areas as $area) : ?>
			#areaDiv<?php echo $area->reddesign_area_id; ?> {
				position: absolute;
				top: <?php echo $area->y1_pos;?>px;
				left: <?php echo $area->x1_pos;?>px;
				width: <?php echo $area->width;?>px;
				height: <?php echo $area->height;?>px;
				color: #5B5BA9;
				border: 2px solid #5B5BA9;
			}
		<?php endforeach; ?>
	</style>

	<div class="form-container">
		<h3><?php echo JText::sprintf('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS', $this->productionBackground->title); ?></h3>
		<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_DESC'); ?></span>

		<div class="well">
			<div id="selectorControls" class="row-fluid">
				<div class="span3">
					<input id="designAreaId" name="designAreaId" type="hidden" value="0">
					<div class="control-group">
						<label for="areaName" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_NAME'); ?>
						</label>
						<div class="controls">
							<input type="text" id="areaName" name="areaName" value="">
						</div>
					</div>
					<div class="control-group">
						<label for="areaX1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X1'); ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX1" name="areaX1"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="areaY1" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y1') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY1" name="areaY1"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="areaX2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_X2') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaX2" name="areaX2"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="areaY2" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_Y2') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaY2" name="areaY2"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="areaWidth" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_WIDTH') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaWidth" name="areaWidth"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
					<div class="control-group">
						<label for="areaHeight" class="control-label">
							<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_HEIGHT') ?>
						</label>
						<div class="controls">
							<input  type="text" id="areaHeight" name="areaHeight"
									value=""
									onkeyup="selectArea(akeeba.jQuery('#areaX1').val(),
														akeeba.jQuery('#areaY1').val(),
														akeeba.jQuery('#areaX2').val(),
														akeeba.jQuery('#areaY2').val(),
														akeeba.jQuery('#areaWidth').val(),
														akeeba.jQuery('#areaHeight').val());">
						</div>
					</div>
				</div>
				<div class="span9">
					<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_IMG_HELP'); ?></span>
					<div id="backgroundImageContainer">
						<img id="background"
								 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->productionBackground->image_path; ?>"/>
						<?php foreach($this->areas as $area) : ?>
							<div id="areaDiv<?php echo $area->reddesign_area_id; ?>"><?php echo $area->title; ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="form-actions">
				<a id="saveAreaBtn" rel="" class="btn btn-primary"
				   title="<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>"
				   onclick="saveArea(akeeba.jQuery('#designAreaId').val());">
					<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
				</a>
				<a id="cancelAreaBtn" rel="" class="btn" onclick="cancelArea();" title="<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>">
					<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
				</a>
			</div>

			<div id="ajaxMessageAreasContainer" style="height: 25px; padding-bottom: 11px;">
				<div id="ajaxMessageAreas" style="display: none;">
				</div>
			</div>
		</div>

		<table id="designAreaList" class="table table-striped">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('ID'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_NAME'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_AREA_PROPERTIES'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if ($count = count($this->areas)) : ?>
				<?php
				$i = -1;
				$m = 1;
				?>
				<?php foreach ($this->areas as $area) : ?>
					<?php
					$i++;
					$m = 1 - $m;
					?>
					<tr id="areaRow<?php echo $area->reddesign_area_id; ?>" class="<?php echo 'row' . $m; ?>">
						<td>
							<?php echo $area->reddesign_area_id; ?>
						</td>
						<td>
							<a href="#">
								<strong><?php echo $this->escape(JText::_($area->title)) ?></strong>
							</a>
						</td>
						<td>
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X1'); ?></strong>
							<?php echo $area->x1_pos; ?>,
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y1'); ?></strong>
							<?php echo $area->y1_pos; ?>,
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_X2'); ?></strong>
							<?php echo $area->x1_pos; ?>,
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_Y2'); ?></strong>
							<?php echo $area->y1_pos; ?>,
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_WIDTH'); ?></strong>
							<?php echo $area->width; ?>,
							<strong><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_HEIGHT'); ?></strong>
							<?php echo $area->height; ?>
						</td>
						<td>
							<button type="button" class="btn btn-danger delete" onclick="removeArea(<?php echo $area->reddesign_area_id; ?>);">
								<i class="icon-minus icon-white"></i>
								<span><?php echo JText::_('COM_REDDESIGN_COMMON_REMOVE'); ?></span>
							</button>
						</td>
					</tr>
				<?php endforeach ?>
			<?php else : ?>
				<tr>
					<td colspan="5">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>