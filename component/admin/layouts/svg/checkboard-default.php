<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$width = empty($displayData['width']) ? 800 : $displayData['width'];
$height = empty($displayData['height']) ? 600 : $displayData['height'];
?>
<svg width="<?php echo $width; ?>px" height="<?php echo $height; ?>px" viewBox="0 0 18 12" preserveAspectRatio="xMinYMin slice">
	<rect style="stroke:none;fill:rgb(255,255,255)" x="0" y="0" width="18" height="12"/>
	<polygon style="stroke:none;fill:#CCCCCC;fill-rule:evenodd;" points=" 0,0 1,0 1,12 2,12 2,0 3,0 3,12 4,12 4,0 5,0 5,12 6,12 6,0 7,0 7,12 8,12 8,0 9,0 9,12 10,12 10,0 11,0 11,12 12,12 12,0 13,0 13,12 14,12 14,0 15,0 15,12 16,12 16,0 17,0 17,12 18,12 18,11 0,11 0,10 18,10 18,9 0,9 0,8 18,8 18,7 0,7 0,6 18,6 18,5 0,5 0,4 18,4 18,3 0,3 0,2 18,2 18,1 0,1"/>
</svg>