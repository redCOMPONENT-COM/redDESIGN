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
	 * Uploads the font file and generates a image preview of the font
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  boolean  Returns true on success
	 */
	public function onBeforeApplySave(&$data)
	{
		$file = JRequest::getVar('fontfile', '', 'files', 'array');

		// If file has been uploaded, process it
		if (!empty($file['name']) && !empty($file['type']))
		{
			$model			= $this->getThisModel();

			// Upload the font file
			$uploaded_file	= $model->uploadFile($file);

			// Create a image preview of the Font
			$font_thumb = $model->createFontPreviewThumb($uploaded_file['mangled_filename']);

			// Update the database with the new path of the font file
			$data['fontfile']	= $uploaded_file['mangled_filename'];
			$data['fontthumb']	= $font_thumb;
		}

		return $data;
	}
}
