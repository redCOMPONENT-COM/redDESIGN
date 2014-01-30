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
 * Clipart View
 *
 * @package     Reddesign.Backend
 * @subpackage  Views
 * @since       1.0
 */
class ReddesignViewClipart extends ReddesignView
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
	 * @var string
	 */
	protected $clipartThumbnail;

	/**
	 * We don't need side bar here.
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Application configuration
	 *
	 * @var  boolean
	 */
	protected $configuration = false;

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

		$this->configuration = ReddesignEntityConfig::getInstance();

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
		$title = JText::_('COM_REDDESIGN_CLIPART_TITLE');
		$state = $isNew ? JText::_('COM_REDDESIGN_COMMON_NEW') : JText::_('COM_REDDESIGN_COMMON_EDIT');

		return $title . ' <small>' . $state . '</small>';
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('clipart.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('clipart.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('clipart.save2new');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('clipart.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('clipart.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
