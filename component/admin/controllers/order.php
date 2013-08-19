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
		$pdfFileName = '0';

		if (!empty($productId))
		{
			// Get JSON about design type from product record. It is previously saved by a plugin.
			$dispatcher = JDispatcher::getInstance();
			$data = $dispatcher->trigger('getDesigntypeJSON', $productId);
			$data = json_decode($data);

			if (!empty($data))
			{
				$pdfFileName = $this->createPdfProductfile($data);
			}
		}

		echo $pdfFileName;
	}

	/**
	 * Create ProductPDF file for redDesign
	 *
	 * @param   array  $data  An array that holds design information
	 *
	 * @return bool
	 */
	public function createPdfProductfile($data)
	{
		$session = JFactory::getSession();
		$productionFileName = $session->get('customizedImage');

		$areas = $data['designAreas'];
		$epsText = '';
		$epsAreaText = '';
		$epsTextFile = '';

		$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $data['designBackground']->eps_file;
		$pdfFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/pdf/';
		$epsFilePath = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/orders/eps/';

		// Read EPS
		$im = new Imagick;
		$im->readImage($epsFileLocation);
		$dimensions = $im->getImageGeometry();
		$imageWidth = $dimensions['width'];
		$imageHeight = $dimensions['height'];

		$pdfLeftMargin = 28.35;
		$pdfTopMargin = 28.35;

		foreach ($areas as $area)
		{
			if ($area->fontTypeId)
			{
				$fontModel = FOFModel::getTmpInstance('Fonts', 'ReddesignModel')->reddesign_area_id($area->id);
				$this->fontType = $fontModel->getItem($area->fontTypeId);
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/' . $this->fontType->font_file;
			}
			else
			{
				$fontTypeFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/fonts/arial.ttf';
			}

			$areaModel = FOFModel::getTmpInstance('Areas', 'ReddesignModel')->reddesign_background_id($data['designBackground']->reddesign_background_id);
			$this->areaItem = $areaModel->getItem($area->id);

			if ((int) $this->areaItem->textalign == 1)
			{
				$offsetLeft = $this->areaItem->x1_pos;
			}
			elseif ((int) $this->areaItem->textalign == 2)
			{
				$offsetLeft = $this->areaItem->x2_pos - ($this->areaItem->width / 2);
			}
			else
			{
				$offsetLeft = $this->areaItem->x1_pos + ($this->areaItem->width / 4);
			}

			$offsetTop = $imageHeight - $this->areaItem->y2_pos;

			$offsetLeft = $pdfLeftMargin + $offsetLeft;
			$offsetTop = $offsetTop + $pdfTopMargin;

			if ($data['designType']->fontsizer == 'auto')
			{
				$AutoSizeData = $session->get('AutoSizeData');

				if ($AutoSizeData['FontSize'])
				{
					$fontSize = $AutoSizeData['FontSize'];
				}

				$noOfLines = count($AutoSizeData['perLineCharArr']);

				$bottomoffset = $imageHeight - $this->areaItem->y2_pos + 28.35;

				$maxHeight = $AutoSizeData['maxHeight'];
				$offsetOverallArea = (($this->areaItem->height - ($fontSize * $maxHeight * $noOfLines)) / 2);
				$offsetOverallArea = $offsetOverallArea + $bottomoffset;

				$offsetLeft = $this->areaItem->x1_pos + 28.35 + ($this->areaItem->width / 2);

				for ($h = 0;$h < $noOfLines;$h++)
				{
					if ($noOfLines == 1)
					{
						$offsetTop = 28.35 + $AutoSizeData['PDFoffsetTop'];
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

					$epsText .= "\n0 0 0 setrgbcolor";
					$epsText .= "\ngsave\n";

					$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
					$epsAreaText .= "\n (" . $AutoSizeData['perLineCharArr'][$h] . ") dup stringwidth pop 2 div neg 0 rmoveto true charpath ";
					$epsAreaText .= "\n fill";
					$epsAreaText .= "\n showpage";

					$epsText .= "\n $offsetLeft $offsetTop moveto";
					$epsText .= "\n (" . $AutoSizeData['perLineCharArr'][$h] . ")";
					$epsText .= "\n cshow";
				}
			}
			else
			{
				$epsText .= $epsAreaText .= "\n/ (" . $fontTypeFileLocation . ") findfont " . $area->fontSize . "  scalefont setfont\n";

				$epsText .= "\n0 0 0 setrgbcolor";
				$epsText .= "\ngsave\n";

				$epsAreaText .= "\n $offsetLeft $offsetTop moveto";
				$epsAreaText .= "\n (" . $area->textArea . ") true charpath ";

				$epsAreaText .= "\n fill";
				$epsAreaText .= "\n showpage";

				$epsText .= "\n $offsetLeft $offsetTop moveto";
				$epsText .= "\n (" . $area->textArea . ")";
				$epsText .= "\n cshow";
			}
		}

		$epsText .= "\ngrestore\n";

		$tmpEpsFile = $epsFilePath . "tmp_" . $productionFileName . ".eps";
		$tmpTextEpsFile = $epsFilePath . "tmptext_" . $productionFileName . ".eps";

		$epsFileName = $epsFilePath . "reddesign" . $productionFileName . ".pdf";

		$tempFile = "%!PS";
		$tempFile .= "\n%%Creator:reddesign";
		$tempFile .= "\n%%Title:reddesign" . $productionFileName;
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
		$epsFile .= "\n%%Creator:reddesign";
		$epsFile .= "\n%%Title:reddesign" . $productionFileName;
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

		$pdfFileName = $pdfFilePath . "reddesign" . $productionFileName . ".pdf";
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

		return $pdfFileName;
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
}
