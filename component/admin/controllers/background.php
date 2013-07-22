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
		// Get Eps if has been added
		$file = $this->input->files->get('bg_eps_file', null);

		// Get Thumbnail if has been added
		$thumbFile			= $this->input->files->get('thumbnail', null);
		$thumbPreviewFile	= null;

		// Get component Params
		$params = JComponentHelper::getParams('com_reddesign');


		// If file has has not been uploaded
		if (empty($file['name']) || empty($file['type']))
		{
			// If is a new background and the file is not attached return error
			if (!$data['reddesign_background_id'])
			{
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_NO_FILE'), 'error');
				$this->setRedirect('index.php?option=com_reddesign&view=designtype&id=' . (int) $data['reddesign_designtype_id'] . '&tab=backgrounds');
				$this->redirect();
			}
		}
		else
		{
			// Upload the background file
			$uploaded_file	= $this->uploadFile($file);

			if (!$uploaded_file)
			{
				$this->setRedirect('index.php?option=com_reddesign&view=designtype&id=' . (int) $data['reddesign_designtype_id'] . '&tab=backgrounds');
				$this->redirect();
			}

			// Create a image preview of the EPS
			$jpegPreviewFile = $this->createBackgroundPreview($uploaded_file['mangled_filename']);

			if (!$jpegPreviewFile)
			{
				return false;
			}

			// If no thumbnail file has been attached generate one based on the Background EPS
			if (!$thumbFile['name'])
			{
				// Create a image preview thumbnail based on the EPS
				$im = new Imagick;
				$im->readImage(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $jpegPreviewFile);
				$im->thumbnailImage($params->get('max_background_thumbnail_width', 50), $params->get('max_background_thumbnail_height', 50), true);
				$im->writeImage(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/thumbnails/' . $jpegPreviewFile);
				$im->clear();
				$im->destroy();
				$thumbPreviewFile = $jpegPreviewFile;
			}
		}

		// If thumbnail has been attached upload it and set component parameters max size
		if ($thumbFile['name'])
		{
			// Upload the attached thumbnail
			require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/helpers/file.php';
			$fileHelper = new ReddesignHelperFile;

			$uploadedThumbFile = $fileHelper->uploadFile(
				$thumbFile,
				'backgrounds/thumbnails',
				$params->get('max_designtype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF'
			);

			$im = new Imagick;
			$im->readImage($uploadedThumbFile['filepath']);
			$im->thumbnailImage($params->get('max_designtype_thumbnail_width', 210), $params->get('max_designtype_thumbnail_height', 140), true);
			$im->writeImage($uploadedThumbFile['filepath']);
			$im->clear();
			$im->destroy();

			$thumbPreviewFile = $uploadedThumbFile['mangled_filename'];
		}

		// Update the database with the new path of the EPS file and its thumb
		$data['eps_file']			= $uploaded_file['mangled_filename'];
		$data['image_path']			= $jpegPreviewFile;

		if ($thumbPreviewFile)
		{
			$data['thumbnail']			= $thumbPreviewFile;
		}

		// If this new background will be the PDF Production background switch it against the previous production background
		if ((int) $data['isPDFbgimage'])
		{
			$backgroundsModel	= $this->getThisModel();
			$designId			= (int) $data['reddesign_designtype_id'];

			// Set all other backgrounds as non PDF backgrounds
			$backgroundsModel->unsetAllPDFBg($designId);
		}

		return $data;
	}

	/**
	 * Moves an uploaded EPS file to the media://com_reddesign/assets/backgrounds/
	 * under a random name and returns a full file definition array, or false if
	 * the upload failed for any reason.
	 *
	 * @param   array  $file  The file descriptor returned by PHP
	 *
	 * @return array|bool
	 */
	private function uploadFile($file)
	{
		$app = JFactory::getApplication();

		// Can we upload this file type?
		if (!$this->canUpload($file))
		{
			return false;
		}

		// Get a (very!) randomised name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverkey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverkey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $file['name'] . microtime() . $serverkey;

		if (function_exists('sha256'))
		{
			$mangledname = sha256($sig);
		}
		elseif (function_exists('sha1'))
		{
			$mangledname = sha1($sig);
		}
		else
		{
			$mangledname = md5($sig);
		}

		// ...and its full path
		$filepath = JPath::clean(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $mangledname . '.eps');

		// If we have a name clash, abort the upload
		if (JFile::exists($filepath))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_ERROR_BACKGROUND_FILENAMEALREADYEXIST'), 'error');

			return false;
		}

		// Do the upload
		if (!JFile::upload($file['tmp_name'], $filepath))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_ERROR_BACKGROUND_CANTJFILEUPLOAD'), 'error');

			return false;
		}

		// Get the MIME type
		if (function_exists('mime_content_type'))
		{
			$mime = mime_content_type($filepath);
		}
		elseif (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $filepath);
		}
		else
		{
			$mime = 'application/postscript';
		}

		$result_file = array(
			'original_filename' => $file['name'],
			'mangled_filename' => $mangledname . '.eps',
			'mime_type' => $mime,
			'filepath' => $filepath
		);

		// Return the file info
		return $result_file;
	}

	/**
	 * Checks if the EPS file can be uploaded. This is a security check.
	 *
	 * @param   array  $file  File information
	 *
	 * @return  boolean
	 */
	private function canUpload($file)
	{
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_reddesign');

		if (empty($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_UPLOAD_INPUT'), 'error');

			return false;
		}

		jimport('joomla.filesystem.file');

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_FILE_NAME'), 'error');

			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		// Allowed file extensions
		$allowable = array('eps');

		if (!in_array($format, $allowable))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_WRONG_FILE_EXTENSION'), 'error');

			return false;
		}

		// Max file size is set by config.xml
		$maxSize = (int) ($params->get('max_eps_file_size', 2) * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_FILE_TOOLARGE'), 'error');

			return false;
		}

		// Only allow eps valid mime types
		$okMIMETypes = 'application/postscript, application/eps, application/x-eps, image/eps, image/x-eps, application/download, application/x-download, application/force-download, application/x download';
		$validFileTypes = array_map('trim', explode(",", $okMIMETypes));

		// If the temp file does not have ok MIME, return
		if (!in_array($file['type'], $validFileTypes))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_ERROR_INVALID_MIME'));

			return false;
		}

		return true;
	}

	/**
	 * Creates a image based on a eps file to show the look and feel of the background into media://com_reddesign/assets/backgrounds/
	 *
	 * @param   string  $eps_file  the path to a .eps file
	 *
	 * @return  string
	 */
	private function createBackgroundPreview($eps_file)
	{
		$params = JComponentHelper::getParams('com_reddesign');
		$best_fit = $params->get('eps_bestfit', 1);
		$max_thumb_width = $params->get('max_eps_thumbnail_width', 600);
		$max_thumb_height = $params->get('max_eps_thumbnail_height', 400);

		$eps_file_location = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $eps_file;

		// Read EPS
		$im = new Imagick;

		$im->readImage($eps_file_location);

		$dimensions = $im->getImageGeometry();

		if ($best_fit && ($dimensions['width'] < $max_thumb_width || $dimensions['height'] < $max_thumb_height))
		{
			$im->thumbnailImage($max_thumb_width, $max_thumb_height, true);
		}
		elseif ($dimensions['width'] > $max_thumb_width || $dimensions['height'] > $max_thumb_height)
		{
			$im->thumbnailImage($max_thumb_width, $max_thumb_height, true);
		}

		// Convert to jpg
		$im->setCompression(Imagick::COMPRESSION_JPEG);
		$im->setCompressionQuality(60);

		$im->setImageFormat('jpeg');

		// Create the Background preview .jpg file name
		$image_name = substr($eps_file, 0, -3) . 'jpg';

		// Write image to the media://com_reddesign/assets/backgrounds/
		$im->writeImage(JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $image_name);
		$im->clear();
		$im->destroy();

		return $image_name;
	}

	/**
	 * Method to set the a background as the PDF background.
	 *
	 * @since	1.0
	 *
	 * @return void
	 */
	public function setPDFbg()
	{
		$designId	= $this->input->getInt('reddesign_designtype_id', '');
		$bgId		= $this->input->getInt('reddesign_background_id', '');

		$model = $this->getThisModel();

		$app = JFactory::getApplication();

		if (!$model->setAsPDFbg($designId, $bgId))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_ERROR_SWITCHING_PDF_BG'), 'error');
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUNDS_PDF_BG_UPDATED'));
		}

		$this->setRedirect('index.php?option=com_reddesign&view=designtype&id=' . $designId . '&tab=backgrounds');
		$this->redirect();
	}
}