<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$items = $displayData['items'];
// @Todo add more Different font sources ex:
//url("riesling-webfont.eot?#iefix") format("embedded-opentype"),
//		url("riesling-webfont.woff") format("woff"),
//		url("riesling-webfont.ttf") format("truetype"),
//		url("riesling-webfont.svg#RieslingRegular") format("svg");
if (!empty($items)): ?>
	<?php foreach ($items as $item) :
		$name = $item->name;
		$fontFile = JUri::root() . 'media/com_reddesign/fonts/' . $item->font_file;
		$format = ReddesignHelpersFont::getFontExtensionFormatName(JFile::getExt($item->font_file));
		?>
		@font-face {
			font-family: "<?php echo $name; ?>";
			src: url("<?php echo $fontFile; ?>") format("<?php echo $format; ?>");
		}
	<?php endforeach; ?>
<?php endif;
