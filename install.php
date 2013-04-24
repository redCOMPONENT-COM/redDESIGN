<?php
/**
 * @package     redDESIGN
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Component installer class
 *
 * @package     redDESIGN
 * @subpackage  Installer
 * @since       2.0
 *
 */
class Com_ReddesignInstallerScript
{
	var $status = null;

	var $installer = null;

	/**
	 * Method to install the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	function install($parent)
	{
		// Install extensions
		$this->installLibraries($parent);
		$this->installModules($parent);
		$this->installPlugins($parent);
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent)
	{

	}

	/**
	 * Method to update the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	function update($parent)
	{
		// Install extensions
		$this->installLibraries($parent);
		$this->installModules($parent);
		$this->installPlugins($parent);
	}

	/**
     * Method that runs right before the install/update/uninstall methods
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
     *
     * @return void
     */
	function preflight($type, $parent)
	{

	}

	/**
	 * Method that runs right after the install/update/uninstall methods
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		$fofInstallationStatus = $this->_installFOF($parent);
	}

	/**
	 * Install the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installLibraries($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->libraries->library)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
				$extPath = $src . '/libraries/' . $extName;
				$result  = 0;

				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}

				$this->_storeStatus('libraries', array('name' => $extName, 'result' => $result));
			}
		}
	}

	/**
	 * Install the package modules
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
				$result    = 0;

				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}

				$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Install the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
				$extPath  = $src . '/plugins/' . $extGroup . '/' . $extName;
				$result   = 0;

				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}

				// Store the result to show install summary later
				$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));

				// Enable the installed plugin
				if ($result)
				{
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->update($db->quoteName("#__extensions"));
					$query->set("enabled=1");
					$query->where("type='plugin'");
					$query->where("element=" . $db->Quote($extName));
					$query->where("folder=" . $db->Quote($extGroup));
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Get the common JInstaller instance used to install all the extensions
	 *
	 * @return JInstaller The JInstaller object
	 */
	function getInstaller()
	{
		if (is_null($this->installer))
		{
			$this->installer = new JInstaller;
		}
		return $this->installer;
	}

	/**
	 * Check if FoF is already installed otherwise installs it
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  array            Array with performed actions summary
	 */
	private function _installFOF($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Load dependencies
		JLoader::import('joomla.filesystem.file');
		JLoader::import('joomla.utilities.date');
		$source = $src . '/libraries/fof';

		if (!defined('JPATH_LIBRARIES'))
		{
			$target = JPATH_ROOT . '/libraries/fof';
		}
		else
		{
			$target = JPATH_LIBRARIES . '/fof';
		}

		// Default values
		$haveToInstallFOF = false;
		$installedFOF     = false;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select("extension_id")
			->from($db->quoteName("#__extensions"))
			->where("type='library'")
			->where("element=" . $db->Quote('lib_fof'));
		
		$db->setQuery($query);
		$db->query();

		try
		{
			$installedFOF = $db->loadResult();
		}
		catch (Exception $e)
		{
			throw new RuntimeException($e->getMessage());
		}

		if (!is_dir($target) || !$installedFOF)
		{
			$haveToInstallFOF = true;
		}
		else
		{
			$fofVersion = array();

			if (file_exists($target . '/version.txt'))
			{
				$rawData = JFile::read($target . '/version.txt');
				$info    = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			}
			else
			{
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}

			$rawData = JFile::read($source . '/version.txt');
			$info    = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
		}

		if ($haveToInstallFOF)
		{
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedFOF = $installer->install($source);
		}
		else
		{
			$versionSource = 'installed';
		}

		if (!isset($fofVersion))
		{
			$fofVersion = array();

			if (file_exists($target . '/version.txt'))
			{
				$rawData = JFile::read($target . '/version.txt');
				$info    = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			}
			else
			{
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}

			$rawData = JFile::read($source . '/version.txt');
			$info    = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if (!($fofVersion[$versionSource]['date'] instanceof JDate))
		{
			$fofVersion[$versionSource]['date'] = new JDate;
		}

		return array(
			'required'	=> $haveToInstallFOF,
			'installed'	=> $installedFOF,
			'version'	=> $fofVersion[$versionSource]['version'],
			'date'		=> $fofVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	/**
	 * Store the result of trying to install an extension
	 *
	 * @param   string  $type    Type of extension (libraries, modules, plugins)
	 * @param   array   $status  The status info
	 *
	 * @return void
	 */
	private function _storeStatus($type, $status)
	{
		// Initialise status object if needed
		if (is_null($this->status))
		{
			$this->status = new stdClass;
		}

		// Initialise current status type if needed
		if (!isset($this->status->{$type}))
		{
			$this->status->{$type} = array();
		}

		// Insert the status
		array_push($this->status->{$type}, $status);
	}
}
