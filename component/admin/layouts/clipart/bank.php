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
$items = $data['items'];
$areaId = $data['areaId'];
$categoryId = $data['categoryId'];
$search = $data['search'];
$config = ReddesignEntityConfig::getInstance();
$clipartPreviewWidth = $config->getMaxClipartPreviewWidth();
$clipartPreviewHeight = $config->getMaxClipartPreviewHeight();
$categories = JHtml::_('category.options', 'com_reddesign');

JHtml::_('rjquery.select2', 'select');
JHtml::_('rjquery.flexslider', '.flexslider', array(
		'slideshow' => false,
		'directionNav' => true,
		'minItems' => 4,
		'prevText' => '',
		'nextText' => '',
		'animation' => 'slide',
		'animationLoop' => false)
);
?>
<div class="row-fluid">
	<div class="span12">
		<legend><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_CLIPART_BANK'); ?></legend>
		<div class="filters">
			<div class="btn-wrapper input-append">
				<input
					type="text"
					name="clipartSearch"
					id="clipartSearch<?php echo $areaId; ?>"
					value="<?php echo $search; ?>"
					class="js-enter-submits">
				<button
					type="button"
					id="clipartSearchButton<?php echo $areaId; ?>"
					class="btn hasTooltip"
					onclick="loadClipartBank(<?php echo $areaId; ?>, true)"
					title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="icon-search"></i>
				</button>
			</div>

			<?php echo JHTML::_(
				'select.genericlist',
				$categories,
				'clipartCategoryId' . $areaId,
				'class="inputbox" id="clipartCategoryId' . $areaId . '" onchange="loadClipartBank(' . $areaId . ', true)" ',
				'value',
				'text',
				$categoryId
			); ?>

		</div>
		<div class="span12">
			<div class="flexslider2" id="clipartBank<?php echo $areaId ;?>">
				<ul class="slides">
					<?php foreach ($items as $clipart) : ?>
						<li>
							<div class="thumbnail clipart-container">
								<div
									class="thumbnailSVG-pointer"
									name="clipart<?php echo $areaId; ?>_<?php echo $clipart->id; ?>"
									style="width:<?php echo $clipartPreviewWidth; ?>px; height:<?php echo $clipartPreviewHeight; ?>px;"></div>
								<object
									id="clipartBank<?php echo $areaId ;?>_<?php echo $clipart->id; ?>"
									name="clipart<?php echo $areaId ;?>_<?php echo $clipart->id ;?>"
									class="thumbnailSVG"
									data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/' . $clipart->clipartFile; ?>"
									type="image/svg+xml">
								</object>
								<input
									type="radio"
									class="change-selected-clipart hide"
									name="selectedClipart<?php echo $areaId ?>"
									value="<?php echo $clipart->id; ?>"
									/><br />
								<?php echo $clipart->name ;?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="clearfix"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery('#clipartBank<?php echo $areaId ;?> .thumbnailSVG').each(function () {
		var svgThumbnail = document.getElementById(jQuery(this).attr('id'));
		svgThumbnail.addEventListener("load", function() {
			setSVGElementScale(this);
		});
		// Some elements are already loaded
		setSVGElementScale(svgThumbnail);
	});

</script>
