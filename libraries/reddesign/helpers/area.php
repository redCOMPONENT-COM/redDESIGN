<?php
/**
 * @package     RedDesign.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;


/**
 * Area helper.
 *
 * @package     Reddesign.Libraries
 * @subpackage  Helpers
 *
 * @since       1.0
 */
final class ReddesignHelpersArea
{
	/**
	 * Returns Area Type in select list format
	 *
	 * @return array
	 */
	public static function getAreaTypeSelectOptions()
	{
		$areaTypes = self::getAreaTypes();
		$optionList = array();

		foreach ($areaTypes as $areaType)
		{
			$optionList[] = JHtml::_('select.option', $areaType['id'], $areaType['title']);
		}

		return $optionList;
	}

	/**
	 * Returns Area Type list
	 *
	 * @return array
	 */
	public static function getAreaTypes()
	{
		return array(
			'1' => array (
				'id'    => 1,
				'name'  => 'text',
				'title' => JText::_('COM_REDDESIGN_AREA_TYPE_TEXT'),
			),
			'2' => array (
				'id'    => 2,
				'name'  => 'clipart',
				'title' => JText::_('COM_REDDESIGN_AREA_TYPE_CLIPART'),
			),
		);
	}

	/**
	 * Returns specific Area Type
	 *
	 * @param   int  $areaTypeId  Area Type Id
	 *
	 * @return array
	 */
	public static function getAreaType($areaTypeId)
	{
		if (empty($areaTypeId) || $areaTypeId <= 0)
		{
			$areaTypeId = 1;
		}

		$areaTypes = self::getAreaTypes();

		if (!empty($areaTypes[$areaTypeId]))
		{
			return $areaTypes[$areaTypeId];
		}

		return array();
	}

	/**
	 * Returns Area Text Type list
	 *
	 * @return array
	 */
	public static function getAreaTextTypeOptions()
	{
		return array(
			JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTBOX')),
			JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTAREA'))
		);
	}

	/**
	 * Returns Area Horizontal Alignment Options
	 *
	 * @return array
	 */
	public static function getAreaHorizontalAlignmentOptions()
	{
		return array(
			JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_COMMON_SELECT')),
			JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_COMMON_LEFT')),
			JHtml::_('select.option', '2', JText::_('COM_REDDESIGN_COMMON_RIGHT')),
			JHtml::_('select.option', '3', JText::_('COM_REDDESIGN_COMMON_CENTER'))
		);
	}

	/**
	 * Returns Area Vertical Alignment Options
	 *
	 * @return array
	 */
	public static function getAreaVerticalAlignmentOptions()
	{
		return array(
			JHtml::_('select.option', '', JText::_('COM_REDDESIGN_COMMON_SELECT')),
			JHtml::_('select.option', 'top', JText::_('COM_REDDESIGN_COMMON_TOP')),
			JHtml::_('select.option', 'middle', JText::_('COM_REDDESIGN_COMMON_MIDDLE')),
			JHtml::_('select.option', 'bottom', JText::_('COM_REDDESIGN_COMMON_BOTTOM'))
		);
	}

	/**
	 * Returns List of all cliparts
	 *
	 * @return array
	 */
	public static function getClipartsSelectOptions()
	{
		// Get all cliparts in the system to be choosen or not for the current design.
		$clipartsModel = RModel::getAdminInstance('Cliparts', array('ignore_request' => true), 'com_reddesign');
		$cliparts = $clipartsModel->getItems();
		$clipartOptions = array();

		foreach ($cliparts as $clipart)
		{
			$clipartOptions[] = JHtml::_('select.option', $clipart->id, $clipart->name);
		}

		return $clipartOptions;
	}

	/**
	 * Returns List of all area featured Cliparts
	 *
	 * @param   int  $areaId  Area Id
	 *
	 * @return object
	 */
	public static function getAreaFeaturedCliparts($areaId)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('ac.clipartId, c.*, c1.title as categoryName')
			->from($db->qn('#__reddesign_area_clipart_xref', 'ac'))
			->leftJoin($db->qn('#__reddesign_cliparts', 'c') . ' ON c.id = ac.clipartId')
			->leftJoin($db->qn('#__categories', 'c1') . ' on c.categoryId = c1.id')
			->where('c.state = 1 AND ac.areaId = ' . (int) $areaId)
			->order('c.name ASC');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Area tag list
	 *
	 * @param   array  $areaTemplateTags  List of template tags that will be merged with default one
	 *
	 * @return object
	 */
	public static function getAreaTemplateTags($areaTemplateTags = array())
	{
		$templateTags = array(
			'{RedDesignBreakDesignAreaTitle}' => '{redDESIGN:AreaTitle}',
			'{RedDesignBreakDesignAreaInputTextLabel}' => '{redDESIGN:InputTextLabel}',
			'{RedDesignBreakDesignAreaInputText}' => '{redDESIGN:InputText}',
			'{RedDesignBreakDesignAreaChooseFontLabel}' => '{redDESIGN:ChooseFontLabel}',
			'{RedDesignBreakDesignAreaChooseFont}' => '{redDESIGN:ChooseFont}',
			'{RedDesignBreakDesignAreaChooseFontSizeLabel}' => '{redDESIGN:ChooseFontSizeLabel}',
			'{RedDesignBreakDesignAreaChooseFontSize}' => '{redDESIGN:ChooseFontSize}',
			'{RedDesignBreakDesignAreaChooseColorLabel}' => '{redDESIGN:ChooseColorLabel}',
			'{RedDesignBreakDesignAreaChooseColor}' => '{redDESIGN:ChooseColor}',
			'{RedDesignBreakDesignAreaChooseHorizontalAlign}' => '{redDESIGN:ChooseHorizontalAlignment}',
			'{RedDesignBreakDesignAreaChooseVerticalAlign}' => '{redDESIGN:ChooseVerticalAlignment}',
			'{RedDesignBreakDesignAreaChooseClipartLabel}' => '{redDESIGN:ChooseClipartLabel}',
			'{RedDesignBreakDesignAreaChooseClipart}' => '{redDESIGN:ChooseClipart}',
		);

		$templateTags = array_merge($templateTags, $areaTemplateTags);

		return $templateTags;
	}

	/**
	 * Parse Area HTML with Redshop Template tags
	 *
	 * @param   string  $areaHtml                   Area default HTML output
	 * @param   string  $areasLoopTemplateInstance  Area Template instance which is used to display result
	 * @param   array   $areaTemplateTags           List of template tags that will be used for parsing
	 *
	 * @return object
	 */
	public static function parseAreaTemplateTags($areaHtml = '', $areasLoopTemplateInstance = '', $areaTemplateTags = null)
	{
		if ($areaTemplateTags == null)
		{
			$areaTemplateTags = self::getAreaTemplateTags();
		}

		foreach ($areaTemplateTags as $areaTemplateTagKey => $areaTemplateTagValue)
		{
			$htmlElement = explode($areaTemplateTagKey, $areaHtml);
			$parseResult = '';

			if (!empty($htmlElement[1]))
			{
				$parseResult = $htmlElement[1];
			}

			$areasLoopTemplateInstance = str_replace($areaTemplateTagValue, $parseResult, $areasLoopTemplateInstance);
		}

		return $areasLoopTemplateInstance;
	}
}
