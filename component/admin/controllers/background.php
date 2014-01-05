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
		$updatedEPS = false;
		$updatedThumbnail = false;
		$uploaded_file = null;
		$jpegPreviewFile = null;
		$backgroundModel = $this->getModel();

		$data = $this->input->get('jform', array(), 'array');

		$file = $this->input->files->get('jform');
		$file = $file['bg_eps_file'];

		// Get Eps if has been added
		// $file = $this->input->files->get('bg_eps_file', null);

		// Get Thumbnail if has been added
		$thumbFile = $this->input->files->get('thumbnail', null);
		$thumbPreviewFile = null;

		// Get component Params
		$params = JComponentHelper::getParams('com_reddesign');

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
			$updatedEPS = true;

			// Upload the background file
			// $uploaded_file = $this->uploadFile($file);
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'backgrounds');

			if (!$uploaded_file)
			{
				echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_UPLOAD_FAILED') . '</div>'), true);
				$app->close();
			}

			// Create an image preview of the EPS.
			// @Thong: No need now
			/*$jpegPreviewFile = $this->createBackgroundPreview($uploaded_file['mangled_filename']);

			if (!$jpegPreviewFile)
			{
				return false;
			}*/
			$jpegPreviewFile = $uploaded_file['mangled_filename'];

			// If no thumbnail file has been attached generate one based on the Background EPS
			if (!$thumbFile['name'])
			{
				// Create a image preview thumbnail based on the EPS
				$im = new Imagick;
				$im->readImage(JPATH_ROOT . '/media/com_reddesign/backgrounds/' . $jpegPreviewFile);
				$im->thumbnailImage($params->get('max_background_thumbnail_width', 50), $params->get('max_background_thumbnail_height', 50), true);
				$im->writeImage(JPATH_ROOT . '/media/com_reddesign/backgrounds/thumbnails/' . $jpegPreviewFile);
				$im->clear();
				$im->destroy();
				$thumbPreviewFile = $jpegPreviewFile;
			}
		}

		// If thumbnail has been attached upload it and set component parameters max size
		if ($thumbFile['name'])
		{
			$updatedThumbnail = true;

			// Upload the attached thumbnail
			require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/helpers/file.php';
			$fileHelper = new ReddesignHelperFile;

			$uploadedThumbFile = $fileHelper->uploadFile(
				$thumbFile,
				'backgrounds/thumbnails',
				$params->get('max_eps_file_size', 2),
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

		// On edit
		if (!!$data['id'])
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->qn(array('eps_file', 'image_path', 'thumbnail')))
				->from($db->qn('#__reddesign_backgrounds'))
				->where($db->qn('id') . ' = ' . $db->q((int) $data['id']));

			$db->setQuery($query);
			$db->execute();
			$oldImages = $db->loadObject();

			// If images has been updated remove old images
			if ($updatedEPS || $updatedThumbnail)
			{
				if ($updatedEPS)
				{
					// Delete old EPS
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->eps_file))
					{
						JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->eps_file);
					}

					// Delete old Image
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->image_path))
					{
						JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->image_path);
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
				$data['eps_file'] = $oldImages->eps_file;
				$data['image_path'] = $oldImages->image_path;
				$data['thumbnail'] = $oldImages->thumbnail;
			}
		}
		else
		{
			// Update the database with the new path of the EPS file
			$data['eps_file'] = $uploaded_file['mangled_filename'];

			// Update the database with the new path to the image
			$data['image_path'] = $jpegPreviewFile;

			if ($thumbPreviewFile)
			{
				$data['thumbnail'] = $thumbPreviewFile;
			}
		}

		// If this new background will be the PDF Production background, switch it against the previous production background
		if ((int) $data['isProductionBg'])
		{
			$designId = (int) $data['designtype_id'];

			// Set all other backgrounds as non PDF backgrounds
			$backgroundModel->unsetAllIsProductionBg($designId);
		}

		// If this new background will be the preview background, switch it against the previous preview background
		if ((int) $data['isDefaultPreview'])
		{
			$designId = (int) $data['designtype_id'];

			// Set all other backgrounds as non PDF backgrounds
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

	/**
	 * Method for load Background Form by AJAX
	 *
	 * @return array
	 */
	public function ajaxBackgroundForm()
	{
		$designTypeId = $this->input->getInt('designtype_id', null);

		if ($designTypeId)
		{
			/** @var RedshopbModelUsers $usersModel */

			$view = $this->getView('Background', 'html');
			$model = RModel::getAdminInstance('Background', array('ignore_request' => true));
			$view->setModel($model, true);

			$model->setState('filter.designtypeid', $designTypeId);

			$view->display();
		}

		JFactory::getApplication()->close();
	}
}
