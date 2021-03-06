<?php
/**
 * @package     Reddesign.Libraries
 * @subpackage  Entity
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Represents the config.
 *
 * @package     Reddesign.Libraries
 * @subpackage  Entity
 * @since       1.0
 */
final class ReddesignEntityConfig
{
	/**
	 * A ReddesignEntityConfig instance.
	 *
	 * @var  ReddesignEntityConfig
	 */
	private static $instance = null;

	/**
	 * The component config.
	 *
	 * @var  JRegistry
	 */
	private $config;

	/**
	 * Singleton.
	 */
	private function __construct()
	{
		// Get the config data from the model.
		require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/models/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_reddesign/tables/config.php';
		$model = JModelLegacy::getInstance('Config', 'ReddesignModel');
		$this->config = $model->getItem();
	}

	/**
	 * Get an instance or create it.
	 *
	 * @return  ReddesignEntityConfig
	 */
	public static function getInstance()
	{
		if (!static::$instance)
		{
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Get a config value.
	 *
	 * @param   string  $key      The config key.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The config value or default if not found.
	 */
	public function get($key, $default = null)
	{
		if (!empty($this->config[$key]))
		{
			return $this->config[$key];
		}

		return $default;
	}

	/**
	 * Get a boolean config value.
	 *
	 * @param   string  $key      The config key.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The config value or default if not found.
	 */
	public function getBool($key, $default = null)
	{
		if (!empty($this->config[$key]))
		{
			return (bool) $this->config[$key];
		}

		return $default;
	}

	/**
	 * Get an integer config value.
	 *
	 * @param   string  $key      The config key.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The config value or default if not found.
	 */
	public function getInt($key, $default = null)
	{
		if (!empty($this->config[$key]))
		{
			return (int) $this->config[$key];
		}

		return $default;
	}

	/**
	 * Get a string config value.
	 *
	 * @param   string  $key      The config key.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The config value or default if not found.
	 */
	public function getString($key, $default = null)
	{
		if (!empty($this->config[$key]))
		{
			return (string) $this->config[$key];
		}

		return $default;
	}

	/**
	 * Check if a config name exists.
	 *
	 * @param   string  $key  The config key.
	 *
	 * @return  boolean  True if it exsists, false otherwise.
	 */
	public function exists($key)
	{
		return !empty($this->config[$key]);
	}

	/**
	 * Get preserveDataBetweenDesignTypes config.
	 *
	 * @return  int  The padding of production file.
	 */
	public function getPreserveDataBetweenDesignTypes()
	{
		return $this->getBool('preserveDataBetweenDesignTypes');
	}

	/**
	 * Get showAssignFontsToAllAreas config.
	 *
	 * @return  int  The padding of production file.
	 */
	public function getShowAssignFontsToAllAreas()
	{
		return $this->getBool('showAssignFontsToAllAreas');
	}

	/**
	 * Get the unit.
	 *
	 * @return  string  The unit
	 */
	public function getUnit()
	{
		return $this->getString('unit', 'px');
	}

	/**
	 * Get the font unit.
	 *
	 * @return  string  The font unit
	 */
	public function getFontUnit()
	{
		return $this->getString('fontUnit', 'px');
	}

	/**
	 * Get DPI resolution of source SVG files.
	 *
	 * @return  int  SVG DPI.
	 */
	public function getSourceDpi()
	{
		return $this->getInt('source_dpi');
	}

	/**
	 * Get the Font Preview Text
	 *
	 * @return  string  The text for font preview
	 */
	public function getFontPreviewText()
	{
		return $this->getString('font_preview_text');
	}

	/**
	 * Get the max SVG file size.
	 *
	 * @return  int  The max SVG file size.
	 */
	public function getCartThumbnailsSource()
	{
		return $this->getBool('cartThumbnailsSource');
	}

	/**
	 * Get the max SVG file size.
	 *
	 * @return  int  The max SVG file size.
	 */
	public function getMaxSVGFileSize()
	{
		return $this->getInt('max_svg_file_size');
	}

	/**
	 * Get the max width of SVG preview file in backend.
	 *
	 * @return  int  The max width of SVG preview file in backend.
	 */
	public function getMaxSVGPreviewAdminWidth()
	{
		return $this->getInt('max_svg_backend_bg_width');
	}

	/**
	 * Get the max width of SVG preview file in frontend.
	 *
	 * @return  int  The max width of SVG preview file in frontend.
	 */
	public function getMaxSVGPreviewSiteWidth()
	{
		return $this->getInt('max_svg_frontend_bg_width');
	}

	/**
	 * Get background thumbnail width.
	 *
	 * @return  int  Background thumbnail width
	 */
	public function getBgThumbnailWidth()
	{
		return $this->getInt('background_thumbnail_width');
	}

	/**
	 * Get background thumbnail height.
	 *
	 * @return  int  Background thumbnail height
	 */
	public function getBgThumbnailHeight()
	{
		return $this->getInt('background_thumbnail_height');
	}

	/**
	 * Get the max width of Clipart preview.
	 *
	 * @return  int  The max width of Clipart preview.
	 */
	public function getMaxClipartPreviewWidth()
	{
		return $this->getInt('max_clipart_preview_width', 80);
	}

	/**
	 * Get the max height of Clipart preview.
	 *
	 * @return  int  The max height of Clipart preview.
	 */
	public function getMaxClipartPreviewHeight()
	{
		return $this->getInt('max_clipart_preview_height', 80);
	}

	/**
	 * Get DPI resolution of source SVG files.
	 *
	 * @return  int  SVG DPI.
	 */
	public function getMinimumUploadDpi()
	{
		return $this->getInt('minimum_upload_dpi');
	}

	/**
	 * Get the padding of production file.
	 *
	 * @return  int  The padding of production file.
	 */
	public function getPaddingProductionFile()
	{
		return $this->getInt('productionFilePadding');
	}
}
