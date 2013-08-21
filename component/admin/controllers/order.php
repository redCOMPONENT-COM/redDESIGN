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
 * Font Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerOrder extends FOFController
{
	/**
	 * Creates PDF production file on AJAX request.
	 *
	 * @return void
	 */
	public function createProductionFile()
	{
		JSession::checkToken('get') or jexit('Invalid Token');

		$productId = $this->input->getInt('productId', null);
		$productionFileName = $this->input->getString('productionFileName', '0');

		if (!empty($productId))
		{
			// Get JSON about design type from product record. It is previously saved by a plugin.
			JPluginHelper::importPlugin('reddesign');
			$dispatcher = JDispatcher::getInstance();
			$data = $dispatcher->trigger('getDesigntypeJSON', array($productId));
			$data = json_decode($data[0], true);

			if (!empty($data))
			{
				if (!JFile::exists(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/' . $productionFileName))
				{
					$data['productionFileName'] = $productionFileName;

					$this->createPdfProductionFile($data);
				}

				$productionFileName = FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/orders/pdf/' . $productionFileName);
			}
		}

		echo $productionFileName;
	}

	/**
	 * Create ProductPDF file for redDesign
	 *
	 * @param   array  $data  An array that holds design information
	 *
	 * @return  void
	 */
	public function createPdfProductionFile($data)
	{
		$productionFileName = $data['productionFileName'];

		$areas = $data['designAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epsTextFile = '';

		$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']['eps_file'];
		$previewFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']['image_path'];
		$pdfFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/';
		$epsFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/eps/';

		// Read EPS.
		$im = new Imagick;
		$im->readImage($epsFileLocation);
		$dimensions = $im->getImageGeometry();
		$imageWidth = $dimensions['width'];
		$imageHeight = $dimensions['height'];

		// Read preview size, for scaling.
		$previewImageSize = getimagesize($previewFileLocation);

		// Scaling ratio
		$ratio = $previewImageSize[0] / $imageWidth;

		$pdfLeftMargin = 28.35;
		$pdfTopMargin = 28.35;

		foreach ($areas as $area)
		{
			if ($area['fontTypeId'])
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area['id']);
				$fontType = $fontModel->getItem($area['fontTypeId']);
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $fontType->font_file;
			}
			else
			{
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($data['designBackground']['reddesign_background_id']);
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

			if ($data['designType']['fontsizer'] == 'auto')
			{
				$autoSizeData = $data['autoSizeData'];
				$fontSize = 0;

				if ($autoSizeData['fontSize'])
				{
					$fontSize = $autoSizeData['fontSize'] / $ratio;
				}

				$noOfLines = count($autoSizeData['perLineCharArr']);

				$bottomoffset = $imageHeight - $areaItem->y2_pos + $pdfTopMargin;

				$maxHeight = $autoSizeData['maxHeight'];
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
					$epsAreaText .= "\n (" . $autoSizeData['perLineCharArr'][$h] . ") dup stringwidth pop 2 div neg 0 rmoveto true charpath ";
					$epsAreaText .= "\n fill";
					$epsAreaText .= "\n showpage";

					$epsText .= "\n $offsetLeft $offsetTop moveto";
					$epsText .= "\n (" . $autoSizeData['perLineCharArr'][$h] . ")";
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

				if ((int) $area['textAlign'] == 1)
				{
					// Left.
					$offsetLeft = $areaItem->x1_pos;
				}
				elseif ((int) $area['textAlign'] == 2)
				{
					// Right.
					$offsetLeft = $areaItem->x2_pos - ($areaItem->width / 2);
				}
				else
				{
					// Center.
					$offsetLeft = $areaItem->x1_pos + ($areaItem->width / 4);
				}

				$offsetTop = (($imageHeight - $areaItem->y2_pos) + $pdfTopMargin + ($areaItem->height / 2)) - (($area['fontSize'] / 2) - ($area['fontSize'] * 0.15));
				$offsetLeft += $pdfLeftMargin;

				$epsText .= $epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $area['fontSize'] . "  scalefont setfont\n";

				$epsText .= "\n$rgbTextColor setrgbcolor";
				$epsText .= "\ngsave\n";

				$epsAreaText .= "\n$rgbTextColor setrgbcolor";
				$epsAreaText .= "\ngsave\n";

				$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
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

		$epsFileName = $epsFilePath . $productionFileName . ".pdf";

		$tempFile = "%!PS";
		$tempFile .= "\n%%Creator:redDESIGN";
		$tempFile .= "\n%%Title:" . $productionFileName;
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

		$fp = fopen($tmpEpsImage, "w");
		fwrite($fp, $tempFile);
		fclose($fp);

		$imageWidth = $imageWidth + 56.7;
		$imageHeight = $imageHeight + 56.7;

		$cmd = "gs -dBATCH -dNOPAUSE -sOutputFile=$tmpBound -sDEVICE=ps2write  \-c '<< /PageSize
				[$imageWidth $imageHeight]  >> setpagedevice'  -f" . $tmpEpsImage;
		exec($cmd);

		$imageBound = $this->readBound($tmpBound);
		$epsFile  = "%!PS-Adobe-3.1 EPSF-3.1";
		$epsFile .= "\n%%Creator:redDESIGN";
		$epsFile .= "\n%%Title:" . $productionFileName;
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

			$epsFile .= "\n%%BeginDocument: danske.eps";
			$epsFile .= "\n(" . $epsFileLocation . ") run";
			$epsFile .= "\n%%EndDocument";
			$epsFile .= "\nEndEPSF";
		}

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

		// Create pdf ...
		ob_clean();

		$pdfFileName = $pdfFilePath . $productionFileName . ".pdf";
		$cmd  = "gs -dBATCH -dNOPAUSE -dNOEPS -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true  -dSubsetFonts=false";
		$cmd .= " -sOutputFile=$pdfFileName -sDEVICE=pdfwrite   \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice'  -f" . $tmpEpsFile;
		exec($cmd);

		$cmd = "gs -dBATCH -dNOPAUSE  -dNOEPS -dEPSCrop -dNOCACHE -dEmbedAllFonts=true -dPDFFitPage=true -dSubsetFonts=false ";
		$cmd .= "-dOptimize=false -sOutputFile=$epsFileName -sDEVICE=pdfwrite  \-c '<< /PageSize [$imageWidth $imageHeight]";
		$cmd .= "  >> setpagedevice' -f" . $tmpTextEpsFile;
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
