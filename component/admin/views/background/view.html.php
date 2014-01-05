<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Background View
 *
 * @package     Reddesign.Backend
 * @subpackage  Views
 * @since       1.0
 */
class ReddesignViewBackground extends ReddesignView
{
	/**
	 * @var  JForm
	 */
	protected $form;

	/**
	 * @var  object
	 */
	protected $item;

	/**
	 * We don't need side bar here.
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		$isNew = (int) $this->item->id <= 0;
		$title = JText::_('COM_REDDESIGN_BACKGROUND_TITLE');
		$state = $isNew ? JText::_('COM_REDDESIGN_COMMON_NEW') : JText::_('COM_REDDESIGN_COMMON_EDIT');

		return $title . ' <small>' . $state . '</small>';
	}
}
