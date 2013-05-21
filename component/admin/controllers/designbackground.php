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
class ReddesignControllerDesignbackground extends FOFController
{
	/**
	 * Constructor to set the right model
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'designbackground';
	}

	/**
	 * Uploads the EPS-Background file and generates a JPG image preview of the EPS
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  boolean  Returns true on success
	 *
	 * @see http://en.wikipedia.org/wiki/Encapsulated_PostScript
	 */
	public function onBeforeApplySave(&$data)
	{
		$file = JRequest::getVar('epsfile', '', 'files', 'array');

		$model			= $this->getThisModel();

		// Upload the font file
		$uploaded_file	= $model->uploadFile($file);

		// Create a image preview of the EPS
		$jpegpreviewfile = $model->createBackgroundPreview($uploaded_file['mangled_filename']);

		// Update the database with the new path of the EPS file and its thumb
		$data['epsfile']			= $uploaded_file['mangled_filename'];
		$data['jpegpreviewfile']	= $jpegpreviewfile;

		return $data;
	}
}
