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
 * Font View
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

	public $typographies = null;

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

		$this->fontThumbnail = substr($this->item->font_file, 0, -3) . 'png';

		// Typography options for the chars tab.
		$this->typographies = array(
			JHTML::_('select.option', '0', JText::_('COM_REDDESIGN_SELECT_TYPOGRAPHY')),
			JHTML::_('select.option', '1', JText::_('COM_REDDESIGN_FONT_X_HEIGHT')),
			JHTML::_('select.option', '2', JText::_('COM_REDDESIGN_FONT_CAP_HEIGHT')),
			JHTML::_('select.option', '3', JText::_('COM_REDDESIGN_FONT_BASELINE')),
			JHTML::_('select.option', '4', JText::_('COM_REDDESIGN_FONT_BASELINE_HEIGHT_CAP_HEIGHT'))
		);

		if (empty($this->item->chars))
		{
			$this->item->chars = array();
		}

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
