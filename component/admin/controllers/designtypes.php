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
 * Design Type Controller.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignControllerDesigntypes extends FOFController
{
	/**
	 * Uploads image and thumbnail files for the design type.
	 *
	 * @param   array  &$data  data filled in the edit form
	 *
	 * @return  array  Returns $data.
	 */
	public function onBeforeApplySave(&$data)
	{
		$uploadedThumbFile = null;
		$params = JComponentHelper::getParams('com_reddesign');
		$fileHelper = null;
		$oldImages = null;

		// On edit, retrieve from database the old images that will be replaced (later we will remove them to keep system storage resources clean)
		if (!!$data['reddesign_designtype_id'])
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->qn(array('sample_image', 'sample_thumb')))
				->from($db->qn('#__reddesign_designtypes'))
				->where($db->qn('reddesign_designtype_id') . ' = ' . $db->q((int) $data['reddesign_designtype_id']));

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
		}

		// Code for managing image and thumbnail.
		if (!empty($imageFile['name']))
		{
			$uploadedImageFile = $fileHelper->uploadFile(
				$imageFile,
				'designtypes',
				$params->get('max_designtype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['sample_image'] = $uploadedImageFile['mangled_filename'];

			// Delete old Image on edit
			if (!!$data['reddesign_designtype_id'])
			{
				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/designtypes/' . $oldImages->sample_image))
				{
					JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/designtypes/' . $oldImages->sample_image);
				}
			}

			// If no thumbnail has been uploaded and user has checked the generate thumbnail based on uploaded image
			if (empty($thumbFile['name']) && $this->input->getBool('autoGenerateThumbCheck', false))
			{
				$dest = JPATH_ROOT . '/media/com_reddesign/assets/designtypes/thumbnails/' . $uploadedImageFile['mangled_filename'];
				JFile::copy($uploadedImageFile['filepath'], $dest);
				$data['sample_thumb'] = $uploadedImageFile['mangled_filename'];
				$uploadedThumbFile['filepath'] = $dest;
			}
		}

		// If a thumbnail has been uploaded
		if (!empty($thumbFile['name']))
		{
			$uploadedThumbFile = $fileHelper->uploadFile(
				$thumbFile,
				'designtypes/thumbnails',
				$params->get('max_designtype_image_size', 2),
				'jpg,JPG,jpeg,JPEG,png,PNG'
			);
			$data['sample_thumb'] = $uploadedThumbFile['mangled_filename'];
		}

		if (!is_null($uploadedThumbFile))
		{
			if (JFile::exists($uploadedThumbFile['filepath']))
			{
				$im = new Imagick;
				$im->readImage($uploadedThumbFile['filepath']);
				$im->thumbnailImage($params->get('max_designtype_thumbnail_width', 100), $params->get('max_designtype_thumbnail_height', 100), true);
				$im->writeImage();
				$im->clear();
				$im->destroy();
			}

			// Delete old Thumbnail on edit
			if (!!$data['reddesign_designtype_id'])
			{
				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/designtypes/thumbnails/' . $oldImages->sample_thumb))
				{
					JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/designtypes/thumbnails/' . $oldImages->sample_thumb);
				}
			}
		}

		// Prepare data for description together with read more option.
		if (isset($data['description']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $data['description']);

			if ($tagPos == 0)
			{
				$data['intro_description'] = $data['description'];
			}
			else
			{
				$intro_description = preg_split($pattern, $data['description'], 2);
				$data['intro_description'] = $intro_description[0];
			}
		}

		$relatedDesigntypes = $this->input->get('related_designtypes', null, 'array');

		if (!$relatedDesigntypes)
		{
			$data['related_designtypes'] = '';
		}
		else
		{
			$data['related_designtypes'] = implode(',', $relatedDesigntypes);
		}

		return $data;
	}
}
