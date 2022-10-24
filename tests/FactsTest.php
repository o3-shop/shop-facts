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

namespace OxidEsales\Facts\Tests\Unit;

use org\bovigo\vfs\vfsStream;
use OxidEsales\Facts\Facts;

class FactsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShopRootPath()
    {
        $facts = $this->buildFacts();

        $expectedRoot = vfsStream::url('root/o3shop_ce');
        $this->assertSame($expectedRoot, $facts->getShopRootPath());
    }

    public function testGetVendorPath()
    {
        $facts = $this->buildFacts();

        $expectedVendor = vfsStream::url('root/o3shop_ce/vendor');
        $this->assertEquals($expectedVendor, $facts->getVendorPath());
    }

    public function testGetSourcePath()
    {
        $facts = $this->buildFacts();

        $expectedSource = $this->getShopSourcePath();
        $this->assertEquals($expectedSource, $facts->getSourcePath());
    }

    public function testGetCommunityEditionSourcePathNormalInstallation()
    {
        $facts = $this->buildFacts();

        $this->assertEquals($this->getShopSourcePath(), $facts->getCommunityEditionSourcePath());
    }

    public function testGetCommunityEditionSourcePathProjectInstallation()
    {
        $facts = $this->buildFacts(true);

        $this->assertEquals($this->getProjectShopSourcePath(), $facts->getCommunityEditionSourcePath());
    }

    private function buildFacts($isProjectInstallation = false)
    {

        $vendorOxidesaleDirectory = [
            'oxideshop-facts' => [
                'bin' => [],
                'src' => []
            ]
        ];
        if ($isProjectInstallation) {
            $vendorOxidesaleDirectory['oxideshop-ce'] = [];
        }

        $structure = [
            'o3shop_ce' => [
                'source' => [
                    'Core' => [],
                    'Application' => []
                ],
                'vendor' => [
                    'bin' => [],
                    'oxid-esales' => $vendorOxidesaleDirectory
                ]
            ],
            'vendor' => []
        ];

        vfsStream::setup('root', null, $structure);
        $root = vfsStream::url('root');

        $__DIR__stub = $root . '/o3shop_ce/vendor/oxid-esales/oxideshop-facts/src';

        $configFile = $this->getMock('ConfigFile');

        $facts = new Facts($__DIR__stub, $configFile);

        return $facts;
    }

    /**
     * Get the path to the O3-Shop source directory.
     *
     * @return string The path to the O3-Shop source directory.
     */
    private function getShopSourcePath()
    {
        return vfsStream::url('root/o3shop_ce/source');
    }

    /**
     * Get the path to the O3-Shop source directory.
     *
     * @return string The path to the O3-Shop source directory.
     */
    private function getProjectShopSourcePath()
    {
        return vfsStream::url('root/o3shop_ce/vendor/oxid-esales/oxideshop-ce/source');
    }
}
