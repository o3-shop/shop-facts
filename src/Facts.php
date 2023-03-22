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

namespace OxidEsales\Facts;

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Edition\EditionSelector;
use Webmozart\PathUtil\Path;

/**
 * Class responsible to return information about O3-Shop.
 * Could be used without shop bootstrap
 * for example before setup of a shop.
 */
class Facts
{
    /**
     * @var string The composer vendor name of O3-Shop.
     */
    const COMPOSER_VENDOR_OXID_ESALES = 'o3-shop';

    /**
     * @var string The composer package name of the O3-Shop Community Edition.
     */
    const COMPOSER_PACKAGE_OXIDESHOP_CE = 'shop-ce';

    protected $startPath;

    /**
     * @var null | ConfigFile
     */
    protected $configReader = null;

    /**
     * Facts constructor.
     *
     * @param string $startPath               Start path.
     * @param null   $configFile              Optional ConfigFile
     */
    public function __construct($startPath = __DIR__, $configFile = null)
    {
        $this->startPath = $startPath;
        $this->configReader = $configFile;
    }

    /**
     * @return string Root path of shop.
     */
    public function getShopRootPath()
    {
        $vendorPaths = [
            '/vendor',
            '/../vendor',
            '/../../vendor',
            '/../../../vendor',
            '/../../../../vendor',
        ];

        $rootPath = '';
        foreach ($vendorPaths as $vendorPath) {
            if (file_exists(Path::join($this->startPath, $vendorPath))) {
                $rootPath = Path::join($this->startPath, $vendorPath, '..');
                break;
            }
        }

        return $rootPath;
    }

    /**
     * @return string Path to vendor directory.
     */
    public function getVendorPath()
    {
        return Path::join($this->getShopRootPath(), 'vendor');
    }

    /**
     * @return string Path to source directory.
     */
    public function getSourcePath()
    {
        return Path::join($this->getShopRootPath(), 'source');
    }

    /**
     * @return string Path to source directory.
     */
    public function getCommunityEditionSourcePath()
    {
        $vendorPath = $this->getVendorPath();

        if ($this->isProjectEshopInstallation()) {
            $communityEditionSourcePath = Path::join($vendorPath, self::COMPOSER_VENDOR_OXID_ESALES, self::COMPOSER_PACKAGE_OXIDESHOP_CE, 'source');
        } else {
            $communityEditionSourcePath = $this->getSourcePath();
        }

        return $communityEditionSourcePath;
    }

    /**
     * @return string
     */
    public function getCommunityEditionRootPath()
    {
        $communityEditionRootPath = $this->getShopRootPath();

        if ($this->isProjectEshopInstallation()) {
            $communityEditionRootPath = Path::join($this->getVendorPath(), self::COMPOSER_VENDOR_OXID_ESALES, self::COMPOSER_PACKAGE_OXIDESHOP_CE);
        }

        return $communityEditionRootPath;
    }

    /**
     * @return string Path to ``out`` directory.
     */
    public function getOutPath()
    {
        return Path::join($this->getSourcePath(), 'out');
    }

    /**
     * @throws \Exception
     *
     * @return string Eshop edition as capital two letters code.
     */
    public function getEdition()
    {
        $editionSelector = new EditionSelector();
        $edition = $editionSelector->getEdition();

        return $edition;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        $editionSelector = new EditionSelector();

        return $editionSelector->isCommunity();
    }

    /**
     * @return mixed
     */
    public function getDatabaseName()
    {
        return $this->getConfigReader()->dbName;
    }

    /**
     * @return mixed
     */
    public function getDatabaseUserName()
    {
        return $this->getConfigReader()->dbUser;
    }

    /**
     * @return mixed
     */
    public function getDatabasePassword()
    {
        return $this->getConfigReader()->dbPwd;
    }

    /**
     * @return mixed
     */
    public function getDatabaseHost()
    {
        return $this->getConfigReader()->dbHost;
    }

    /**
     * @return mixed
     */
    public function getDatabasePort()
    {
        return $this->getConfigReader()->dbPort;
    }

    /**
     * @return mixed
     */
    public function getDatabaseDriver()
    {
        return $this->getConfigReader()->dbType;
    }

    /**
     * @return string
     */
    public function getShopUrl()
    {
        return $this->getConfigReader()->sShopURL;
    }

    /**
     * @return array
     *
     * @deprecated this method will be remove in next major version and it will moved to doctrine-migration-wrapper component
     */
    public function getMigrationPaths()
    {
        $editionSelector = new EditionSelector();

        $migrationPaths = [
            'ce' => $this->getConfigReader()->getVar(ConfigFile::PARAMETER_SOURCE_PATH).'/migration/migrations.yml',
        ];

        $migrationPaths['pr'] = $this->getConfigReader()->getVar(ConfigFile::PARAMETER_SOURCE_PATH)
                                . '/migration/project_migrations.yml';

        return $migrationPaths;
    }

    /**
     * Safeguard for ConfigFile object.
     *
     * @return ConfigFile
     */
    protected function getConfigReader()
    {
        if (is_null($this->configReader)) {
            $this->configReader = new ConfigFile();
        }
        return $this->configReader;
    }

    /**
     * Determine, if the given O3-Shop is a project installation.
     *
     * @return bool Is the given O3-Shop installation a poject installation?
     */
    private function isProjectEshopInstallation()
    {
        $vendorCommunityEditionPath = Path::join($this->getVendorPath(), self::COMPOSER_VENDOR_OXID_ESALES, self::COMPOSER_PACKAGE_OXIDESHOP_CE);

        return is_dir($vendorCommunityEditionPath);
    }
}
