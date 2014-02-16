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
class ReddesignControllerFont extends RControllerForm
{
	/**
	 * Method to save a record. We need first to upload font files,
	 * than after that we can save DB font record to the #__redddesign_fonts.
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
		$file = $file['font_file'];
		$uploaded_file = null;

		// If it is not new one than just save the data to the db.
		if (empty($id))
		{
			// If file has been uploaded, process it
			if (!empty($file['name']) && !empty($file['type']))
			{
				// Upload the font file
				$uploaded_file = ReddesignHelpersFile::uploadFile($file, 'fonts', $config->getMaxSVGFileSize(), 'woff');
			}

			// Delete font .ttf file
			if (JFile::exists(JPATH_SITE . '/media/com_reddesign/fonts/' . $uploaded_file['mangled_filename']))
			{
				$data = $this->input->post->get('jform', array(), 'array');

				if (empty($data['name']))
				{
					$data['name'] = str_replace('.woff', '', $file['name']);
				}

				$data['font_file'] = $uploaded_file['mangled_filename'];

				$this->input->post->set('jform', $data);

				return parent::save($key, $urlVar);
			}
			else
			{
				$recordId = $this->input->getInt($urlVar, null);
				$this->setMessage(JText::_('COM_REDDESIGN_FONT_ERROR_UPLOAD_INPUT'), 'error');

				// Redirect back to the edit screen.
				$this->setRedirect(
					$this->getRedirectToItemRoute($this->getRedirectToItemAppend($recordId, $urlVar))
				);

				return false;
			}
		}
		else
		{
			return parent::save($key, $urlVar);
		}
	}

	/**
	 * Saves character specific data.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxSaveChar()
	{
		$model = $this->getModel();
		$table = $model->getTable('Char');

		$data = array();
		$data['id']                = $this->input->getInt('id', null);
		$data['font_char']         = $this->input->getString('font_char', '');
		$data['width']             = $this->input->getFloat('width', null);
		$data['height']            = $this->input->getFloat('height', null);
		$data['typography']        = $this->input->getInt('typography', null);
		$data['typography_height'] = $this->input->getFloat('typography_height', null);
		$data['font_id']           = $this->input->getInt('font_id', null);

		if (!$table->bind($data))
		{
			$data['message'] = JText::_('COM_REDDESIGN_FONT_CHAR_CANT_SAVE_CHAR_BINDING');
			$data['status']  = 'error';
		}
		elseif (!$table->store($data))
		{
			$data['message'] = JText::_('COM_REDDESIGN_FONT_CHAR_CANT_SAVE_CHAR_STORING');
			$data['status']  = 'error';
		}
		else
		{
			$data['message'] = JText::sprintf('COM_REDDESIGN_FONT_CHAR_SUCCESSFULLY_SAVED_CHAR', $data['font_char']);
			$data['status']  = 'success';
		}

		if (empty($data['id']))
		{
			$data['id'] = $table->id;
		}

		echo json_encode($data);

		JFactory::getApplication()->close();
	}

	/**
	 * Deletes a specific character from the font view.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function ajaxRemoveChar()
	{
		$model = $this->getModel();
		$table = $model->getTable('Char');
		$id = $this->input->getInt('id', null);

		if ($table->delete($id))
		{
			echo 'success';
		}
		else
		{
			echo JText::_('COM_REDDESIGN_FONT_CHAR_ERROR_WHILE_REMOVING');
		}

		JFactory::getApplication()->close();
	}
}
