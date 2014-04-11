<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to upload one or multiple files.
 *
 * @package     RedshopB
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldFileUploader extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'FileUploader';

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'fields.fileuploader';

	/**
	 * A static cache.
	 *
	 * @var  array
	 */
	protected $cache = array();

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$layout = !empty($this->element['layout']) ? $this->element['layout'] : $this->layout;

		return RLayoutHelper::render(
			$layout,
			array(
				'id'       => $this->id,
				'element'  => $this->element,
				'field'    => $this,
				'name'     => $this->name,
				'required' => $this->required,
				'value'    => $this->value
			)
		);
	}
}
