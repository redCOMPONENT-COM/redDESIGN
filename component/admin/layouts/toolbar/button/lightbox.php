<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JHtml::_('behavior.modal');

$data = $displayData;

if (!isset($data['button']))
{
	throw new InvalidArgumentException(JText::_('COM_REDDESIGN_BUTTON_NOT_PASSED'));
}

/** @var RToolbarButtonStandard $button */
$button = $data['button'];

$text = $button->getText();
$iconClass = $button->getIconClass();
$class = $button->getClass();

$url = $button->getUrl();
$rel = $button->getRel();

// Get the button class.
$btnClass = 'btn modal ';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}
?>

<a href="<?php echo $url ?>" class="<?php echo $btnClass ?>" rel="<?php echo $rel; ?>" style="left: 0; width: 85px; box-shadow: none;">
	<?php if (!empty($iconClass)) : ?>
		<i class="<?php echo $iconClass ?>"></i>
	<?php endif; ?>

	<?php echo $text ?>
</a>
