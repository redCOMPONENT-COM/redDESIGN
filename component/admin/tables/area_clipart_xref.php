<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_COMPONENT . '/helpers');

/**
 * Design Type table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTablearea_clipart_xref extends RTable
{
	/**
	 * The name of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reddesign_area_clipart_xref';

	/**
	 * @var  integer
	 */
	public $id;

	/**
	 * @var  integer
	 */
	public $areaId;

	/**
	 * @var  integer
	 */
	public $clipartId;
}
