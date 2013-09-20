<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

/**
 * redDesign Integration Plugin.
 *
 * @package     RedDesign.Component
 * @subpackage  Plugin
 *
 * @since       1.0
 */
class PlgRedshop_ProductReddesign extends JPlugin
{
	private $plugin;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @access  public
	 *
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->plugin = JPluginHelper::getPlugin('redshop_product', 'reddesign');

		$this->loadLanguage();
	}

	/**
	 * Stops loading redSHOP's jQuery file if true is returned.
	 * This is used to prevent jQuery conflicts and multiple jQuery loads
	 * during integration.
	 *
	 * @param   object  $data  Product data object.
	 *
	 * @return bool If true than redSHOP won't load its jQuery file
	 */
	public function stopRedshopJQuery($data)
	{
		if ($data->product_type == 'redDESIGN')
		{
			return true;
		}

		return false;
	}

	/**
	 * This method loads redDESIGN frontend editor into redSHOP
	 * frontend product detail view.
	 *
	 * @param   string  &$template_desc  The string which contains all of the product view HTML.
	 * @param   object  $params          Menu item product view parameters.
	 * @param   object  $data            Product object.
	 *
	 * @return  void
	 */
	public function onAfterDisplayProduct(&$template_desc, $params, $data)
	{
		if ($data->product_type == 'redDESIGN')
		{
			// Get design type ID
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('reddesign_designtype_id'));
			$query->from($db->quoteName('#__reddesign_product_mapping'));
			$query->where($db->quoteName('product_id') . ' = ' . $data->product_id);
			$db->setQuery($query);
			$reddesignDesigntypeId = $db->loadResult();

			$inputvars = array(
				'id'	=> $reddesignDesigntypeId
			);
			$input = new FOFInput($inputvars);

			ob_start();
			FOFDispatcher::getTmpInstance('com_reddesign', 'designtype', array('input' => $input))->dispatch();
			$html = ob_get_contents();
			ob_end_clean();

			$template_desc .= $html;
		}
	}
}
