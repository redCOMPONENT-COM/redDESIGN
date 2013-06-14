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

class ReddesignViewDesign extends FOFViewHtml
{
	/**
	 * Initialize JS and CSS files for the image selector
	 *
	 * @param   string $tpl  The template to use
	 *
	 * @return  boolean|null False if we can't render anything     *
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$item = $model->getItem();

		$bckgrnd_model = FOFModel::getTmpInstance('Backgrounds', 'ReddesignModel');
		$bckgrnds = $bckgrnd_model->savestate(0)->limit(0)->limitstart(0)->getItemList();
		$options = array();
		if (count($bckgrnds)) foreach ($bckgrnds as $bckgrnd)
		{
			$options[] = JHTML::_('select.option', $bckgrnd->reddesign_background_id, $bckgrnd->title);
		}
		array_unshift($options, JHTML::_('select.option', 0, '- ' . JText::_('COM_REDDESIGN_SELECT_BACKGROUND') . ' -'));
		//$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$onchange = ' onchange="javascript:"';
		$this->backgrounds = JHtml::_('select.genericlist', $options, 'reddesign_background_id', $onchange, 'value', 'text', $item->reddesign_background_id);

		$background = FOFModel::getTmpInstance('Background', 'ReddesignModel')
			->setId($item->reddesign_background_id)
			->getItem();
		$this->background = $background;

		list($width, $height) = getimagesize(FOFTemplateUtils::parsePath('media://com_reddesign/assets/backgrounds/') . $this->background->jpegpreviewfile);
		$this->bckgrnd_width = $width;
		$this->bckgrnd_height = $height;

		$font = FOFModel::getTmpInstance('Font', 'ReddesignModel')
			->setId(1)
			->getItem();
		$this->font = $font;

		parent::display($tpl);
	}
}
