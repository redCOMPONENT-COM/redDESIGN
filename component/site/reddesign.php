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

// Include FoF
JLoader::import('fof.include');

// Dispatch
FOFDispatcher::getTmpInstance('com_reddesign')->dispatch();