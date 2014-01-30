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
 * The clipart edit controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class ReddesignControllerClipart extends RControllerForm
{
	/**
	 * Method to save a record. We need first to upload clipart files,
	 * than after that we can save DB clipart record to the #__redddesign_cliparts.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		$config = ReddesignEntityConfig::getInstance();
		$id = $this->input->getInt('id', null);
		$file = $this->input->files->get('jform');

		if (!empty($file['clipartFile']))
		{
			$file = $file['clipartFile'];
		}

		$uploaded_file = null;

		// If file has been uploaded, process it
		if (!empty($file['type']))
		{
			// Upload the file
			$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'cliparts', $config->getMaxSVGFileSize(), 'svg');

			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/cliparts/' . $uploaded_file['mangled_filename']))
			{
				$data = $this->input->post->get('jform', array(), 'array');

				if (empty($data['name']))
				{
					$data['name'] = $file['name'];
				}

				$data['clipartFile'] = $uploaded_file['mangled_filename'];

				$this->input->post->set('jform', $data);

				if (!empty($id))
				{
					/** @var ReddesignTableClipart $clipartTable */
					$clipartTable = RTable::getAdminInstance('Clipart');
					$clipartTable->load($id);

					if (isset($clipartTable->id) && $clipartTable->id > 0)
					{
						// Delete old clipart
						if (JFile::exists(JPATH_SITE . '/media/com_reddesign/cliparts/' . $clipartTable->clipartFile))
						{
							JFile::delete(JPATH_SITE . '/media/com_reddesign/cliparts/' . $clipartTable->clipartFile);
						}
					}
				}
			}
			else
			{
				$recordId = $this->input->getInt($urlVar, null);
				$this->setMessage(JText::_('COM_REDDESIGN_CLIPART_ERROR_UPLOAD_INPUT'), 'error');

				// Redirect back to the edit screen.
				$this->setRedirect(
					$this->getRedirectToItemRoute($this->getRedirectToItemAppend($recordId, $urlVar))
				);

				return false;
			}
		}

		return parent::save($key, $urlVar);
	}
}
