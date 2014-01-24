<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_COMPONENT . '/helpers');
jimport('joomla.filesystem.folder');

/**
 * Design Type table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class ReddesignTableDesigntype extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reddesign_designtypes';

	/**
	 * @var  integer
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  string
	 */
	public $alias;

	/**
	 * @var  integer
	 */
	public $state = 1;

	/**
	 * @var  integer
	 */
	public $ordering = 0;

	/**
	 * @var  integer
	 */
	public $created_by = null;

	/**
	 * @var  string
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $modified_by = null;

	/**
	 * @var  string
	 */
	public $modified_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $checked_out = null;

	/**
	 * @var  string
	 */
	public $checked_out_time = '0000-00-00 00:00:00';

	/**
	 * @var  string
	 */
	public $fontsizer = 'auto';

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		/** @var ReddesignModelBackgrounds $modelBackgrounds */
		$modelBackgrounds = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
		$modelBackgrounds->setState('designtype_id', $this->id);
		$backgrounds = $modelBackgrounds->getItems();

		if (parent::delete($pk))
		{
			foreach ($backgrounds as $background)
			{
				// Delete background SVG
				if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $background->svg_file))
				{
					JFile::delete(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $background->svg_file);
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		// Create alias for title
		$this->alias = JFilterOutput::stringURLSafe($this->name);

		$data  = JFactory::getApplication()->input->get('jform', array(), 'array');
		$task = JFactory::getApplication()->input->get('task', 'save');

		$originalId = $data['id'];

		if ($task == 'save2copy')
		{
			$this->name = JString::increment($this->name);
		}

		if (parent::store($updateNulls))
		{
			if ($task == 'save2copy')
			{
				$app   = JFactory::getApplication();
				$newId = $this->id;

				/** @var ReddesignModelBackgrounds $modelBackgrounds */
				$modelBackgrounds = RModel::getAdminInstance('Backgrounds', array('ignore_request' => true));
				$modelBackgrounds->setState('designtype_id', $originalId);
				$backgrounds = $modelBackgrounds->getItems();

				/** @var ReddesignTableBackground $backgroundTable */
				$backgroundTable = RTable::getAdminInstance('Background');

				/** @var ReddesignTableArea $areaTable */
				$areaTable = RTable::getAdminInstance('Area');

				foreach ($backgrounds as $background)
				{
					$backgroundTable->load($background->id);
					$mangledName = ReddesignHelpersFile::getUniqueName($background->svg_file);

					$backgroundTable->svg_file = $mangledName . '.' . JFile::getExt($background->svg_file);

					// Copy same SVG
					if (JFile::exists(JPATH_SITE . '/media/com_reddesign/backgrounds/' . $background->svg_file))
					{
						JFile::copy(
							JPATH_SITE . '/media/com_reddesign/backgrounds/' . $background->svg_file,
							JPATH_SITE . '/media/com_reddesign/backgrounds/' . $backgroundTable->svg_file
						);
					}

					$backgroundTable->designtype_id = $newId;
					$backgroundTable->id = 0;

					$backgroundTable->store();

					/** @var ReddesignModelAreas $areasModel */
					$areasModel = RModel::getAdminInstance('Areas', array('ignore_request' => true));
					$areasModel->setState('background_id', $background->id);
					$areas = $areasModel->getItems();

					if (!empty($areas))
					{
						foreach ($areas as $area)
						{
							$areaTable->load($area->id);

							$areaTable->background_id = $backgroundTable->id;
							$areaTable->id = 0;

							$areaTable->store();
						}
					}
				}
			}

			return true;
		}

		return false;
	}
}
