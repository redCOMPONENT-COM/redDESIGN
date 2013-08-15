<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
$tagPos = preg_match($pattern, $this->item->description);

if ($tagPos != 0)
{
	$intro_description = preg_split($pattern, $this->item->description, 2);
	$this->item->description = $intro_description[0].$intro_description[1];
}
?>

<div class="row-fluid">
	<div class="span8">
		<div class="media">
			<?php if ($this->item->sample_thumb) : ?>
				<a
					class="pull-left modal"
					href="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/thumbnails/' . $this->item->sample_thumb); ?>">
					<img
						class="media-object product-thumbnail"
						alt="<?php echo $this->item->title ?>"
						src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/' . $this->item->sample_image); ?>">
				</a>
			<?php endif; ?>
			<div class="media-body">
				<?php echo $this->item->description; ?>
			</div>
		</div>
	</div>
	<div class="span3 offset1">
		<div class="total well">
			<h5><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_PRODUCT_TOTAL'); ?>: <span id="total"></span></h5>
			<p>
				<button type="submit" class="btn btn-primary" id="orderDesign"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BUTTON_ORDER'); ?></button>
			</p>
		</div>
	</div>
	<?php if (!empty($this->relatedDesigntypes)) : ?>
	<div id="related-designtypes" class="span12">
		<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_RELATED_DESIGNTYPES'); ?> |
		<?php foreach ($this->relatedDesigntypes as $relatedDesigntype) : ?>
			<a id="related-designtype<?php echo $relatedDesigntype->reddesign_designtype_id; ?>"
			   href="<?php echo JRoute::_('index.php?option=com_reddesign&id=' . $relatedDesigntype->reddesign_designtype_id); ?>">
				<?php echo $relatedDesigntype->title; ?>
			</a> |
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
