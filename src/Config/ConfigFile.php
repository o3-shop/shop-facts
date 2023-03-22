<?php
/**
 * This file is part of O3-Shop Facts.
 *
 * O3-Shop is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

namespace OxidEsales\Facts\Config;

/**
 * Wraps and provides getters for configuration constants stored in configuration file (usually config.inc.php).
 */
#[AllowDynamicProperties]
class ConfigFile
{
    const PARAMETER_VENDOR_PATH = 'vendor_path';

    const PARAMETER_SOURCE_PATH = 'source_path';

    const ERROR_CODE_CONFIGFILE_PATH_EMPTY = 1;
    const ERROR_CODE_CONFIGFILE_NOT_FOUND = 2;

    /**
     * Initializes the instance. Loads config variables from the file.
     *
     * @param string $pathToConfigIncFile Configuration file name.
     *
     * @throws \Exception if config.inc.php does not exist.
     */
    public function __construct($pathToConfigIncFile = null)
    {
        if (is_file($pathToConfigIncFile)) {
            $this->loadVars($pathToConfigIncFile);
            return;
        }
        $pathToConfigIncFile = '';
        $rootPath = '';
        $pathToAutoloadFromThisScript = '/';
        $autoloaderName = 'vendor/autoload.php';
        $count = 0;
        while ($count < 8) {
            $fullPathToAutoload = __DIR__ . $pathToAutoloadFromThisScript;
            if (is_file($fullPathToAutoload . $autoloaderName)) {
                $rootPath = $fullPathToAutoload;
                $pathToConfigIncFile = $rootPath.'source/config.inc.php';
                break;
            }
            $pathToAutoloadFromThisScript = '/..' . $pathToAutoloadFromThisScript;
            $count++;
        }
        if (empty($pathToConfigIncFile)) {
            throw new \Exception('One of the files vendor/autoload.php or source/config.inc.php was not found!', static::ERROR_CODE_CONFIGFILE_PATH_EMPTY);
        }
        if (!file_exists($pathToConfigIncFile)) {
            throw new \Exception('File source/config.inc.php was not found!', static::ERROR_CODE_CONFIGFILE_NOT_FOUND);
        }

        $this->setVar(static::PARAMETER_VENDOR_PATH, $rootPath.'vendor');
        $this->setVar(static::PARAMETER_SOURCE_PATH, $rootPath.'source');
        $this->loadVars($pathToConfigIncFile);
    }

    /**
     * Returns loaded variable value by name.
     *
     * @param string $varName Variable name
     *
     * @return mixed
     */
    public function getVar($varName)
    {
        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     * Set config variable.
     *
     * @param string $varName Variable name
     * @param string $value   Variable value
     */
    public function setVar($varName, $value)
    {
        $this->$varName = $value;
    }

    /**
     * Checks by name if variable is set
     *
     * @param string $varName Variable name
     *
     * @return bool
     */
    public function isVarSet($varName)
    {
        return isset($this->$varName);
    }

    /**
     * Returns all loaded vars as an array
     *
     * @return array[string]mixed
     */
    public function getVars()
    {
        return get_object_vars($this);
    }

    /**
     * Performs variable loading from configuration file by including the php file.
     * It works with current configuration file format well,
     * however in case the variable storage format is not satisfactory
     * this method is a subject to be changed.
     *
     * @param string $fileName Configuration file name
     */
    protected function loadVars($fileName)
    {
        include $fileName;
    }
}
