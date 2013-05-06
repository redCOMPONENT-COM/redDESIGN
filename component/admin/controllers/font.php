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
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  boolean  Returns true on success
	 */
	public function onBeforeApplySave(&$data)
	{
		$file = JRequest::getVar('fontfile', '', 'files', 'array');

		$model			= $this->getThisModel();

		// Upload the font file
		$uploaded_file	= $model->uploadFile($file);

		// Update the database with the new path of the font file
		$data['fontfile'] = $uploaded_file['filepath'];

		return $data;
	}
}
