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
 * Background Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerBackground extends FOFController
{
	/**
	 * Constructor to set the right model
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->modelName = 'background';
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
		$file = JRequest::getVar('bg_eps_file', '', 'files', 'array');

		// If file has been uploaded, process it
		if (!empty($file['name']) && !empty($file['type']))
		{
			$model			= $this->getThisModel();

			// Upload the font file
			$uploaded_file	= $model->uploadFile($file);

			// Create a image preview of the EPS
			$jpegpreviewfile = $model->createBackgroundPreview($uploaded_file['mangled_filename']);

			// Update the database with the new path of the EPS file and its thumb
			$data['eps_file']			= $uploaded_file['mangled_filename'];
			$data['image_path']			= $jpegpreviewfile;
		}

		return $data;
	}
}
