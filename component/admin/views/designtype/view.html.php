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
 * Background View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewDesigntype extends FOFViewHtml
{
	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$this->input->setVar('hidemainmenu', true);

		$model 						= $this->getModel();
		$this->item 				= $model->getItem();
		$this->activeTab 			= JFactory::getApplication()->input->getString('tab', 'general');
		$this->document 			= JFactory::getDocument();
		$this->areas 				= null;
		$this->productionBackground = null;
		$this->editor				= JFactory::getEditor();

		// If it's not a new design
		if (!empty($this->item->reddesign_designtype_id))
		{
			// Get all the backgrounds that belongs to this Design Type item.
			$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->backgrounds = $backgroundModel->getItemList();

			$areas = array();

			foreach ($this->backgrounds as $background)
			{
				// Get the background image that has been selected to be the Production PDF file image
				if ($background->isPDFbgimage)
				{
					$this->productionBackground = $background;

					// Get all areas existing in the database for this specific background
					$areaModel = FOFModel::getTmpInstance('Area', 'ReddesignModel')->reddesign_background_id($background->reddesign_background_id);
					$areas = $areaModel->getItemList();
				}
			}

			$this->areas = $areas;

			// Get allColor radio input type for each area
			$colorCodes = array();

			foreach ($this->areas as $area)
			{
				$colorCodes["color_" . $area->reddesign_area_id] = $area->color_code;
				$colorCode = $area->color_code;

				if ($colorCode != 1 || $colorCode != '1')
				{
					$colorCode = 0;
				}

				$colorCodes['allColor' . $area->reddesign_area_id] = JHTML::_('select.booleanlist',
																				'allColor' . $area->reddesign_area_id,
																				'class="inputbox" onclick="hideColorPicker(\'' . $area->reddesign_area_id . '\');"',
																				$colorCode
																				);
			}

			$this->colorCodes = $colorCodes;

			$this->alginmentOptions = array(
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_COMMON_SELECT')),
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_COMMON_LEFT')),
				JHtml::_('select.option', '2', JText::_('COM_REDDESIGN_COMMON_RIGHT')),
				JHtml::_('select.option', '3', JText::_('COM_REDDESIGN_COMMON_CENTER'))
			);

			// Get all fonts in the system to be choosen or not for the current design
			$fontsModel = FOFModel::getTmpInstance('Font', 'ReddesignModel');
			$this->fonts = $fontsModel->getItemList();
			$this->fontsOptions = array();

			foreach ($this->fonts as $font)
			{
				$this->fontsOptions[] = JHtml::_('select.option', $font->reddesign_font_id, $font->title);
			}

			// Get all the parts that belongs to this Design Type item.
			$partsModel = FOFModel::getTmpInstance('Part', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
			$this->parts = $partsModel->getItemList();

			$configParams = JComponentHelper::getParams('com_reddesign');
			$this->unit = $configParams->get('unit', 'px');

			if ($this->unit == 'cm')
			{
				// (1 px = 0.026458333cm)
				$this->pxToUnit = '0.026458333';

				// (1 cm = 37.795275591px)
				$this->unitToPx = '37.795275591';
			}
			elseif ($this->unit == 'mm')
			{
				// (1 px = 0.264583333mm)
				$this->pxToUnit = '0.264583333';

				// (1 mm = 3.779527559px)
				$this->unitToPx = '3.779527559';
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
