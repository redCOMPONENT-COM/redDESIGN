<?php
/**
 * @package     Redshopb.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Character Table
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableChar extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'reddesign_chars';

	/**
	 * @var  integer
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $font_char;

	/**
	 * @var  double
	 */
	public $width;

	/**
	 * @var  double
	 */
	public $height;

	/**
	 * @var  integer
	 */
	public $typography;

	/**
	 * @var  double
	 */
	public $typography_height;

	/**
	 * @var  integer
	 */
	public $font_id;
}
