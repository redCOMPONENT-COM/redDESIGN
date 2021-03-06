<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Designtype View
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       2.0
 */

class ReddesignViewDesigntype extends ReddesignView
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
	 * @var array
	 */
	protected $areaType = array();

	/**
	 * @var array
	 */
	protected $areas = array();

	/**
	 * @var  object
	 */
	protected $productionBackground = null;

	/**
	 * @var float
	 */
	public $bgBackendPreviewWidth = 600;

	/**
	 * @var float
	 */
	public $bgBackendPreviewHeight = 400;

	/**
	 * @var string
	 */
	public $unit = 'px';

	/**
	 * @var int
	 */
	public $sourceDpi = 72;

	/**
	 * @var float
	 */
	public $ratio;

	/**
	 * @var float
	 */
	public $unitConversionRatio;

	/**
	 * Production background attributes
	 *
	 * @var  object
	 */
	public $productionBgAttributes = null;

	/**
	 * @var array
	 */
	protected $backgrounds = array();

	/**
	 * @var string
	 */
	protected $selectedFontsDeclaration = '';

	/**
	 * @var array
	 */
	protected $fonts = array();

	/**
	 * Do not display the sidebar
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

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
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function display($tpl = null)
	{
		$this->item 	= $this->get('Item');
		$this->form 	= $this->get('Form');

		// Preview and unit configuration
		$config = ReddesignEntityConfig::getInstance();
		$this->bgBackendPreviewWidth = $config->getMaxSVGPreviewAdminWidth();
		$this->unit = $config->getUnit();
		$this->sourceDpi = $config->getSourceDpi();

		// If it's not a new design
		if (!empty($this->item->id))
		{
			// Get all the backgrounds that belongs to this Design Type item.
			$backgroundsModel = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
			$backgroundsModel->setState('designtype_id', $this->item->id);
			$this->backgrounds = $backgroundsModel->getItems();

			$areas = array();

			if ($this->backgrounds)
			{
				foreach ($this->backgrounds as $background)
				{
					// Get the background image that has been selected to be the Production PDF file image.
					if ($background->isProductionBg)
					{
						$this->productionBackground = $background;

						// Get all areas existing in the database for this specific background.
						$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true));
						$areasModel->setState('filter.background_id', $background->id);
						$areas = $areasModel->getItems();

						$selectedFonts = ReddesignHelpersFont::getSelectedFontsFromArea($areas);
						$this->selectedFontsDeclaration = ReddesignHelpersFont::getFontStyleDeclaration($selectedFonts);
					}
				}

				$this->areas = $areas;

				if (!empty($this->productionBackground) && JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file))
				{
					// Production background measures.
					$xml = simplexml_load_file(JURI::root() . 'media/com_reddesign/backgrounds/' . $this->productionBackground->svg_file);
					$this->productionBgAttributes = $xml->attributes();

					$this->productionBgAttributes->width  = str_replace('px', '', $this->productionBgAttributes->width);
					$this->productionBgAttributes->height = str_replace('px', '', $this->productionBgAttributes->height);
				}
				else
				{
					$this->productionBgAttributes = new stdClass;
					$this->productionBgAttributes->width  = 0;
					$this->productionBgAttributes->height = 0;
				}

				$this->unitConversionRatio = ReddesignHelpersSvg::getUnitConversionRatio($this->unit, $this->sourceDpi);
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
		$title = JText::_('COM_REDDESIGN_DESIGNTYPE_HEADER');
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

		$save = RToolbarBuilder::createSaveButton('designtype.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('designtype.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('designtype.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('designtype.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('designtype.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('designtype.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
