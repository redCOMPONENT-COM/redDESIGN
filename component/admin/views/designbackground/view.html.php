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
 * Design Background View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */

class ReddesignViewDesignbackground extends FOFViewHtml
{
	/**
	 * Before adding or editing a background gets the designs list
	 *
	 * @param   null  $tpl  Template to load
	 *
	 * @return bool
	 */
	public function onAdd($tpl = null)
	{
		JRequest::setVar('hidemainmenu', true);
		$model = $this->getModel();

		$this->assign('item', $model->getItem());
		$this->assign('designs_list', $model->getDesigns());
		$this->assign('fonts_list', $model->getFonts());

		return true;
	}
}
