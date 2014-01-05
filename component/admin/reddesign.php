<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator.Frontcontroller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

// Register component prefix.
JLoader::registerPrefix('Reddesign', __DIR__);

// Register library prefix
RLoader::registerPrefix('Reddesign', JPATH_LIBRARIES . '/reddesign');

$app = JFactory::getApplication();

// Check access.
if (!JFactory::getUser()->authorise('core.manage', 'com_reddesign'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

// Instanciate and execute the front controller.
$controller = JControllerLegacy::getInstance('Reddesign');
$controller->execute($app->input->get('task'));
$controller->redirect();
