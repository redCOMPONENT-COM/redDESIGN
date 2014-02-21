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
	$this->displayedBackground = $displayData->displayedBackground;
	$this->backgrounds = $displayData->backgrounds;
	$this->designType = $displayData->designType;
	$this->displayedProductionBackground = $displayData->displayedProductionBackground;
	$this->displayedAreas = $displayData->displayedAreas;
	$this->product = $displayData->product;
}

?>
<h4 class="page-header"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_BACKGROUNDS_TITLE') ?></h4>
<div class="row-fluid">
	<ul id="backgrounds" class="thumbnails">

		<?php foreach($this->backgrounds as $background) : ?>
			<li>
				<div class="background-container">
					<div class="background-selection">
						<input type="radio"
						       class="price-modifier"
						       onChange ="changeBackground('<?php echo $background->property_id; ?>');"
						       id="background<?php echo $background->id;?>"
						       name="background[]"
						       value="<?php echo $background->id ?>"
							<?php if ($background->id == $this->displayedBackground->id) : ?>
								checked="checked"
							<?php endif; ?>
							/>
					</div>
					<div class="background-detail">
						<div class="background-thumbnail-container">
							<img src="<?php echo ReddesignHelpersBackground::getThumbnail($background->svg_file) ?>" alt="<?php echo $background->name; ?>" />
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
			   value="<?php echo $this->displayedProductionBackground->id;?>"
			/>

	</ul>
</div>
