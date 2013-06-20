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

class ReddesignViewDesigntype extends FOFViewHtml
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
		$backgroundModel = FOFModel::getTmpInstance('Background', 'ReddesignModel')
			->reddesign_designtype_id($item->reddesign_designtype_id);
		$this->backgrounds = $backgroundModel->getItemList();

		parent::display($tpl);
	}
}
