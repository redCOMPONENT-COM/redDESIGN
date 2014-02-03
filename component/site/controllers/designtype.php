<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;


/**
 * Designtype Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignControllerDesigntype extends JController
{
	/**
	 * Display designtype via AJAX.
	 *
	 * @return void
	 */
	public function ajaxLoadDesigntype()
	{
		$app = JFactory::getApplication();
		$displayData = new JObject;
		$propertyId = $app->input->getInt('propertyId', 0);

		$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true), 'com_reddesign');
		$designTypeModel = RModel::getAdminInstance('Designtype', array('ignore_request' => true), 'com_reddesign');
		$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
		$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true), 'com_reddesign');

		$displayedBackground = $backgroundModel->getItemByProperty($propertyId);
		$displayData->designType = $designTypeModel->getItem($displayedBackground->designtype_id);
		$displayData->displayedProductionBackground = $designTypeModel->getProductionBackground($displayedBackground->designtype_id);

		$areasModel->setState('filter.background_id', $displayData->displayedProductionBackground->id);
		$displayData->displayedAreas = $areasModel->getItems();

		$displayData->fonts = $fontsModel->getItems();

		$htmlAreas = RLayoutHelper::render('default_areas', $displayData, $basePath = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl');

		if (JFile::exists(JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_reddesign/designtype/areas_tmpl.php'))
		{
			$path = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_reddesign/designtype/areas_tmpl.php';
			$layoutFile = file_get_contents($path, null, null, 256);
		}
		else
		{
			$path = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl/areas_tmpl.php';
			$layoutFile = file_get_contents($path, null, null, 256);
		}

		$areasOutput = '';

		$areasLoopTemplate = explode('{redDESIGN:AreasLoopStart}', $layoutFile);
		$areasLoopTemplate = explode('{redDESIGN:AreasLoopEnd}', $areasLoopTemplate[1]);
		$areasLoopTemplate = $areasLoopTemplate[0];

		if (!empty($displayData->displayedAreas))
		{
			foreach ($displayData->displayedAreas as $area)
			{
				$areasLoopTemplateInstance = $areasLoopTemplate;

				// Get area specific content.
				$areaHtml = explode('{RedDesignBreakDesignArea' . $area->id . '}', $htmlAreas);
				$areaHtml = $areaHtml[1];

				// Get specific area title.
				$htmlElement = explode('{RedDesignBreakDesignAreaTitle}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:AreaTitle}", $htmlElement, $areasLoopTemplateInstance);

				// Get input text label.
				$htmlElement = explode('{RedDesignBreakDesignAreaInputTextLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:InputTextLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get input text.
				$htmlElement = explode('{RedDesignBreakDesignAreaInputText}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:InputText}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font input.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFont}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFont}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font size label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontSizeLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontSizeLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose font size input.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseFontSize}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseFontSize}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColorLabel}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColorLabel}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColor}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColor}", $htmlElement, $areasLoopTemplateInstance);

				// Get choose color label.
				$htmlElement = explode('{RedDesignBreakDesignAreaChooseColor}', $areaHtml);
				$htmlElement = $htmlElement[1];
				$areasLoopTemplateInstance = str_replace("{redDESIGN:ChooseColor}", $htmlElement, $areasLoopTemplateInstance);

				$areasOutput .= $areasLoopTemplateInstance;
			}
		}

		echo $areasOutput;

		$app->close();
	}

	/**
	 * Gets background on given property ID and sends background object via AJAX.
	 *
	 *  @return void
	 */
	public function ajaxGetBackground()
	{
		$app = JFactory::getApplication();
		$propertyId = $app->input->getInt('propertyId', 0);
		$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true), 'com_reddesign');
		$displayedBackground = $backgroundModel->getItemByProperty($propertyId);

		$xml = simplexml_load_file(JURI::root() . 'media/com_reddesign/backgrounds/' . $displayedBackground->svg_file);
		$xmlInfo = $xml->attributes();
		$displayedBackground->width  = str_replace('px', '', $xmlInfo->width);
		$displayedBackground->height = str_replace('px', '', $xmlInfo->height);

		$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');
		$areasModel->setState('filter.background_id', $displayedBackground->id);
		$displayedAreas = $areasModel->getItems();

		$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayedAreas);
		$displayedBackground->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);

		echo json_encode($displayedBackground);

		$app->close();
	}
}
