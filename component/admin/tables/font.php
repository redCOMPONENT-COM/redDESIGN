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
 * Font Table
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignTableFont extends FOFTable
{
	/**
	 * Performs validation check for field values
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function check()
	{
		$app = JFactory::getApplication();

		if (empty($this->font_file))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_FONT_FILE'), 'error');

			return false;
		}

		if (empty($this->default_width))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_WIDTH'), 'error');

			return false;
		}

		if (empty($this->default_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_HEIGHT'), 'error');

			return false;
		}

		if (empty($this->default_caps_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_CAPS_HEIGHT'), 'error');

			return false;
		}

		if (empty($this->default_baseline_height))
		{
			$app->enqueueMessage(JText::_('COM_REDDESIGN_FONT_ERROR_EMPTY_DEFAULT_BASELINE_HEIGHT'), 'error');

			return false;
		}

		return parent::check();
	}

	/**
	 * Removes font related files after erasing the font in database
	 *
	 * @param   int  $oid  font database row id
	 *
	 * @return bool|void
	 */
	protected function onAfterDelete($oid)
	{
		// Delete font and font thumb file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_thumb);
		}

		// Delete font and font file
		if (JFile::exists(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file))
		{
			JFile::delete(JPATH_SITE . '/media/com_reddesign/assets/fonts/' . $this->font_file);
		}

		parent::onAfterDelete($oid);
	}
}
