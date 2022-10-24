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

namespace OxidEsales\Facts\Edition;

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;

/**
 * Class is responsible for returning edition of O3-Shop.
 */
class EditionSelector
{
    const COMMUNITY = 'CE';

    /** @var string Edition abbreviation */
    private $edition = null;

    /** @var ConfigFile */
    private $configFile = null;

    /**
     * EditionSelector constructor.
     * Adds possibility to inject ConfigFile to force different settings.
     *
     * @param null|ConfigFile $configFile
     */
    public function __construct($configFile = null)
    {
        $this->configFile = $configFile;

        $this->edition = $this->findEdition();
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() === static::COMMUNITY;
    }

    /**
     * Check for forced edition in config file. If edition is not specified,
     * determine it by ClassMap existence.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function findEdition()
    {
        try {
            $edition = $this->findEditionByConfigFile();
            if (empty($edition)) {
                $edition = $this->findEditionByEditionFiles();
            }
        } catch (\Exception $exception) {
            try {
                $edition = $this->findEditionByEditionFiles();
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        return strtoupper($edition);
    }


    /**
     * Find edition by directories of the editions in the vendor directory
     *
     * @return string
     *
     * @throws \Exception
     */
    private function findEditionByEditionFiles()
    {
        $facts = $this->getFacts();
        $edition = '';
        if (is_dir($facts->getCommunityEditionSourcePath()) === true) {
            $edition = static::COMMUNITY;
        }

        if ($edition === '') {
            throw new \Exception("Shop directory structure is not setup properly. Edition could not be detected");
        }

        return $edition;
    }

    /**
     * @return Facts
     */
    private function getFacts()
    {
        return new Facts();
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function findEditionByConfigFile()
    {
        $configFile = $this->getConfigFile();
        $edition = $configFile->getVar('edition');

        return $edition;
    }

    /**
     * Safeguard for ConfigFile object.
     *
     * @return null|ConfigFile
     */
    protected function getConfigFile()
    {
        if (is_null($this->configFile)) {
            $this->configFile = new ConfigFile();
        }

        return $this->configFile;
    }
}
