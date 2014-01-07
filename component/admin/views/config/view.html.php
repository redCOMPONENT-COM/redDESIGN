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
 * Config View
 *
 * @package     Reddesign.Backend
 * @subpackage  Views
 * @since       2.0
 */
class ReddesignViewConfig extends ReddesignView
{
	/**
	 * @var  JForm
	 */
	protected $form;

	/**
	 * Do we have to display a sidebar ?
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
		$this->form	= $this->get('Form');

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDDESIGN_CONFIG_GENERAL_CONFIGURATION');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_reddesign'))
		{
			$save = RToolbarBuilder::createSaveButton('config.apply');
			$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('config.save');
			$cancel = RToolbarBuilder::createCloseButton('config.cancel');

			$group->addButton($save)
				->addButton($saveAndClose)
				->addButton($cancel);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
