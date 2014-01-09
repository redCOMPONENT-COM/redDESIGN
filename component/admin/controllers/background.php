<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * The font edit controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class ReddesignControllerBackground extends RControllerForm
{
	/**
	 * Method for load Background Form by AJAX
	 *
	 * @return void
	 */
	public function ajaxBackgroundSave()
	{
		// Init vars
		$app = JFactory::getApplication();
		$updatedSVG = false;
		$updatedThumbnail = false;
		$uploaded_file = null;
		$backgroundModel = $this->getModel();

		$data = $this->input->get('jform', array(), 'array');

		$file = $this->input->files->get('jform');
		$file = $file['bg_svg_file'];

		// Get Thumbnail if has been added
		$thumbFile = $this->input->files->get('thumbnail', null);
		$thumbPreviewFile = null;

		// Get component configuration
		$config = ReddesignEntityConfig::getInstance();
		$thumbnailWidth = $config->getMaxBackgroundThumbWidth();
		$thumbnailHeight = $config->getMaxBackgroundThumbHeight();

		// If file has has not been uploaded
		if (empty($file['name']) || empty($file['type']))
		{
			// If is a new background and the file is not attached return error
			if (!$data['id'])
			{
				echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_NO_FILE') . '</div>'), true);
				$app->close();
			}
		}
		elseif (empty($data['name']))
		{
			echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_NO_TITLE') . '</div>'), true);
			$app->close();
		}
		else
		{
			$updatedSVG = true;

			// Upload the background file
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'backgrounds');

			if (!$uploaded_file)
			{
				echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_UPLOAD_FAILED') . '</div>'), true);
				$app->close();
			}

			// If no thumbnail file has been attached generate one based on the Background SVG
			if (!$thumbFile['name'])
			{
				// Create a image preview thumbnail based on the SVG file.
				$thumbPreviewFile = str_replace('.svg', '.png', $uploaded_file['mangled_filename']);
				$thumbPreviewFile = JPATH_ROOT . '/media/com_reddesign/backgrounds/thumbnails/' . $thumbPreviewFile;

				$im = new Imagick;
				$im->readImage(JPATH_ROOT . '/media/com_reddesign/backgrounds/' . $uploaded_file['mangled_filename']);
				$im->thumbnailImage($thumbnailWidth, $thumbnailHeight, true);
				$im->writeImage('png:' . $thumbPreviewFile);
				$im->clear();
				$im->destroy();
			}
		}

		// If thumbnail has been attached upload it and set component parameters max size
		if ($thumbFile['name'])
		{
			$updatedThumbnail = true;

			// Upload the attached thumbnail
			$uploadedThumbFile = ReddesignHelpersFile::uploadFile(
				$thumbFile,
				'backgrounds/thumbnails',
				$config->getMaxSVGFileSize(),
				'jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF'
			);

			$im = new Imagick;
			$im->readImage($uploadedThumbFile['filepath']);
			$im->thumbnailImage($thumbnailWidth, $thumbnailHeight, true);
			$im->writeImage($uploadedThumbFile['filepath']);
			$im->clear();
			$im->destroy();

			$thumbPreviewFile = $uploadedThumbFile['mangled_filename'];
		}

		// On edit
		if (!!$data['id'])
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->qn(array('svg_file', 'thumbnail')))
				->from($db->qn('#__reddesign_backgrounds'))
				->where($db->qn('id') . ' = ' . $db->q((int) $data['id']));

			$db->setQuery($query);
			$db->execute();
			$oldImages = $db->loadObject();

			// If images has been updated remove old images
			if ($updatedSVG || $updatedThumbnail)
			{
				if ($updatedSVG)
				{
					// Delete old SVG
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->svg_file))
					{
						JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->svg_file);
					}
				}

				if ($updatedThumbnail)
				{
					// Delete background old thumbnail
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/thumbnails/' . $oldImages->thumbnail))
					{
						JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/thumbnails/' . $oldImages->thumbnail);
					}
				}
			}
			else
			{
				$data['svg_file'] = $oldImages->svg_file;
				$data['thumbnail'] = $oldImages->thumbnail;
			}
		}
		else
		{
			// Update the database with the new path of the SVG file.
			$data['svg_file'] = $uploaded_file['mangled_filename'];

			if ($thumbPreviewFile)
			{
				$data['thumbnail'] = $thumbPreviewFile;
			}
		}

		// If this new background will be the PDF Production background, switch it against the previous production background.
		if ((int) $data['isProductionBg'])
		{
			$designId = (int) $data['designtype_id'];

			// Set all other backgrounds as non PDF backgrounds.
			$backgroundModel->unsetAllIsProductionBg($designId);
		}

		// If this new background will be the preview background, switch it against the previous preview background.
		if ((int) $data['isDefaultPreview'])
		{
			$designId = (int) $data['designtype_id'];

			// Set all other backgrounds as non PDF backgrounds.
			$backgroundModel->unsetAllIsDefaultPreview($designId);
		}

		if (empty($data['isProductionBg']))
		{
			$data['isProductionBg'] = 0;
		}

		if (empty($data['isDefaultPreview']))
		{
			$data['isDefaultPreview'] = 0;
		}

		if (empty($data['isPreviewBg']))
		{
			$data['isPreviewBg'] = 0;
		}

		$backgroundTable = RTable::getAdminInstance('Background');

		$backgroundTable->bind($data);

		if (!$backgroundTable->store())
		{
			echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_STORE_FAILED') . '</div>'), true);
			$app->close();
		}

		$app->enqueueMessage(JText::_('COM_REDDESIGN_BACKGROUND_STORE_SUCCESS'), 'message');
		$session = JFactory::getSession();
		$session->set('application.queue', $app->getMessageQueue());
		echo json_encode(array(1, ''), true);
		$app->close();
	}
}
