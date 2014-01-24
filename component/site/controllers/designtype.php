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
	 * Executes a task
	 *
	 * @param   string  $task  The task to be executed
	 *
	 * @return bool|null|void
	 *
	 * @access public
	 */
	public function execute($task)
	{
		$this->registerDefaultTask('read');

		parent::execute($task);
	}

	/**
	 * Calculates Font size and Offset when Auto-size is on.
	 *
	 * @param   int    $fontId         FontId.
	 * @param   array  $enteredChars   EnteredChars.
	 * @param   array  $fontDetailArr  FontDetailArr.
	 * @param   int    $canvasHeight   CanvasHeight.
	 * @param   int    $canvasWidth    CanvasWidth.
	 *
	 * @return array of Font Size and Offset respectively
	 *
	 * @access public
	 */
	public function getFontSizeOnCharsBase($fontId, $enteredChars, $fontDetailArr, $canvasHeight, $canvasWidth)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$char = preg_split('/(?<!^)(?!$)/u', $enteredChars);

		// Select character settings for a given font.
		$query
			->select('max(chars.height) as height, group_concat(typography separator ", ") as typography, typography_height')
			->from($db->quoteName('#__reddesign_chars as chars'))
			->where($db->quoteName('chars.reddesign_font_id') . ' = ' . (int) $fontId)
			->where($db->quoteName('chars.font_char') . ' IN ("' . implode('","', $char) . '")')
			->order('chars.reddesign_char_id ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects.
		$charArr = $db->loadObject();
		$maxHeight = $charArr->height;

		$width = 0;

		for ($i = 0;$i < count($char);$i++)
		{
			$query = $db->getQuery(true);
			$query
				->select('chars.width')
				->from($db->quoteName('#__reddesign_chars as chars'))
				->where($db->quoteName('binary chars.font_char') . ' = ' . $db->quote($char[$i]))
				->where($db->quoteName('chars.reddesign_font_id') . ' = "' . (int) $fontId . '" ');
			$db->setQuery($query);
			$width 		= $width + $db->loadResult();
		}

		$avgWidth = $width / count($char);

		if ($avgWidth == 0)
		{
			$avgWidth = $fontDetailArr->default_width;
		}

		if ($maxHeight == 0)
		{
			$maxHeight = $fontDetailArr->default_height;
		}

		$typoArr = explode(',', $charArr->typography);

		if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array(4, $typoArr))
		{
			$maxHeight = $maxHeight + $fontDetailArr->default_caps_height;
		}

		$perLineChar = explode("\n", $enteredChars);

		// @to-do take it from area table.....
		$maxCharsInSingleLine = max(array_map('strlen', $perLineChar));

		// @to-do take it from area table..
		$lineCount = count($perLineChar);

		$fontSize = $this->calculateFontSize($maxCharsInSingleLine, $avgWidth, $maxHeight, $lineCount, $canvasHeight, $canvasWidth);

		$offset = $this->getCharOffset($char, $fontId, $fontSize);

		$autoSizeData = array();
		$autoSizeData['fontSize'] = $fontSize;
		$autoSizeData['stringLines'] = $perLineChar;
		$autoSizeData['topoffset'] = $offset[0];
		$autoSizeData['maxHeight'] = $maxHeight;

		return $autoSizeData;
	}

	/**
	 * Calculates Font size When Auto-size is on.
	 *
	 * @param   int    $maxCharsInSingleLine  maxCharsInSingleLine.
	 * @param   float  $fontW                 fontW.
	 * @param   float  $fontH                 fontH.
	 * @param   int    $lineCount             lineCount.
	 * @param   int    $canvasHeight          canvasHeight.
	 * @param   int    $canvasWidth           canvasWidth.
	 *
	 * @return int
	 *
	 * @access public
	 */
	public function calculateFontSize($maxCharsInSingleLine=3, $fontW=0.5366667, $fontH=0.77, $lineCount=1, $canvasHeight=504, $canvasWidth=324)
	{
		// To find the font size for particular one line, need to divide it by number of lines.
		$canvasEffectiveHeight = $canvasHeight / $lineCount;
		$fontSizeByHeightMax = $canvasEffectiveHeight / $fontH;
		$fontSizeByWidthMax = $canvasWidth / (($fontW) * $maxCharsInSingleLine);

		/*
		* Mid-Height (eg. char "a,s,u")
		* Cap-Height (eg. char "b,B,X")
		* Base-Height (eg. char "j,q,y")
		*/

		if ($fontSizeByWidthMax > $fontSizeByHeightMax)
		{
			$finalFontSize = $fontSizeByHeightMax;
			$finalFontSize = $finalFontSize / 1.1;
		}
		else
		{
			$finalFontSize = $fontSizeByWidthMax;
		}

		return $finalFontSize;
	}

	/**
	 *  Calculates Font size When Auto-size is on.
	 *
	 * @param   string  $canvaseDimensionBase       It can be width(w) or height(h).
	 * @param   int     $canvaseDimensionBaseInput  Dimension amount.
	 * @param   int     $fontId                     Font id.
	 * @param   string  $enteredChars               Entered char by user.
	 * @param   array   $fontDetailArr              Default height and width of fonts.
	 *
	 * @return Array
	 *
	 * @access public
	 */
	public function getCanvaseDimension($canvaseDimensionBase, $canvaseDimensionBaseInput, $fontId, $enteredChars, $fontDetailArr)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$char = str_split(mb_convert_encoding(urldecode($enteredChars), "ISO-8859-1", "UTF-8"));

		// Select character settings for a given font.
		$query
			->select('max(chars.height) as height, group_concat(typography separator ", ") as typography, typography_height')
			->from($db->quoteName('#__reddesign_chars as chars'))
			->where($db->quoteName('chars.reddesign_font_id') . ' = ' . (int) $fontId)
			->where($db->quoteName('chars.font_char') . ' IN ("' . implode('","', $char) . '")')
			->order('chars.reddesign_char_id ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects.
		$charArr = $db->loadObject();
		$maxHeight = $charArr->height;

		$width = 0;

		for ($i = 0;$i < count($char);$i++)
		{
			$query = $db->getQuery(true);
			$query
				->select('chars.width')
				->from($db->quoteName('#__reddesign_chars as chars'))
				->where($db->quoteName('binary chars.font_char') . ' = ' . $db->quote($char[$i]))
				->where($db->quoteName('chars.reddesign_font_id') . ' = "' . (int) $fontId . '" ');
			$db->setQuery($query);
			$width 		= $width + $db->loadResult();
		}

		if ($width == 0)
		{
			$width = $fontDetailArr->default_width;
		}

		if ($maxHeight == 0)
		{
			$maxHeight = $fontDetailArr->default_height;
		}

		$typoArr = explode(',', $charArr->typography);

		if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array(4, $typoArr))
		{
			$maxHeight = $maxHeight + $fontDetailArr->default_caps_height;
		}

		if ($canvaseDimensionBase == 'w')
		{
			$fontSize = $canvaseDimensionBaseInput / ($width * 1.1);
			$canvasHeight = $fontSize * $maxHeight;
			$canvasWidth = $canvaseDimensionBaseInput;
		}
		else
		{
			$fontSize = $canvaseDimensionBaseInput / $maxHeight;
			$canvasWidth = $fontSize * $width;
			$canvasHeight = $canvaseDimensionBaseInput;
		}

		$canvaseDimension = array();
		$canvaseDimension['canvasHeight'] = $canvasHeight;
		$canvaseDimension['canvasWidth'] = $canvasWidth;

		return $canvaseDimension;
	}

	/**
	 *  Calculates Font size When Auto-size is on.
	 *
	 * @param   array  $char      Char array entered by user.
	 * @param   int    $fontId    Font id.
	 * @param   int    $fontSize  This is font size of entered chars.
	 *
	 * @return Array
	 *
	 * @access public
	 */
	public function getCharOffset($char, $fontId, $fontSize)
	{
		$db = JFactory::getDBO();

		if (count($char))
		{
			$query = $db->getQuery(true);
			$query
				->select('fonts.default_height, fonts.default_caps_height, fonts.default_baseline_height')
				->from($db->quoteName('#__reddesign_fonts as fonts'))
				->where($db->quoteName('fonts.reddesign_font_id') . ' = ' . (int) $fontId);

			$db->setQuery($query);
			$HeightArray = $db->loadObject();

			$query = $db->getQuery(true);
			$query
				->select('max(height) as height,  group_concat(typography separator ", ") as typography, typography_height')
				->from($db->quoteName('#__reddesign_chars as chars'))
				->where($db->quoteName('chars.reddesign_font_id') . ' = ' . (int) $fontId)
				->where($db->quoteName('binary chars.font_char') . ' IN ("' . implode('","', $char) . '")')
				->order('chars.reddesign_char_id ASC');

			$db->setQuery($query);
			$ResultArray = $db->loadObject();

			$typoArr = explode(',', $ResultArray->typography);

			if (empty($HeightArray))
			{
				return array( '+0', 0 );
			}

			$totalHeight = $HeightArray->default_height + $ResultArray->typography_height;
			$totalHeight = $totalHeight + $HeightArray->default_caps_height + $HeightArray->default_baseline_height;

			if (in_array('2', $typoArr) && in_array('3', $typoArr) && !in_array('4', $typoArr))
			{
				$difference = $totalHeight - $ResultArray->height - $HeightArray->default_caps_height;
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}
			elseif (in_array('3', $typoArr) || in_array('4', $typoArr))
			{
				$difference = $totalHeight - ($ResultArray->height);
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}
			elseif (in_array('2', $typoArr))
			{
				$difference = $totalHeight - ( $ResultArray->height + $HeightArray->default_baseline_height);
				$offsetTop = (($difference * $fontSize) / 2);
				$offsetTop = "+" . $offsetTop;
			}
			else
			{
				$difference = $totalHeight - ( $ResultArray->height + $HeightArray->default_baseline_height);
				$offsetTop = (($difference * $fontSize) / 2) * 1.2;
				$offsetTop = "-" . $offsetTop;
			}

			$diff = $totalHeight - $ResultArray->height;

			if (!$ResultArray->height)
			{
				$offsetTop = '+0';
			}

			return array($offsetTop, $diff );
		}
		else
		{
			return array( '+0', 0 );
		}
	}
}
