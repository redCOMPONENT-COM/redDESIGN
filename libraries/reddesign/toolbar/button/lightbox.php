<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Represents an alert button.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
class ReddesignToolbarButtonLightbox extends RToolbarButton
{
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $rel;

	/**
	 * Constructor.
	 *
	 * @param   string  $url        Url to the view which will be displayed in the lightbox.
	 * @param   string  $text       The button text.
	 * @param   string  $iconClass  The icon class.
	 * @param   string  $class      The button class.
	 * @param   string  $rel        Rel attribute added to the hyperlink tag.
	 */
	public function __construct($url, $text, $iconClass, $class = '', $rel = '')
	{
		parent::__construct($text, $iconClass, $class);

		$this->url = $url;
		$this->rel = $rel;
	}

	/**
	 * Get the url.
	 *
	 * @return  string  The url.
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Get the rel.
	 *
	 * @return  string  The rel.
	 */
	public function getRel()
	{
		return $this->rel;
	}

	/**
	 * Render the button.
	 *
	 * @return  string  The rendered button.
	 */
	public function render()
	{
		return RLayoutHelper::render('toolbar.button.lightbox', array('button' => $this));
	}
}
