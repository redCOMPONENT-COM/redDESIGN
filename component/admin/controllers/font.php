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
class ReddesignControllerFont extends FOFController
{
	/**
	 * Constructor to set the right model
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'font';
	}

	/**
	 * Uploads the font file
	 *
	 * @return  boolean  Returns true on success
	 */
	public function onBeforeApplySave()
	{
		$file = JRequest::getVar('fontfile', '', 'files', 'array');

		$model			= $this->getThisModel();
		$uploaded_file	= $model->uploadFile($file);

		// $uploaded_file['filepath'];
	}
}
