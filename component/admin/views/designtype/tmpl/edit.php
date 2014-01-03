<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

JHTML::_('behavior.framework');
JHtml::_('rjquery.select2', 'select');

JHTML::_('behavior.modal', 'a.jmodal');

RHelperAsset::load('jquery.imgareaselect.pack.js');
RHelperAsset::load('imgareaselect-animated.css');
?>


<?php if (!extension_loaded('gd') && !function_exists('gd_info')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_GD_LIBRARY'); ?></p>
<?php endif; ?>

<?php if (!extension_loaded('imagick')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
<?php endif; ?>

<ul class="nav nav-tabs">
	<li class="active">
		<a href="#general" id="generalLink" data-toggle="tab" onclick="clearAreaSelection();">
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_GENERAL_TAB'); ?>
		</a>
	</li>
	<?php if (!empty($this->item->id)) : ?>
		<li>
			<a href="#backgrounds"  id="backgroundsLink" data-toggle="tab" onclick="clearAreaSelection();">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS'); ?>
			</a>
		</li>
		<li>
			<a href="#design-areas" id="design-areasLink"  data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_DESIGN_AREAS'); ?>
			</a>
		</li>
	<?php endif; ?>
</ul>

<div id="my-tab-content" class="tab-content">
	<div class="tab-pane active" id="general">
		<form enctype="multipart/form-data"
			action="index.php?option=com_reddesign&task=designtype.edit&id=<?php echo $this->item->id; ?>"
			method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<input type="hidden" name="option" value="com_reddesign">
			<input type="hidden" name="view" value="designtype">
			<input type="hidden" name="task" value="">
			<?php echo $this->form->getInput('id'); ?>
			<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

			<div id="basic_configuration" class="span12">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('name'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('state'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('state'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('fontsizer'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('fontsizer'); ?>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
		if (!empty($this->item->id))
		{
	?>
		<div class="tab-pane" id="backgrounds">
			<?php /*echo $this->loadTemplate('background');*/ ?>
			<?php /*echo $this->loadTemplate('backgrounds');*/ ?>
			<?php
				$backgroundModel = RModel::getAutoInstance('Background');
				$data = new stdClass;
				$data->item = new stdClass;
				$data->item->reddesign_designtype_id = $this->item->id;
				echo RLayoutHelper::render('edit', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/background/tmpl');

				$data = new stdClass;
				$data->items = $this->backgrounds;

				echo RLayoutHelper::render('default', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/backgrounds/tmpl');
			?>

			<div id="backgroundForm"></div>
			<div id="backgroundsList"></div>
		</div>
		<div class="tab-pane" id="design-areas">
			<?php /*echo $this->loadTemplate('designareas');*/ ?>
			<?php
				$data = new stdClass;
				$data->items = $this->areas;
				$data->item = $this->item;
				$data->productionBackground = $this->productionBackground;
				$data->unit = $this->unit;
				$data->pxToUnit = $this->pxToUnit;
				$data->unitToPx = $this->unitToPx;
				$data->ratio = $this->ratio;
				$data->imageWidth = $this->imageWidth;
				$data->imageHeight = $this->imageHeight;
				$data->fontsOptions = $this->fontsOptions;
				$data->inputFieldOptions = $this->inputFieldOptions;
				echo RLayoutHelper::render('default', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/designareas/tmpl');
			?>
		</div>

	<?php
		}
	?>

</div>


<script type="text/javascript">
	(function($){
		$(document).ready(function () {
			//ajaxLoadBackgroundForm();
			//ajaxLoadBackgrounds();
		});
	})(jQuery);
	/**
	 * Function to clear area selection drawn over other tabs due to auto zIndex.
	 * Without this function, area selections apear on top of all tabs.
	 */
	function clearAreaSelection()
	{
		var imageAreaSelection = jQuery("img#background").imgAreaSelect({ instance: true });

		if ((typeof imageAreaSelection != null) && (typeof imageAreaSelection != 'undefinded'))
		{
			imageAreaSelection.cancelSelection();
		}
	}

	/**
	 * Load Background Form
	 * @return void
	 */
	function ajaxLoadBackgroundForm()
	{
		var url = 'index.php?option=com_reddesign&task=background.ajaxBackgroundForm&designtype_id=<?php echo $this->item->id ?>';
		//url = 'index.php?option=com_reddesign&view=background&layout=edit&tmpl=component';
		console.log('ajaxLoadBackgrounds: ' + url);
		jQuery('#addBgBtn').parent().hide();
		// Perform the ajax request
		jQuery.ajax({
			url: url
		}).done(function (data) {
			jQuery('#backgroundForm').html(data);
		});
	}

	/**
	 * Load Backgrounds list
	 * @return void
	 */
	function ajaxLoadBackgrounds()
	{
		var url = 'index.php?option=com_reddesign&task=backgrounds.ajaxBackgrounds&designtype_id=<?php echo $this->item->id ?>';
		console.log('ajaxLoadBackgrounds: ' + url);
		jQuery('#addBgBtn').parent().hide();
		// Perform the ajax request
		jQuery.ajax({
			url: url
		}).done(function (data) {
			jQuery('#backgroundsList').html(data);
			jQuery('select').select2();
			jQuery('.hasTooltip').tooltip({"animation": true, "html": true, "placement": "top",
				"selector": false, "title": "", "trigger": "hover focus", "delay": 0, "container": false});

			// JModal
			SqueezeBox.initialize({});
			SqueezeBox.assign($$('a.jmodal'), {
				parse: 'rel'
			});
			console.log('SqueezeBox');
		});
	}


</script>
