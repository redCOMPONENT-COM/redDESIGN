<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

?>
{redDESIGN:ButtonCustomizeIt}
{redDESIGN:AreasTitle}

{redDESIGN:AreasLoopStart}
<div class="row">{redDESIGN:AreaTitle}</div>
<div class="row">
	<div class="product_InputText col-xs-6">{redDESIGN:InputTextLabel} {redDESIGN:InputText}</div>
	<div class="product_ChooseFont col-xs-6">{redDESIGN:ChooseFontLabel} {redDESIGN:ChooseFont}</div>
</div>
<div class="product_ChooseFontSize">
	<div class="ChooseFontSize">{redDESIGN:ChooseFontSizeLabel} {redDESIGN:ChooseFontSize}</div>
	<div class="ChooseHorizontalAlignment">{redDESIGN:ChooseHorizontalAlignment}</div>
	<div class="ChooseVerticalAlignment">{redDESIGN:ChooseVerticalAlignment}</div>
</div>
<div class="product_ChooseColor">{redDESIGN:ChooseColorLabel} {redDESIGN:ChooseColor}</div>
<div>{redDESIGN:ChooseClipartLabel}</div>
<div class="product_ChooseClipart">{redDESIGN:ChooseClipart}</div>
<div class="clearfix"></div>
{redDESIGN:AreasLoopEnd}