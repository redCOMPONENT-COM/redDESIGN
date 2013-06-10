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
 * Design editor Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @since       1.0
 */
class ReddesignModelReddesign extends FOFModel
{
	// This is a trick to avoid getting warning PHP messages by the JDatabase layer

	public function __construct($config = array()) {
		$config['table'] = 'nothing';
		parent::__construct($config);
	}

	public function getItem($id = null) {
		return null;
	}
}
