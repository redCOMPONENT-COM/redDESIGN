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

class ReddesignViewBackground extends FOFViewHtml
{
	/**
	 * Initialize JS and CSS files for the image selector
	 *
	 * @param   string  $tpl  The template to use
	 *
	 * @return  boolean|null False if we can't render anything	 *
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->assign('background_fonts_list', $model->getBackgroundFonts());

		parent::display($tpl);
	}

	/**
	 * Before adding or editing a background gets the designs list
	 *
	 * @param   null  $tpl  Template to load
	 *
	 * @return bool
	 */
	public function onAdd($tpl = null)
	{
		//JRequest::setVar('hidemainmenu', true);
		$this->input->setVar('hidemainmenu', true);
		$model = $this->getModel();

		$this->assign('item', $model->getItem());
		$this->assign('designs_list', $model->getDesigns());
		$this->assign('fonts_list', $model->getFonts());

		return true;
	}
}
