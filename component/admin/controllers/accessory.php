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
 * Accessory Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerAccessory extends FOFController
{
	/**
	 * Uploads image and thumbnail files for the accessory.
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  array  Returns $data.
	 */
	public function onBeforeApplySave(&$data)
	{
		$imageFile = $this->input->files->get('image', null);

		// Code for managing image and thumbnail.
		if (!empty($imageFile['name']))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/helpers/file.php';
			$fileHelper = new ReddesignHelperFile;
			$params = JComponentHelper::getParams('com_reddesign');

			$uploadedImageFile = $fileHelper->uploadFile(
				$imageFile,
				'accessories',
				$params->get('max_accessory_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['image'] = $uploadedImageFile['mangled_filename'];

			$thumbFile = $this->input->files->get('thumbnail', null);
			$uploadedThumbFile = null;

			// If thumbnail field is empty than use image.
			if (!empty($thumbFile['name']))
			{
				$uploadedThumbFile = $fileHelper->uploadFile(
					$thumbFile,
					'accessories/thumbnails',
					$params->get('max_accessory_image_size', 2),
					'jpg,JPG,jpeg,JPEG,png,PNG'
				);
				$data['thumbnail'] = $uploadedThumbFile['mangled_filename'];
			}
			else
			{
				$dest = JPATH_ROOT . '/media/com_reddesign/assets/accessories/thumbnails/' . $uploadedImageFile['mangled_filename'];
				JFile::copy($uploadedImageFile['filepath'], $dest);
				$data['thumbnail'] = $uploadedImageFile['mangled_filename'];
				$uploadedThumbFile['filepath'] = $dest;
			}

			if (JFile::exists($uploadedThumbFile['filepath']))
			{
				$im = new Imagick;
				$im->readImage($uploadedThumbFile['filepath']);
				$im->thumbnailImage($params->get('max_accessory_thumbnail_width', 210), $params->get('max_accessory_thumbnail_height', 140), true);
				$im->writeImage();
				$im->clear();
				$im->destroy();
			}
		}

		return $data;
	}
}