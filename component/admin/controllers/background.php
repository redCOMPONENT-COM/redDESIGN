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
		$uploaded_file = null;
		$backgroundModel = $this->getModel();

		$data = $this->input->get('jform', array(), 'array');

		$file = $this->input->files->get('jform');
		$file = $file['bg_svg_file'];

		// Get component configuration
		$config = ReddesignEntityConfig::getInstance();

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
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'backgrounds', $config->getMaxSVGFileSize());

			if (!$uploaded_file)
			{
				echo json_encode(array(0, '<div class="alert alert-error">' . JText::_('COM_REDDESIGN_BACKGROUND_ERROR_UPLOAD_FAILED') . '</div>'), true);
				$app->close();
			}
		}

		// On edit
		if (!empty($data['id']))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('svg_file'))
				->from($db->qn('#__reddesign_backgrounds'))
				->where($db->qn('id') . ' = ' . $db->q((int) $data['id']));

			$db->setQuery($query);
			$db->execute();
			$oldImages = $db->loadObject();

			// If images has been updated remove old images
			if ($updatedSVG)
			{
				if ($updatedSVG)
				{
					// Delete old SVG
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->svg_file))
					{
						JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $oldImages->svg_file);
					}
				}
			}
			else
			{
				$data['svg_file'] = $oldImages->svg_file;
			}
		}
		else
		{
			// Update the database with the new path of the SVG file.
			$data['svg_file'] = $uploaded_file['mangled_filename'];
		}

		// If this new background will be the PDF Production background, switch it against the previous production background.
		if (!empty($data['isProductionBg']))
		{
			// Set all other backgrounds as non PDF backgrounds.
			$backgroundModel->unsetAllIsProductionBg($data['designtype_id']);
		}

		// If this new background will be the preview background, switch it against the previous preview background.
		if (!empty($data['isDefaultPreview']))
		{
			// Set all other backgrounds as non PDF backgrounds.
			$backgroundModel->unsetAllIsDefaultPreview($data['designtype_id']);
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

		if (empty($data['useCheckerboard']))
		{
			$data['useCheckerboard'] = 0;
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
