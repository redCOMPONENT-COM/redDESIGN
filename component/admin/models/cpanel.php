<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * CPanel Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelCpanel extends FOFModel
{
	/**
	 * Trick to avoid getting warning PHP messages by the JDatabase layer on a FoF view with no table
	 *
	 * @param   array  $config  Optional configuration array
	 *
	 * @see https://github.com/akeeba/fof/wiki/Creating%20a%20view%20without%20a%20database%20table
	 */
	public function __construct($config = array())
	{
		$config['table'] = 'fonts';
		parent::__construct($config);
	}

	/**
	 * Trick when FoF view has no table: by returning a null value, the system is happy because a value is returned.
	 *
	 * @param   null  $id  Id of the element to be returned
	 *
	 * @return FOFTable|null
	 */
	public function getItem($id = null)
	{
		return null;
	}
}
