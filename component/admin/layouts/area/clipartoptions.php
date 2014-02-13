<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = $displayData;
$area = $data['area'];
$area->cliparts = ReddesignHelpersArea::getAreaFeaturedCliparts($area->id);
$selectedClipartIds = JArrayHelper::getColumn($area->cliparts, 'id');
$designType = $data['designType'];

JHtml::_('rjquery.select2', 'select');
?>
<div class="row-fluid">
<div class="span12 col-md12">
	<fieldset>
		<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_LEGEND_FONT_SETTINGS'); ?></legend>
		<div class="row-fluid">
			<div class="span4 col-md4">

				<div class="control-group">
					<label for="<?php echo 'areaFontAlignment' . $area->id; ?>" class="control-label" style="float:none;">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_ALIGNMENT_CLIPART') ?>
					</label>
					<div class="controls">
						<?php
						echo JHtml::_('select.genericlist',
							ReddesignHelpersArea::getAreaHorizontalAlignmentOptions(),
							'areaAlignment' . $area->id,
							'',
							'value',
							'text',
							$area->textalign
						);
						?>
					</div>
				</div>

				<div class="control-group">
					<label for="<?php echo 'areaVerticalAlignment' . $area->id; ?>" class="control-label" style="float:none;">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_VERTICAL_ALIGNMENT') ?>
					</label>
					<div class="controls">
						<?php
						echo JHtml::_('select.genericlist',
							ReddesignHelpersArea::getAreaVerticalAlignmentOptions(),
							'areaVerticalAlignment' . $area->id,
							'',
							'value',
							'text',
							$area->verticalAlign
						);
						?>
					</div>
				</div>

				<div class="control-group">
					<label for="<?php echo 'areaFonts' . $area->id; ?>" class="control-label" style="float:none;">
						<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_FEATURED_CLIPARTS') ?>
					</label>
					<div class="controls">
						<?php
						echo JHtml::_(
							'select.genericlist',
							ReddesignHelpersArea::getClipartsSelectOptions(),
							'areaCliparts' . $area->id . '[]',
							' multiple="multiple" ',
							'value',
							'text',
							$selectedClipartIds
						);
						?>
					</div>
				</div>

			</div>
			<div class="span7 col-md7 media">
				<?php echo JText::_('COM_REDDESIGN_CLIPART_PREVIEW');?><br />
				<?php foreach ($area->cliparts as $clipart) : ?>
					<div class="pull-left thumbnail">
						<?php echo $clipart->name ;?><br />
						<object
							id="clipart<?php echo $clipart->id; ?>"
							class="thumbnailSVG"
							data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/' . $clipart->clipartFile; ?>"
							type="image/svg+xml">
						</object>
					</div>
				<?php endforeach; ?>
				<div class="clearfix"></div>
				<br />
			</div>
		</div>

	</fieldset>
</div>

<div class="span6 col-md6 offset5">
	<button id="saveAreaClipartSettings<?php echo $area->id; ?>"
	        type="button"
	        class="btn btn-success"
	        data-loading-text="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_SAVE_AREA_SETTINGS'); ?>"
	        onclick="saveAreaClipartSettings(<?php echo $area->id . ',\'' . $area->name . '\''; ?>);">
			<span>
				<?php echo JText::_('COM_REDDESIGN_COMMON_SAVE'); ?>
			</span>
	</button>
	<button type="button"
	        class="btn"
	        onclick="showAreaSettings(<?php echo $area->id; ?>);">
			<span>
				<?php echo JText::_('COM_REDDESIGN_COMMON_CANCEL'); ?>
			</span>
	</button>
</div>
</div>
