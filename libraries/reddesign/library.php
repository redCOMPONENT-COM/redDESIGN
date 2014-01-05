<?php
/**
 * Reddesign Library file.
 * Including this file into your application will make redDESIGN available to use.
 *
 * @package    Reddesign.Library
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

// Define redDESIGN Library Folder Path
define('JPATH_REDDESIGN_LIBRARY', __DIR__);

// Load redCORE bootstra
JLoader::import('redcore.bootstrap');

// Register library prefix
RLoader::registerPrefix('Reddesign', JPATH_REDDESIGN_LIBRARY);
