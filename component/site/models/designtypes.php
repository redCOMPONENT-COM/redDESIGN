<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');


/**
 * Designtype Model.
 *
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @since       1.0
 */
class ReddesignModelDesigntypes extends RModelList
{
	/**
	 * Designtype Id
	 *
	 * @var  int
	 */
	private $id;

	/**
	 * Get Id
	 *
	 * @return int  Designtype Id.
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set Id
	 *
	 * @param   int  $id  Designtype Id.
	 *
	 * @return void.
	 */
	public function setId($id)
	{
		$this->id = (int) $id;
	}

	/**
	 * Retrieve all backgrounds from the database that belongs to the current design.
	 *
	 * @return array  Array of backgrounds.
	 */
	public function getBackgrounds()
	{
		$backgroundModel = RModel::getAdminInstance('Backgrounds', array(), 'com_reddesign');
		$backgroundModel->setState('designtype_id', $this->getId());

		$backgrounds = $backgroundModel->getItems();

		return $backgrounds;
	}

	/**
	 * Retrieve the production file background from the backgrounds list.
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no PDF background.
	 */
	public function getProductionBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isProductionBg)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Production background", just text.
		return false;
	}

	/**
	 * Retrieve the preview background to be used as background for previews.
	 *
	 * @return mixed  Returns the array that describes a background or false if there is no preview background.
	 */
	public function getPreviewBackground()
	{
		$backgrounds = $this->getBackgrounds();

		foreach ($backgrounds as $background)
		{
			if ($background->isDefaultPreview)
			{
				return $background;
			}
		}

		// If this point is reached means that this design don't have "Preview background" set.
		return false;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
					->select('d.*')
					->from($db->qn('#__reddesign_designtypes', 'd'));

		if ($this->id > 0)
		{
			$query->where('d.id = ' . (int) $this->id);
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'd.name';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}
}
