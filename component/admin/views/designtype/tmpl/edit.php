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

$tab = JFactory::getApplication()->input->get('tab', '');

switch ($tab)
{
	case 'backgrounds':
		$generalTabClass = '';
		$backgroundTabClass = 'active';
		$areaTabClass = '';
		break;

	case 'design-areas':
		$generalTabClass = '';
		$backgroundTabClass = '';
		$areaTabClass = 'active';
		break;

	default:
		$generalTabClass = 'active';
		$backgroundTabClass = '';
		$areaTabClass = '';
		break;
}

?>


<?php if (!extension_loaded('gd') && !function_exists('gd_info')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_GD_LIBRARY'); ?></p>
<?php endif; ?>

<?php if (!extension_loaded('imagick')) : ?>
	<p class="alert"><?php echo JText::_('RED_REDDESIGN_CPANEL_ERROR_CANT_FIND_IMAGICK_LIBRARY'); ?></p>
<?php endif; ?>

<ul class="nav nav-tabs">
	<li class="<?php echo $generalTabClass; ?>">
		<a href="#general" id="generalLink" data-toggle="tab">
			<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_GENERAL_TAB'); ?>
		</a>
	</li>
	<?php if (!empty($this->item->id)) : ?>
		<li class="<?php echo $backgroundTabClass; ?>">
			<a href="#backgrounds"  id="backgroundsLink" data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS'); ?>
			</a>
		</li>
		<li class="<?php echo $areaTabClass; ?>">
			<a href="#design-areas" id="design-areasLink"  data-toggle="tab">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_NAV_DESIGN_AREAS'); ?>
			</a>
		</li>
	<?php endif; ?>
</ul>

<div id="my-tab-content" class="tab-content">
	<div class="tab-pane <?php echo $generalTabClass; ?>" id="general">
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
		<div class="tab-pane <?php echo $backgroundTabClass; ?>" id="backgrounds">
			<div class="row">
				<div class="span2 offset10">
					<input type="button" class="btn btn-primary" id="addBgBtn" value="<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_BACKGROUNDS_HIDE_FORM'); ?>"/>
				</div>
			</div>

			<div id="backgroundForm">
				<?php
				$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true));
				$data = new stdClass;
				$data->item = new stdClass;
				$data->item->designtype_id = $this->item->id;
				$data->model = $backgroundModel;
				echo RLayoutHelper::render('edit', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/background/tmpl');
				?>
			</div>
			<div id="backgroundsList">
				<?php
				$data = new stdClass;
				$data->items = $this->backgrounds;
				$data->designtype_id = $this->item->id;
				echo RLayoutHelper::render('default', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/backgrounds/tmpl');
				?>
			</div>
		</div>
		<div class="tab-pane <?php echo $areaTabClass; ?>" id="design-areas">
			<div id="areaForm">
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
					$data->item->designtype_id = $this->item->id;
					$data->params = $this->params;
					echo RLayoutHelper::render('edit_area_js', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/area/tmpl');
					echo RLayoutHelper::render('edit', $data, $basePath = JPATH_ROOT . '/administrator/components/com_reddesign/views/area/tmpl');
				?>
			</div>
		</div>

	<?php
		}
	?>

</div>
