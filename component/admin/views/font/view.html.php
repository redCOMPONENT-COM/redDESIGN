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

	/**
	 * Application configuration
	 *
	 * @var  boolean
	 */
	protected $configuration = false;

	/**
	 * @var array
	 */
	public $typographies = null;

	/**
	 * Do we have to display a topbar inner layout ?
	 *
	 * @var  boolean
	 */
	protected $displayTopBarInnerLayout = false;

	/**
	 * Do not display the Joomla back button on edit screen
	 *
	 * @var  boolean
	 */
	protected $displayBackToJoomla = false;

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

		if ($this->item->id > 0)
		{
			$fontStyleDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($this->item->id);

			if (!empty($fontStyleDeclaration))
			{
				$document = JFactory::getDocument();
				$document->addStyleDeclaration($fontStyleDeclaration);
			}
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
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('font.save2new');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew);

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
