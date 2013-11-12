<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Designtype View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewDesigntype extends FOFViewHtml
{
	public $item;

	public $activeTab;

	public $document;

	public $areas;

	public $productionBackground;

	public $params;

	public $sizerOptions;

	public $backgrounds;

	public $ratio;

	public $colorCodes;

	public $inputFieldOptions;

	public $fonts;

	public $alignmentOptions;

	public $fontsOptions;

	public $unit;

	public $pxToUnit;

	public $unitToPx;

	public $imageWidth;

	public $imageHeight;

	public $backgroundTypeOptions;

	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$this->input->set('hidemainmenu', true);

		$model 						= $this->getModel();
		$this->item 				= $model->getItem();
		$this->activeTab 			= JFactory::getApplication()->input->getString('tab', 'general');
		$this->document 			= JFactory::getDocument();
		$this->areas 				= null;
		$this->productionBackground = null;
		$this->params				= JComponentHelper::getParams('com_reddesign');

		// Font sizer options for the general tab.
		$this->sizerOptions = array(
			JHTML::_('select.option',  'auto', JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS_AUTO')),
			JHTML::_('select.option',  'slider', JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS_SLIDER')),
			JHTML::_('select.option',  'dropdown_numbers', JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS_DROPDOWN_NUMBERS')),
			JHTML::_('select.option',  'dropdown_labels', JText::_('COM_REDDESIGN_DESIGNTYPE_FIELD_FONT_SIZE_CONTROLS_DROPDOWN_LABELS'))
		);

		// Related design types.
		$designtypesModel = FOFModel::getTmpInstance('Designtypes', 'ReddesignModel');
		$designtypes = $designtypesModel->getItemList();
		$designtypesOptions = array();

		foreach ($designtypes as $designtype)
		{
			if ($designtype->reddesign_designtype_id != $this->item->reddesign_designtype_id)
			{
				$designtypesOptions[] = JHtml::_('select.option', $designtype->reddesign_designtype_id, $designtype->title);
			}
		}

		// If it's not a new design
		if (!empty($this->item->reddesign_designtype_id))
		{
			// Get all the backgrounds that belongs to this Design Type item.
			$backgroundModel = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();

			$areas = array();

			foreach ($this->backgrounds as $background)
			{
				// Get the background image that has been selected to be the Production PDF file image.
				if ($background->isProductionBg)
				{
					$this->productionBackground = $background;

					$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $this->productionBackground->eps_file;
					$previewFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $this->productionBackground->image_path;

					// Read EPS size.
					$im = new Imagick;
					$im->readImage($epsFileLocation);
					$dimensions = $im->getImageGeometry();
					$this->imageWidth = $dimensions['width'];
					$this->imageHeight = $dimensions['height'];

					// Read preview size, for scaling.
					$previewImageSize = getimagesize($previewFileLocation);

					// Scaling ratio
					$this->ratio = $previewImageSize[0] / $this->imageWidth;

					// Get all areas existing in the database for this specific background.
					$areaModel = FOFModel::getTmpInstance('Area', 'ReddesignModel')->reddesign_background_id($background->reddesign_background_id);
					$areas = $areaModel->getItemList();
				}
			}

			$this->areas = $areas;

			$this->inputFieldOptions = array(
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTBOX')),
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTAREA'))
			);

			$this->alignmentOptions = array(
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_COMMON_SELECT')),
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_COMMON_LEFT')),
				JHtml::_('select.option', '2', JText::_('COM_REDDESIGN_COMMON_RIGHT')),
				JHtml::_('select.option', '3', JText::_('COM_REDDESIGN_COMMON_CENTER'))
			);

			// Get all fonts in the system to be choosen or not for the current design.
			$fontsModel = FOFModel::getTmpInstance('Font', 'ReddesignModel');
			$this->fonts = $fontsModel->getItemList();
			$this->fontsOptions = array();

			foreach ($this->fonts as $font)
			{
				$this->fontsOptions[] = JHtml::_('select.option', $font->reddesign_font_id, $font->title);
			}

			$this->backgroundTypeOptions = array(
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_PRODUCTION_BG')),
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_PREVIEW_BG'))
			);

			// Unit for measures.
			$this->unit = $this->params->get('unit', 'px');

			if ($this->unit == 'cm')
			{
				/**
				 * Default DPI in Imagick is used and it is 72 DPI.
				 * Thag gives us 1px = 0.035278cm.
				 */
				$this->pxToUnit = '0.035277778';

				// From above 1cm = 28,346456514px.
				$this->unitToPx = '28.346456514';
			}
			elseif ($this->unit == 'mm')
			{
				/**
				 * Default DPI in Imagick is used and it is 72 DPI.
				 * Thag gives us 1px = 0.35277778mm.
				 */
				$this->pxToUnit = '0.35277778';

				// From above 1mm = 2,834645651px.
				$this->unitToPx = '2.834645651';
			}
			else
			{
				$this->pxToUnit = '1';
				$this->unitToPx = '1';
			}
		}

		parent::display();
	}
}
