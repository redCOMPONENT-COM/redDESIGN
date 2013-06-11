<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Frontend dispatcher
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignDispatcher extends FOFDispatcher
{
	public $defaultView = 'background';

	public function onBeforeDispatch()
	{
		$result = parent::onBeforeDispatch();

		if ($result)
		{
			// Load Akeeba Strapper
			include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';

			// @TODO: next line instead of 1 I should attach the md5(version of the component)
			// AkeebaStrapper::$tag = 1;
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQuery();
			AkeebaStrapper::jQueryUI();
			AkeebaStrapper::addCSSfile('media://com_reddesign/assets/css/backend.css');
		}

		return true;
	}
}
