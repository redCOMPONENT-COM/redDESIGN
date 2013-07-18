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
		// Get Design
		$model 						= $this->getModel();
		$this->item 				= $model->getItem();

		$this->backgrounds					= $model->getBackgrounds();
		$this->previewBackground			= $model->getPreviewBackground();
		$this->previewBackgrounds			= $model->getPreviewBackgrounds();
		$this->productionBackground			= $model->getProductionBackground();
		$this->productionBackgroundAreas	= $model->getProductionBackgroundAreas($this->productionBackground->reddesign_background_id);
		$this->fonts						= $model->getFonts();

		parent::display($tpl);
	}
}
