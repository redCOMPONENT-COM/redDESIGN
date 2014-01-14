<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// Set variables for using them in HMVC. For regular MVC $displayData can not be used.
$this->areas = $displayData->items;
$this->designtype_id = $displayData->item->designtype_id;
?>
