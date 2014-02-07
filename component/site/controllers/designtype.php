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

		if (!empty($areasLoopTemplate[1]))
		{
			$areasLoopTemplate = explode('{redDESIGN:AreasLoopEnd}', $areasLoopTemplate[1]);
			$areasLoopTemplate = $areasLoopTemplate[0];
		}
		else
		{
			$areasLoopTemplate = null;
		}

		if (!empty($displayData->displayedAreas) && !empty($areasLoopTemplate))
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
	 * @return void
	 */
	public function ajaxGetBackground()
	{
		$app = JFactory::getApplication();
		$propertyId = $app->input->getInt('propertyId', 0);

		$designTypeModel = RModel::getAdminInstance('Designtype', array('ignore_request' => true), 'com_reddesign');
		$backgroundModel = RModel::getAdminInstance('Background', array('ignore_request' => true), 'com_reddesign');
		$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true), 'com_reddesign');

		$displayedBackground = $backgroundModel->getItemByProperty($propertyId);
		$xml = simplexml_load_file(JURI::root() . 'media/com_reddesign/backgrounds/' . $displayedBackground->svg_file);
		$xmlInfo = $xml->attributes();
		$displayedBackground->width  = str_replace('px', '', $xmlInfo->width);
		$displayedBackground->height = str_replace('px', '', $xmlInfo->height);

		$areasModel->setState('filter.background_id', $displayedBackground->id);
		$displayedBackground->areas = $areasModel->getItems();

		$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayedBackground->areas);
		$displayedBackground->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);

		$productionBackground = $designTypeModel->getProductionBackground($displayedBackground->designtype_id);
		$displayedBackground->productionBackgroundId = $productionBackground->id;

		$designType = $designTypeModel->getItem($displayedBackground->designtype_id);
		$displayedBackground->designtype_name = $designType->name;

		echo json_encode($displayedBackground);

		$app->close();
	}

	/**
	 * Gets clipart bank
	 *
	 * @return void
	 */
	public function ajaxLoadClipartBank()
	{
		$app = JFactory::getApplication();
		$categoryId = $app->input->getInt('categoryId', 0);
		$search = $app->input->getString('search', '');
		$areaId = $app->input->getInt('areaId', 0);

		/** @var ReddesignModelCliparts $clipartsModel */
		$clipartsModel = RModel::getAdminInstance('Cliparts', array('ignore_request' => true), 'com_reddesign');

		if ($categoryId > 0)
		{
			$clipartsModel->setState('filter.categoryId', $categoryId);
		}
		else
		{
			$items = $clipartsModel->getItems();

			if (!empty($items) && count($items) > 0)
			{
				$categoryId = $items[0]->categoryId;
				$clipartsModel->setState('filter.categoryId', $categoryId);
			}
		}

		$clipartsModel->setState('filter.search_cliparts', $search);

		$formName = 'clipartBankForm';
		$pagination = $clipartsModel->getPagination();
		$pagination->set('formName', $formName);

		echo RLayoutHelper::render('clipart.bank', array(
				'state' => $clipartsModel->getState(),
				'items' => $clipartsModel->getItems(),
				'pagination' => $pagination,
				'areaId' => $areaId,
				'categoryId' => $categoryId,
				'search' => $search,
				'filter_form' => $clipartsModel->getForm(),
				'activeFilters' => $clipartsModel->getActiveFilters(),
				'formName' => $formName,
				'clipartsModel' => $clipartsModel,
			),
			JPATH_ROOT . '/administrator/components/com_reddesign/layouts'
		);

		$app->close();
	}

	/**
	 * Reads uploaded file and outputs file in specific format for selection
	 *
	 * @return void
	 */
	public function ajaxUploadCustomClipart()
	{
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();
		$config = ReddesignEntityConfig::getInstance();
		$return         = new stdClass;
		$success = false;

		$areaId = $app->input->getInt('areaId', 0);
		$file = $app->input->files->get('uploadClipartFile' . $areaId, array(), 'array');
		$return->clipartOutput = '';

		if (!empty($file) && isset($file['name']) && !empty($file['type']))
		{
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'cliparts/uploaded', $config->getMaxSVGFileSize(), 'svg');

			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/cliparts/uploaded/' . $uploaded_file['mangled_filename']))
			{
				$success = true;
			}
		}

		$return->success = $success;

		if ($success)
		{
			$return->clipartOutput = RLayoutHelper::render('clipart.upload', array(
					'areaId' => $areaId,
					'file' => $uploaded_file,
				),
				JPATH_ROOT . '/administrator/components/com_reddesign/layouts'
			);
		}

		echo $return->clipartOutput;

		$app->close();
	}
}
