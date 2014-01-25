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
 * Design Type Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerDesigntype extends RControllerForm
{
	/**
	 * Saves design areas for AJAX request.
	 * ToDo: Make a control inside redDESIGN which will trigger fuction for deleting
	 * ToDo: all PDFs related to not paid orders.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxCustomizeDesign()
	{
		$orderItemId = $this->input->getInt('orderItemId', null);
		$orderId = $this->input->getInt('orderId', null);

		if ($orderItemId)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('redDesignData'));
			$query->from($db->quoteName('#__reddesign_orderitem_mapping'));
			$query->where($db->quoteName('order_item_id') . ' = ' . $orderItemId);
			$db->setQuery($query);
			$redDesignData = $db->loadResult();

			$redDesignData = json_decode($redDesignData);
			$preparedDesignData = $this->prepareDesignTypeData($redDesignData);
			$productionFileName = $this->createProductionFiles($preparedDesignData);

			$orderItemProductionFiles = new stdClass;
			$orderItemProductionFiles->order_item_id = $orderItemId;
			$orderItemProductionFiles->productionPdf = $productionFileName;
			$orderItemProductionFiles->productionEps = $productionFileName;
			$db->updateObject('#__reddesign_orderitem_mapping', $orderItemProductionFiles, 'order_item_id');

			$downloadFileName = 'production-file-' . $orderId . '-' . $orderItemId;

			$productionPdf = JPATH_ROOT . '/media/com_reddesign/backgrounds/orders/pdf/' . $productionFileName . '.pdf';
			echo '<a href="' . $productionPdf . '" download="' . $downloadFileName . '.pdf">' .
				JText::_('COM_REDDESIGN_COMMON_DOWNLOAD') .
				' PDF</a><br/><br/>';

			$productionEps = JPATH_ROOT . '/media/com_reddesign/backgrounds/orders/eps/' . $productionFileName . '.eps';
			echo '<a href="' . $productionEps . '" download="' . $downloadFileName . '.eps">' .
				JText::_('COM_REDDESIGN_COMMON_DOWNLOAD') .
				' EPS</a>';
		}
	}

	/**
	 * Prepares Design Type data in JSON format.
	 *
	 * @param   array  $redDesignData  Data from the request.
	 *
	 * @return string $designTypeJSON Design type data JSON encoded.
	 */
	public function prepareDesignTypeData($redDesignData = null)
	{
		if ($redDesignData)
		{
			// Get design type data.
			$designTypeModel = RModel::getAdminInstance('DesignType', array('ignore_request' => true));
			$designType      = $designTypeModel->getItem($redDesignData->designtype_id);

			$data = array();
			$data['designType'] = $designType;

			// Get Background Data
			$backgroundModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
			$data['designBackground'] = $backgroundModel->getItem($redDesignData->production_background_id);

			// Get designAreas
			$data['designAreas'] = array();

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_area_id'));
			$query->from($db->quoteName('#__reddesign_areas'));
			$query->where($db->quoteName('background_id') . ' = ' . $redDesignData->production_background_id);
			$db->setQuery($query);
			$areaIds = $db->loadColumn();

			foreach ($areaIds as $areaId)
			{
				$area = array();
				$area['id'] = $areaId;

				$key = 'fontArea' . $areaId;
				$area['fontTypeId'] = $redDesignData->$key;

				$key = 'colorCode' . $areaId;
				$area['fontColor'] = $redDesignData->$key;

				$key = 'fontSize' . $areaId;

				if (!empty($redDesignData->$key))
				{
					$area['fontSize'] = $redDesignData->$key;
				}

				$key = 'textArea_' . $areaId;
				$area['textArea'] = $redDesignData->$key;

				$data['designAreas'][] = $area;
			}

			$data['autoSizeData'] = json_decode($redDesignData->autoSizeData);
			$data['customUserWidth'] = $redDesignData->customUserWidth;
			$data['customUserHeight'] = $redDesignData->customUserHeight;
			$data['enteredDimensionunit'] = $redDesignData->enteredDimensionunit;

			return $data;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Create ProductPDF file for redDesign
	 *
	 * @param   array  $data  An array that holds design information
	 *
	 * @return   string  $pdfFileName  Newly generate PDF file name.
	 */
	public function createProductionFiles($data)
	{
		// Get component Params
		$params = JComponentHelper::getParams('com_reddesign');

		$designTypeModel = RModel::getAdminInstance('DesignType', array('ignore_request' => true));
		$designTypeItem = $designTypeModel->getItem($data['designBackground']->designtype_id);

		// Create production PDF file name
		$userId = JFactory::getUser()->id;
		$mangledname  = explode('.', $data['designBackground']->svg_file);
		$mangledname  = $mangledname[0];

		// Get a (very!) randomised name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverkey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverkey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $mangledname . microtime() . $serverkey;

		if (function_exists('sha256'))
		{
			$mangledname = sha256($sig);
		}
		elseif (function_exists('sha1'))
		{
			$mangledname = sha1($sig);
		}
		else
		{
			$mangledname = md5($sig);
		}

		$productionFileName = $userId . '-' . date('d-m-y-H-i-s') . '-' . $mangledname;

		$areas = $data['designAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epsTextFile = '';

		$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->svg_file;
		$previewFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->image_path;
		$pdfFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/';
		$epsFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/eps/';

		// Read EPS.
		$im = new Imagick;
		$im->readImage($epsFileLocation);
		$dimensions = $im->getImageGeometry();
		$imageWidth = $dimensions['width'];
		$imageHeight = $dimensions['height'];

		// Get unit setting for padding conversion.
		$unit = $params->get('unit', 'px');

		// Check for user entered dimension
		if (isset($data['customUserWidth']) && !empty($data['customUserWidth']))
		{
			// CM is default unit entered by user TODO: OR NOT?
			$imageWidth = $data['customUserWidth'] * 28.346456514;
		}

		// Check for user entered dimension
		if (isset($data['customUserHeight']) && !empty($data['customUserHeight']))
		{
			// CM is default unit entered by user TODO: OR NOT?
			$imageHeight = $data['customUserHeight'] * 28.346456514;
		}

		// Read preview size, for scaling.
		$previewImageSize = getimagesize($previewFileLocation);

		// Scaling ratio.
		$ratio = $previewImageSize[0] / $imageWidth;

		switch ($unit)
		{
			case 'cm':
				$unitToPx = '28.346456514';
				break;
			case 'mm':
				$unitToPx = '2.834645651';
				break;
			default:
				$unitToPx = '1';
				break;
		}

		// Default DPI is 72.
		$pdfLeftMargin = $params->get('productionFilePadding', 10) * $unitToPx;
		$pdfTopMargin = $pdfLeftMargin;

		foreach ($areas as $area)
		{
			if ($area['fontTypeId'])
			{
				$fontModel = RModel::getAdminInstance('Font', array('ignore_request' => true));
				$fontType = $fontModel->getItem($area['fontTypeId']);

				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $fontType->font_file))
				{
					$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/fonts/' . $fontType->font_file;
				}
			}

			$areaModel = RModel::getAdminInstance('Areas', array('ignore_request' => true));
			$areaItem = $areaModel->getItem($area['id']);

			/*
			 * Scale back all used measures by using calculated ratio.
			 * PHP_ROUND_HALF_DOWN is used because test show that measures are scaled in that way.
			 */
			$areaItem->width = round($areaItem->width / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->height = round($areaItem->height / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->x1_pos = round($areaItem->x1_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->y1_pos = round($areaItem->y1_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->x2_pos = round($areaItem->x2_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);
			$areaItem->y2_pos = round($areaItem->y2_pos / $ratio, 0, PHP_ROUND_HALF_DOWN);

			$rgbTextColorBuffer = $this->hex2RGB('#' . $area['fontColor']);
			$rgbTextColor = round($rgbTextColorBuffer['red'] * (1 / 255), 2);
			$rgbTextColor .= ' ';
			$rgbTextColor .= round($rgbTextColorBuffer['green'] * (1 / 255), 2);
			$rgbTextColor .= ' ';
			$rgbTextColor .= round($rgbTextColorBuffer['blue'] * (1 / 255), 2);

			if ($designTypeItem->fontsizer == 'auto' || $designTypeItem->fontsizer == 'auto_chars')
			{
				$autoSizeDataArray = $data['autoSizeData'];
				$autoSizeData = null;

				foreach ($autoSizeDataArray as $autoSizeDataElement)
				{
					if ($autoSizeDataElement->reddesign_area_id == $areaItem->reddesign_area_id)
					{
						$autoSizeData = $autoSizeDataElement;
					}
				}

				$fontSize = 0;

				if ($autoSizeData->fontSize)
				{
					$fontSize = $autoSizeData->fontSize / $ratio;
				}

				$noOfLines = count($autoSizeData->stringLines);

				$bottomoffset = $imageHeight - $areaItem->y2_pos + $pdfTopMargin;

				$maxHeight = $autoSizeData->maxHeight;
				$offsetOverallArea = (($areaItem->height - ($fontSize * $maxHeight * $noOfLines)) / 2);
				$offsetOverallArea = $offsetOverallArea + $bottomoffset;

				/*
				 * In the POSTSCRIPT default coordinate system, the origin is in the lower left hand corner of the current page.
				 * As usual, x increases to the right. But, y increases upward!
				 */

				$offsetLeft = $areaItem->x1_pos + $pdfLeftMargin + ($areaItem->width / 2);

				for ($h = 0;$h < $noOfLines;$h++)
				{
					if ($noOfLines == 1)
					{
						// Because other parts of the algorithm doesn't respect em-square we have to take a constant like 0.15 (15%) of the font size
						// because there is some empty space between chars and em-square's border.
						$offsetTop = (($imageHeight - $areaItem->y2_pos) + $pdfTopMargin + ($areaItem->height / 2)) - (($fontSize / 2) - ($fontSize * 0.15));
					}
					else
					{
						$gap = 1;

						if ($noOfLines > 3)
						{
							$gap = ($fontSize * 1.1) / ($noOfLines - 1);
						}

						$gap = $gap / 2;

						$offsetTop = $offsetOverallArea - $gap + ($fontSize * $maxHeight * (($noOfLines - 1) - $h)) * 1.1;
					}

					$epsText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $fontSize . "  scalefont setfont\n";
					$epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $fontSize . "  scalefont setfont\n";

					$epsText .= "\n$rgbTextColor setrgbcolor";
					$epsText .= "\ngsave\n";

					$epsAreaText .= "\n$rgbTextColor setrgbcolor";
					$epsAreaText .= "\ngsave\n";

					$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
					$epsAreaText .= "\n (" . $autoSizeData->stringLines[$h] . ") dup stringwidth pop 2 div neg 0 rmoveto true charpath ";
					$epsAreaText .= "\n fill";
					$epsAreaText .= "\n showpage";

					$epsText .= "\n $offsetLeft $offsetTop moveto";
					$epsText .= "\n (" . $autoSizeData->stringLines[$h] . ")";
					$epsText .= "\n cshow";
				}
			}
			else
			{
				$area['fontSize'] = round($area['fontSize'] / $ratio, 0);

				/*
				 * Calculate offset.
				 * In the POSTSCRIPT default coordinate system, the origin is in the lower left hand corner of the current page.
				 * As usual, x increases to the right. But, y increases upward!
				 */
				$alignmentPostScript = '';

				if ((int) $areaItem->textalign == 1)
				{
					// Left.
					$offsetLeft = $areaItem->x1_pos;
				}
				elseif ((int) $areaItem->textalign == 2)
				{
					// Right.
					$offsetLeft = $areaItem->x2_pos;
					$alignmentPostScript = "\n (" . $area['textArea'] . ") dup stringwidth pop neg 0 rmoveto";
				}
				else
				{
					// Center.
					$offsetLeft = $areaItem->x1_pos;
					$alignmentPostScript = "\n (" . $area['textArea'] . ") dup stringwidth pop 2 div 0 rmoveto";
				}

				$offsetTop = $areaItem->y2_pos + $pdfTopMargin;
				$offsetLeft += $pdfLeftMargin;

				$epsText .= $epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $area['fontSize'] . "  scalefont setfont\n";

				$epsText .= "\n$rgbTextColor setrgbcolor";
				$epsText .= "\ngsave\n";

				$epsAreaText .= "\n$rgbTextColor setrgbcolor";
				$epsAreaText .= "\ngsave\n";

				$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
				$epsAreaText .= $alignmentPostScript;
				$epsAreaText .= "\n (" . $area['textArea'] . ") true charpath ";

				$epsAreaText .= "\n fill";
				$epsAreaText .= "\n showpage";

				$epsText .= "\n $offsetLeft $offsetTop moveto";
				$epsText .= "\n (" . $area['textArea'] . ")";
				$epsText .= "\n cshow";
			}
		}

		$epsText .= "\ngrestore\n";

		$tmpEpsFile = $epsFilePath . "tmp_" . $productionFileName . ".eps";
		$tmpTextEpsFile = $epsFilePath . "tmptext_" . $productionFileName . ".eps";

		$tempFile = "%!PS";
		$tempFile .= "\n%%Creator:redDESIGN";
		$tempFile .= "\n%%Title:" . $productionFileName . ".pdf";
		$tempFile .= "\n%%LanguageLevel: 3";
		$tempFile .= "\n%%DocumentData: Clean7Bit";
		$tempFile .= "\n%%EndComments";
		$tempFile .= "\n";
		$tempFile .= "\n%%BeginProlog";
		$tempFile .= "\n/BeginEPSF {";
		$tempFile .= "\n/EPSFsave save def";
		$tempFile .= "\ncount /OpStackSize exch def";
		$tempFile .= "\n/DictStackSize countdictstack def";
		$tempFile .= "\n% turn off showpage";
		$tempFile .= "\n/showpage {} def";
		$tempFile .= "\n% set up default graphics state";
		$tempFile .= "\n0 setgray 0 setlinecap";
		$tempFile .= "\n1 setlinewidth 0 setlinejoin";
		$tempFile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$tempFile .= "\n/languagelevel where";
		$tempFile .= "\n{pop languagelevel 1 ne";
		$tempFile .= "\n{false setstrokeadjust false setoverprint} if";
		$tempFile .= "\n} if";
		$tempFile .= "\n} bind def";
		$tempFile .= "\n";
		$tempFile .= "\n/EndEPSF {";
		$tempFile .= "\ncount OpStackSize sub";
		$tempFile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$tempFile .= "\ncountdictstack DictStackSize sub";
		$tempFile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$tempFile .= "\nEPSFsave restore";
		$tempFile .= "\n} bind def";
		$tempFile .= "\n";
		$tempFile .= "\n%%EndProlog";
		$tempFile .= "\n%%Page: 1 1";
		$tempFile .= "\n/pagesave save def";
		$tempFile .= "\n";
		$tempFile .= "\n 0 0 translate";

		if (file_exists($epsFileLocation))
		{
			$tempFile .= "\nBeginEPSF";
			$tempFile .= "\n 0 0 translate";
			$tempFile .= "\n%%BeginDocument: danske.eps";
			$tempFile .= "\n(" . $epsFileLocation . ") run";
			$tempFile .= "\n%%EndDocument";
			$tempFile .= "\nEndEPSF";
		}

		$tempFile .= "\npagesave restore showpage";

		// Create temp eps file for reading bounding box...
		$tempFile .= "\n%%EOF";

		$tmpEpsImage = $epsFilePath . "tmp_eps_" . $productionFileName . ".eps";
		$tmpBound = $epsFilePath . "tmp_bound_" . $productionFileName . ".ps";
		$tmpResizeEPS = $epsFilePath . "tmp_resizeeps_" . $productionFileName . ".eps";

		$fp = fopen($tmpEpsImage, "w");
		fwrite($fp, $tempFile);
		fclose($fp);

		$cmd = "gs -dBATCH -dNOPAUSE -sOutputFile=$tmpBound -sDEVICE=ps2write  \-c '<< /PageSize
				[$imageWidth $imageHeight]  >> setpagedevice'  -f" . $tmpEpsImage;
		exec($cmd);

		$imageBound = $this->readBound($tmpBound);
		$epsFile  = "%!PS-Adobe-3.1 EPSF-3.1";
		$epsFile .= "\n%%Creator:redDESIGN";
		$epsFile .= "\n%%Title:" . $productionFileName . ".pdf";
		$epsFile .= "\n%%LanguageLevel: 3";
		$epsFile .= "\n%%DocumentData: Clean7Bit";
		$epsFile .= "\n%%EndComments";
		$epsFile .= "\n";
		$epsFile .= "\n%%BeginProlog";
		$epsFile .= "\n/BeginEPSF {";
		$epsFile .= "\n/EPSFsave save def";
		$epsFile .= "\ncount /OpStackSize exch def";
		$epsFile .= "\n/DictStackSize countdictstack def";
		$epsFile .= "\n% turn off showpage";
		$epsFile .= "\n/showpage {} def";
		$epsFile .= "\n% set up default graphics state";
		$epsFile .= "\n0 setgray 0 setlinecap";
		$epsFile .= "\n1 setlinewidth 0 setlinejoin";
		$epsFile .= "\n10 setmiterlimit [] 0 setdash newpath";
		$epsFile .= "\n/languagelevel where";
		$epsFile .= "\n{pop languagelevel 1 ne";
		$epsFile .= "\n{false setstrokeadjust false setoverprint} if";
		$epsFile .= "\n} if";
		$epsFile .= "\n} bind def";
		$epsFile .= "\n";
		$epsFile .= "\n/EndEPSF {";
		$epsFile .= "\ncount OpStackSize sub";
		$epsFile .= "\ndup 0 lt {neg {pop} repeat} {pop} ifelse";
		$epsFile .= "\ncountdictstack DictStackSize sub";
		$epsFile .= "\ndup 0 lt {neg {end} repeat} {pop} ifelse";
		$epsFile .= "\nEPSFsave restore";
		$epsFile .= "\n} bind def";
		$epsFile .= "\n";

		$epsFile .= "\n/x 1 def";
		$epsFile .= "\n/cshow		%  (str)  =>  ---";
		$epsFile .= "\n{ dup stringwidth pop -2 div 0 rmoveto show } bind def";
		$epsFile .= "\n/alignshow		%  (str)  =>  ---";
		$epsFile .= "\n	{dup stringwidth pop neg 0 rmoveto show} bind def";
		$epsFile .= "\n/nl { x currentpoint exch pop 16 sub moveto } bind def";
		$epsFile .= "\n%%EndProlog";
		$epsFile .= "\n%%Page: 1 1";
		$epsFile .= "\n/pagesave save def";
		$epsFile .= "\n";

		if (file_exists($epsFileLocation))
		{
			$epsFile .= "\nBeginEPSF";
			$epsFile .= "\n 0 0 translate";

			if ($imageBound[0] > 100)
			{
				$epsFile .= "\n 0 0 translate";
			}
			elseif ($imageBound[3] == 0)
			{
				$epsFile .= "\n 0 0 translate";
			}
			else
			{
				$epsFile .= "\n " . $pdfLeftMargin . " " . $pdfTopMargin . " translate";
			}

			$epsFile .= "\n% 0 0 " . ($imageWidth) . " " . ($imageHeight);

			$cmd = "gs -dSAFER -dBATCH -dNOPAUSE -dEPSFitPage ";
			$cmd .= "-sOutputFile=$tmpResizeEPS -sDEVICE=epswrite ";
			$cmd .= "\-c '<< /PageSize [$imageWidth $imageHeight] >> setpagedevice' -f $epsFileLocation";
			exec($cmd);

			$epsFile .= "\n%%BeginDocument: danske.eps";
			$epsFile .= "\n(" . $tmpResizeEPS . ") run";
			$epsFile .= "\n%%EndDocument";
			$epsFile .= "\nEndEPSF";
		}

		$imageWidth += (2 * $pdfLeftMargin);
		$imageHeight += (2 * $pdfTopMargin);

		$epsFile .= "\nBeginEPSF";
		$epsFile .= "\nclear";
		$epsFile .= "\n 0 0 translate";
		$epsTextFile .= $epsFile;
		$epsFile .= "\n%%BeginDocument: text.eps";
		$epsFile .= "\n" . $epsAreaText;
		$epsFile .= "\n%%EndDocument";
		$epsFile .= "\nEndEPSF";

		// Create temp eps file for reading bounding box...
		$epsFile .= "\n%%EOF";

		$epsTextFile .= "\n%%BeginDocument: text.eps";
		$epsTextFile .= "\n" . $epsText;
		$epsTextFile .= "\n%%EndDocument";
		$epsTextFile .= "\nEndEPSF";
		$epsTextFile .= "\n%%EOF";

		$fp = fopen($tmpEpsFile, "w");
		fwrite($fp, $epsFile);
		fclose($fp);

		$fp = fopen($tmpTextEpsFile, "w");
		fwrite($fp, $epsTextFile);
		fclose($fp);

		// Create pdf.
		ob_clean();

		$pdfFileName = $pdfFilePath . $productionFileName . ".pdf";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOCACHE -dEmbedAllFonts=true -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$pdfFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpEpsFile;
		exec($cmd);

		$epsFileName = $epsFilePath . $productionFileName . ".eps";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOCACHE -dEmbedAllFonts=true -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$epsFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpTextEpsFile;
		exec($cmd);

		if (file_exists($tmpTextEpsFile))
		{
			unlink($tmpTextEpsFile);
		}

		if (file_exists($tmpEpsFile))
		{
			unlink($tmpEpsFile);
		}

		if (file_exists($tmpEpsImage))
		{
			unlink($tmpEpsImage);
		}

		if (file_exists($tmpBound))
		{
			unlink($tmpBound);
		}

		if (file_exists($tmpResizeEPS))
		{
			unlink($tmpResizeEPS);
		}

		return $productionFileName;
	}

	/**
	 * Read BoundingArea of Image
	 *
	 * @param   string  $fname  location of image
	 *
	 * @return array
	 */
	private function readBound($fname)
	{
		$contentStr = "";

		if (!file_exists($fname))
		{
			return false;
		}

		$contents = file($fname);

		for ($f = 0; $f < count($contents); $f++)
		{
			if (strstr($contents[$f], "%%BoundingBox"))
			{
				$contentStr = $contents[$f];
				break;
			}
		}

		$box = explode(":", $contentStr);
		$boundingBox = explode(" ", trim($box[1]));

		return $boundingBox;
	}

	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param   string   $hexStr          (hexadecimal color value)
	 * @param   boolean  $returnAsString  (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param   string   $seperator       (to separate RGB values. Applicable only if second parameter is true.)
	 *
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	public function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
	{
		// Gets a proper hex string
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
		$rgbArray = array();

		if (strlen($hexStr) == 6)
		{
			// If a proper hex code, convert using bitwise operation. No overhead... faster.
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		elseif (strlen($hexStr) == 3)
		{
			// If shorthand notation, need some string manipulations.
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		}
		else
		{
			// Invalid hex color code.
			return false;
		}

		// Returns the rgb string or the associative array.
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
	}
}
