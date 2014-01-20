<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.application.component.controller');

$jinput = JFactory::getApplication()->input;

require_once JPATH_LIBRARIES . '/redcore/bootstrap.php';

// Register component prefix
JLoader::registerPrefix('Reddesign', __DIR__);

// Register library prefix
RLoader::registerPrefix('Reddesign', JPATH_LIBRARIES . '/reddesign');

// Load CSS file
RHelperAsset::load('site.css');

// Set the controller page
$controller = $jinput->getCmd('view', 'designtypes');

if (!file_exists(JPATH_COMPONENT . '/controllers/' . $controller . '.php'))
{
	$controller = 'designtypes';
	$jinput->set('view', 'designtype');
}

require_once JPATH_COMPONENT . '/controllers/' . $controller . '.php';

// Set a default task if none is present, this is needed to be able to override the display task
// $jinput->set('task', $jinput->getCmd('task', $jinput->get('view') . '.execute'));
$jinput->set('task', $jinput->getCmd('task', $jinput->get('view') . '.display'));

// Execute the controller
$controller = JControllerLegacy::getInstance('reddesign');
$controller->execute($jinput->get('task'));
$controller->redirect();
