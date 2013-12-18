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
 * User View
 *
 * @package     Reddesign.Backend
 * @subpackage  Views
 * @since       1.0
 */
class ReddesignViewFont extends ReddesignView
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
	protected $fontThumbnail;

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

		if (empty($this->item->default_width))
		{
			$this->item->default_width = 0.99999;
		}

		if (empty($this->item->default_height))
		{
			$this->item->default_height = 0.99999;
		}

		if (empty($this->item->default_caps_height))
		{
			$this->item->default_caps_height = 0.99999;
		}

		if (empty($this->item->default_baseline_height))
		{
			$this->item->default_baseline_height = 0.99999;
		}

		$this->fontThumbnail = substr($this->item->font_file, 0, -3) . 'png';

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
		$title = JText::_('COM_REDDESIGN_FONT_TITLE');
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

		$save = RToolbarBuilder::createSaveButton('font.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('font.save');

		$group->addButton($save);
		$group->addButton($saveAndClose);

		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('font.save2new');
		$group->addButton($saveAndNew);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('font.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('font.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
