<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;


$model = $this->getModel('designtype');
//echo RLayoutHelper::render('designtype.edit', array('view' => 'background', 'options' => array('designtype_id' => $this->item->id, 'id' => '')));
/*echo RLayoutHelper::render('designtype.background', array(
		'state' => $model->getState(),
		'item' => $this->item,
		'formName' => 'adminForm',
		'action' => JRoute::_('index.php?option=com_redshopb&view=department&model=users'),
		'return' => base64_encode('index.php?option=com_redshopb&view=department&layout=edit&id=')
	)
);*/
echo RLayoutHelper::render('designtype.background');
?>

<script type="text/javascript">
	jQuery(document).ready(
		function () {
			if(jQuery("#isPreviewBg").is(":checked"))
			{
				jQuery("#isDefaultPreviewContainer").show();
				jQuery("#useCheckerboardContainer").show();
			}
			else
			{
				jQuery("#isDefaultPreviewContainer").hide();
				jQuery("#useCheckerboardContainer").hide();
			}

			jQuery("#isPreviewBg").change(function() {
				if(jQuery("#isPreviewBg").is(":checked"))
				{
					jQuery("#isDefaultPreviewContainer").show();
					jQuery("#useCheckerboardContainer").show();
				}
				else
				{
					jQuery("#isDefaultPreviewContainer").hide();
					jQuery("#isDefaultPreview").attr('checked', false);
					jQuery("#useCheckerboardContainer").hide();
					jQuery("#useCheckerboard").attr('checked', false);
				}
			});

			jQuery(document).on('click', '#saveBgBtn',
				function () {
					jQuery('#background').submit();
				}
			);

			jQuery(document).on('click', '#cancelBgBtn',
				function () {
					jQuery("#backgroundTitle").html("<?php echo JText::_('COM_REDDESIGN_BACKGROUND_TITLE'); ?>");
					jQuery("#reddesign_background_id").val('');
					jQuery("#bg_title").val('');
					jQuery("#bg_enabled").val('1');

					jQuery('#backgroundForm').fadeOut("fast");
					jQuery('#addBgBtn').parent().fadeIn("fast");
				}
			);
		});
</script>
