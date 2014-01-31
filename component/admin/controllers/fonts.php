<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Fonts Controller
 *
 * @package     Reddesign.Backend
 * @subpackage  Controllers
 * @since       1.0
 */

class ReddesignControllerFonts extends RControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('add', 'edit');
	}

	/**
	 * Assigns selected fonts to all areas.
	 *
	 * @return void
	 */
	public function fontsToAllAreas()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$cidCount = count($cid);

		$areasModel = RModel::getInstance('Areas', 'ReddesignModel');
		$areas = $areasModel->getItems();
		$areaIds = array();

		foreach ($areas as $area)
		{
			$areaIds[] = $area->id;
		}

		if (empty($cid))
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			$cid = json_encode($cid, JSON_FORCE_OBJECT);

			// Get the model.
			$model = RModel::getInstance('Fonts', 'ReddesignModel');

			// Publish the items.
			try
			{
				$model->fontsToAllAreas($cid, $areaIds);

				$this->setMessage(JText::plural('COM_REDDESIGN_N_FONTS_ASSIGNED', $cidCount));
			}
			catch (Exception $e)
			{
				$this->setMessage(JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error');
			}
		}

		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute($extensionURL));
	}
}
