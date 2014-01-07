<?php
/**
 * @package     RedDesign.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Config table.
 *
 * @package     RedDesign.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableConfig extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_config';

	/**
	 * @var  integer
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $params;
}
