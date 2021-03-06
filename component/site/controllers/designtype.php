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
		}
		else
		{
			$path = JPATH_ROOT . '/components/com_reddesign/views/designtype/tmpl/areas_tmpl.php';
		}

		ob_start();
		include $path;
		$layoutFile = ob_get_contents();
		ob_end_clean();

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

				if (!empty($areaHtml[1]))
				{
					$areaHtml = $areaHtml[1];
				}
				else
				{
					$areaHtml = '';
				}

				$areasLoopTemplateInstance = ReddesignHelpersArea::parseAreaTemplateTags($areaHtml, $areasLoopTemplateInstance);

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

		$productionBackground = $designTypeModel->getProductionBackground($displayedBackground->designtype_id);
		$displayedBackground->productionBackgroundId = $productionBackground->id;

		$areasModel->setState('filter.background_id', $productionBackground->id);
		$displayedBackground->areas = $areasModel->getItems();

		$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($displayedBackground->areas);
		$displayedBackground->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);

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

		if ($categoryId == 0)
		{
			$items = $clipartsModel->getItems();

			if (!empty($items[0]))
			{
				$categoryId = $items[0]->categoryId;
				$clipartsModel = RModel::getAdminInstance('Cliparts', array('ignore_request' => true), 'com_reddesign');
			}
		}

		$clipartsModel->setState('filter.categoryId', $categoryId);
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
		$return = new JObject;
		$return->result = '';
		$return->message = '';
		$uploaded_file = '';

		$areaId = $app->input->getInt('areaId', 0);
		$file = $app->input->files->get('uploadClipartFile' . $areaId, array(), 'array');

		if (!empty($file) && isset($file['name']) && !empty($file['type']))
		{
			$folderPath = JPATH_SITE . '/media/com_reddesign/cliparts/uploaded/';

			$allowedExtensions = 'svg,jpg,jpeg,png,gif,SVG,JPG,JPEG,PNG,GIF';
			$allowedMimeTypes = 'image/svg+xml,image/jpeg,image/jpg,image/jp_,application/jpg,application/x-jpg,image/pjpeg,image/pipeg,image/vnd.swiftview-jpeg,image/png,application/png,application/x-png,image/x-xbitmap,image/gif,image/x-xbitmap,image/gi_';

			$uploaded_file = ReddesignHelpersFile::uploadFile(
								$file,
								'cliparts/uploaded/',
								$config->getMaxSVGFileSize(),
								$allowedExtensions,
								$allowedMimeTypes
							);

			if (JFile::exists($folderPath . $uploaded_file['mangled_filename']))
			{
				if (JFile::getExt($uploaded_file['mangled_filename']) != 'svg')
				{
					// Check DPI
					$imageResolution = $this->getDpi($folderPath . $uploaded_file['mangled_filename']);
					$minimumDpi = $config->getMinimumUploadDpi();

					if ($imageResolution['x'] < $minimumDpi || $imageResolution['y'] < $minimumDpi)
					{
						$return->result = 'tooSmallDpi';
						$return->message = JText::sprintf(
							'COM_REDDESIGN_DESIGNTYPE_CLIPART_UPLOAD_DPI_TO_SMALL',
							$imageResolution['x'],
							$config->getMinimumUploadDpi()
						);
					}
				}
			}
		}

		if (empty($return->message))
		{
			$return->result = RLayoutHelper::render(
				'clipart.upload',
				array('areaId' => $areaId, 'file' => $uploaded_file),
				JPATH_ROOT . '/administrator/components/com_reddesign/layouts'
			);
		}

		echo json_encode($return);

		$app->close();
	}

	/**
	 * Gets image DPI.
	 *
	 * @param   string  $filename  Name of an image.
	 *
	 * @return array
	 */
	private function getDpi($filename)
	{
		$a = fopen($filename, 'r');
		$string = fread($a, 20);
		fclose($a);

		$data = bin2hex(substr($string, 14, 4));
		$x = substr($data, 0, 4);
		$y = substr($data, 0, 4);

		return array('x' => hexdec($x), 'y' => hexdec($y));
	}
}
