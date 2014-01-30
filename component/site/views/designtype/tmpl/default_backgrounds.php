<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

if (isset($displayData))
{
	/*$this->item = $displayData->item;
	$this->backgrounds = $displayData->backgrounds;
	$this->relatedDesignTypeIds = $displayData->relatedDesignTypeIds;
	$this->productionBackground = $displayData->productionBackground;
	$this->attributesBackgrounds = $displayData->attributesBackgrounds;*/

	$this->displayedBackground = $displayData->displayedBackground;
	$this->designType = $displayData->designType;
	$this->backgrounds = $displayData->backgrounds;
}

$background_id = null;

$input = JFactory::getApplication()->input;

?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_FRAMES_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="frames" class="thumbnails">

		<?php foreach($this->backgrounds as $background) : ?>
			<li>
				<div class="background-container">
					<div class="background-selection">
						<input type="radio"
						       class="price-modifier"
						       onChange ="setBackground(<?php echo $background->id;?>);"
						       id="frame<?php echo $background->id;?>"
						       name="frame"
						       value="<?php echo $background->id ?>"
							<?php if ($background->id == $this->displayedBackground->id) : ?>
								checked="checked"
							<?php endif; ?>
							/>
					</div>
					<div class="background-detail">
						<div class="background-thumbnail-container">
							<object type="image/svg+xml"
							        data="<?php echo JURI::root() . 'media/com_reddesign/backgrounds/' . $background->svg_file; ?>"
							        width="150"
							        height="150"
								>
								<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BROWSER_NOT_SUPPORTED_FALLBACK'); ?>
							</object>
						</div>
						<div class="pull-left">
							<h5><?php echo $background->name; ?></h5>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach; ?>

		<input type="hidden"
			   name="background_id"
			   id="background_id"
			   value="<?php echo $this->displayedBackground->id;?>"
			/>

		<input type="hidden"
			   name="production_background_id"
			   id="production_background_id"
			   value="<?php echo $this->productionBackground->id;?>"
			/>

	</ul>
</div>
