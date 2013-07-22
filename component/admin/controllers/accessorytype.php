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
class ReddesignControllerAccessorytype extends FOFController
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
		$imageFile = $this->input->files->get('sample_image', null);

		// Code for managing image and thumbnail.
		if (!empty($imageFile['name']))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/helpers/file.php';
			$fileHelper = new ReddesignHelperFile;
			$params = JComponentHelper::getParams('com_reddesign');

			$uploadedImageFile = $fileHelper->uploadFile(
				$imageFile,
				'accessorytypes',
				$params->get('max_accessorytype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['sample_image'] = $uploadedImageFile['mangled_filename'];

			$thumbFile = $this->input->files->get('sample_thumb', null);
			$uploadedThumbFile = null;

			// If sample_thumb field is empty than use sample_image.
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
				$dest = JPATH_ROOT . '/media/com_reddesign/assets/accessorytypes/thumbnails/' . $uploadedImageFile['mangled_filename'];
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
		}

		return $data;
	}
}