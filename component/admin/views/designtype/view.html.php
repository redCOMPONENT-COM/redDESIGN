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
	 * @var  object
	 */
	protected $document;

	/**
	 * @var array
	 */
	protected $areas = array();

	/**
	 * @var  object
	 */
	protected $productionBackground = null;

	/**
	 * @var  object
	 */
	protected $params;

	/**
	 * @var array
	 */
	protected $backgrounds = array();

	/**
	 * @var float
	 */
	protected $ratio;

	/**
	 * @var array
	 */
	protected $inputFieldOptions = array();

	/**
	 * @var array
	 */
	protected $fonts = array();

	/**
	 * @var array
	 */
	protected $alignmentOptions = array();

	/**
	 * @var array
	 */
	protected $fontsOptions = array();

	/**
	 * @var string
	 */
	protected $unit;

	/**
	 * @var float
	 */
	protected $pxToUnit;

	/**
	 * @var float
	 */
	protected $unitToPx;

	/**
	 * @var float
	 */
	protected $imageWidth;

	/**
	 * @var float
	 */
	protected $imageHeight;

	/**
	 * @var array
	 */
	protected $backgroundTypeOptions = array();

	/**
	 * Do not display the sidebar
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

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
		$this->document = JFactory::getDocument();
		$this->params	= JComponentHelper::getParams('com_reddesign');

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

						$epsFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $this->productionBackground->eps_file;
						$previewFileLocation = JPATH_ROOT . '/media/com_reddesign/assets/backgrounds/' . $this->productionBackground->image_path;

						// Read EPS size.
						$im = new Imagick;
						$im->readImage($epsFileLocation);
						$dimensions = $im->getImageGeometry();
						$this->imageWidth = $dimensions['width'];
						$this->imageHeight = $dimensions['height'];

						// Read preview size, for scaling.
						$previewImageSize = getimagesize($previewFileLocation);

						// Scaling ratio
						$this->ratio = $previewImageSize[0] / $this->imageWidth;

						// Get all areas existing in the database for this specific background.
						$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true));
						$areasModel->setState('reddesign_background_id', $background->reddesign_background_id);
						$areas = $areasModel->getItems();
					}
				}
			}

			$this->areas = $areas;

			$this->inputFieldOptions = array(
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTBOX')),
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_DESIGNTYPE_DESIGN_AREAS_TEXTAREA'))
			);

			$this->alignmentOptions = array(
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_COMMON_SELECT')),
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_COMMON_LEFT')),
				JHtml::_('select.option', '2', JText::_('COM_REDDESIGN_COMMON_RIGHT')),
				JHtml::_('select.option', '3', JText::_('COM_REDDESIGN_COMMON_CENTER'))
			);

			// Get all fonts in the system to be choosen or not for the current design.
			$fontsModel = RModel::getAdminInstance('Fonts', array('ignore_request' => true));
			$this->fonts = $fontsModel->getItems();
			$this->fontsOptions = array();

			foreach ($this->fonts as $font)
			{
				$this->fontsOptions[] = JHtml::_('select.option', $font->id, $font->name);
			}

			$this->backgroundTypeOptions = array(
				JHtml::_('select.option', '1', JText::_('COM_REDDESIGN_PRODUCTION_BG')),
				JHtml::_('select.option', '0', JText::_('COM_REDDESIGN_PREVIEW_BG'))
			);

			// Unit for measures.
			$this->unit = $this->params->get('unit', 'px');

			if ($this->unit == 'cm')
			{
				/**
				 * Default DPI in Imagick is used and it is 72 DPI.
				 * Thag gives us 1px = 0.035278cm.
				 */
				$this->pxToUnit = '0.035277778';

				// From above 1cm = 28,346456514px.
				$this->unitToPx = '28.346456514';
			}
			elseif ($this->unit == 'mm')
			{
				/**
				 * Default DPI in Imagick is used and it is 72 DPI.
				 * Thag gives us 1px = 0.35277778mm.
				 */
				$this->pxToUnit = '0.35277778';

				// From above 1mm = 2,834645651px.
				$this->unitToPx = '2.834645651';
			}
			else
			{
				$this->pxToUnit = '1';
				$this->unitToPx = '1';
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
		return JText::_('COM_REDDESIGN_DESIGNTYPE_HEADER');
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
