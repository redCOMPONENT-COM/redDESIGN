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
 * Backend dispatcher
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       2.0
 */
class ReddesignDispatcher extends FOFDispatcher
{
	/**
	 * Function that gets executed before dispatch the component
	 *
	 * @return bool|void
	 */
	public function onBeforeDispatch()
	{
		$result = parent::onBeforeDispatch();

		if ($result)
		{
			// Load Akeeba Strapper
			include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';

			// @TODO: next line instead of 1 I should attach the md5(version of the component)
			AkeebaStrapper::$tag = 1;
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQueryUI();

		}

		return true;
	}
}
