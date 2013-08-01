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
 * Accessory Type Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerAccessorytypes extends FOFController
{
	/**
	 * Uploads image and thumbnail files for the accessory type.
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  array  Returns $data.
	 */
	public function onBeforeApplySave(&$data)
	{
		// On edit, retrieve from database the old images that will be replaced (later we will remove them to keep system storage resources clean)
		if (!!$data['reddesign_accessorytype_id'])
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->qn(array('sample_image', 'sample_thumb')))
				->from($db->qn('#__reddesign_accessorytypes'))
				->where($db->qn('reddesign_accessorytype_id') . ' = ' . $db->q((int) $data['reddesign_accessorytype_id']));

			$db->setQuery($query);
			$db->execute();
			$oldImages = $db->loadObject();
		}

		$imageFile = $this->input->files->get('sample_image', null);
		$thumbFile = $this->input->files->get('sample_thumb', null);

		if (!empty($imageFile['name']) || !empty($thumbFile['name']))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/helpers/file.php';
			$fileHelper = new ReddesignHelperFile;

			$params = JComponentHelper::getParams('com_reddesign');
		}

		// Code for managing image and thumbnail.
		if (!empty($imageFile['name']))
		{
			$uploadedImageFile = $fileHelper->uploadFile(
				$imageFile,
				'accessorytypes',
				$params->get('max_accessorytype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['sample_image'] = $uploadedImageFile['mangled_filename'];

			// Delete old Image on edit
			if (!!$data['reddesign_accessorytype_id'])
			{
				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/' . $oldImages->sample_image))
				{
					JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/' . $oldImages->sample_image);
				}
			}
		}


		// If sample_thumb field is empty than use sample_image.
		$uploadedThumbFile = null;

		if (!empty($thumbFile['name']))
		{
			$uploadedThumbFile = $fileHelper->uploadFile(
				$thumbFile,
				'accessorytypes/thumbnails',
				$params->get('max_accessorytype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['sample_thumb'] = $uploadedThumbFile['mangled_filename'];
		}
		else
		{
			$dest = JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $uploadedImageFile['mangled_filename'];
			JFile::copy($uploadedImageFile['filepath'], $dest);
			$data['sample_thumb'] = $uploadedImageFile['mangled_filename'];
			$uploadedThumbFile['filepath'] = $dest;
		}

		if (JFile::exists($uploadedThumbFile['filepath']))
		{
			$im = new Imagick;
			$im->readImage($uploadedThumbFile['filepath']);
			$im->thumbnailImage($params->get('max_accessorytype_thumbnail_width', 64), $params->get('max_accessorytype_thumbnail_height', 64), true);
			$im->writeImage();
			$im->clear();
			$im->destroy();
		}

		// Delete old Thumbnail on edit
		if (!!$data['reddesign_accessorytype_id'])
		{
			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $oldImages->sample_thumb))
			{
				JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $oldImages->sample_thumb);
			}
		}

		return $data;
	}
}