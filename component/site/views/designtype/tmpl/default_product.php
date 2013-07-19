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

<div class="row">
	<div class="span5">
		<img id="background"
			 src="<?php echo FOFTemplateUtils::parsePath('media://com_reddesign/assets/designtypes/' . $this->item->sample_image); ?>"/>
	</div>
	<div class="span4">
		<?php echo $this->item->description; ?>
	</div>
</div>