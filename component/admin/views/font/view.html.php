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
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');

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
		$state = $isNew ? JText::_('JNEW') : JText::_('JEDIT');

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

		$save = RToolbarBuilder::createSaveButton('user.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('user.save');

		$group->addButton($save);
		$group->addButton($saveAndClose);

		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('user.save2new');
		$group->addButton($saveAndNew);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('user.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('user.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
