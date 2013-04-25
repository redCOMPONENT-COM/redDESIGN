<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator.Frontcontroller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

// Include FoF
JLoader::import('fof.include');

// Register component prefix.
JLoader::registerPrefix('RedDesign', __DIR__);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_reddesign'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Dispatch
FOFDispatcher::getTmpInstance('com_reddesign')->dispatch();