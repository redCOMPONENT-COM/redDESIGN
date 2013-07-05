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
 * Designtype View
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignViewDesigntype extends FOFViewHtml
{
	/**
	 * Executes before rendering the page for the Read task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$model 						= $this->getModel();

		// Get Design
		$this->item 				= $model->getItem();

		// Get all the backgrounds that belongs to this Designtype item
		$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')->reddesign_designtype_id($this->item->reddesign_designtype_id);
		$this->backgrounds = $backgroundModel->getItemList();

		parent::display($tpl);
	}
}
