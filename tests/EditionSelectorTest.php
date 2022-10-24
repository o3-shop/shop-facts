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

use OxidEsales\Facts\Edition\EditionSelector;

class EditionSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsEditionFromConfig()
    {
        $config = $this->getConfigStub('CE');

        $editionSelector = new EditionSelector($config);

        $this->assertSame('CE', $editionSelector->getEdition());
    }

    public function providerGetCommunityEdition()
    {
        return [
            ['CE'],
            ['ce'],
            ['cE'],
            ['Ce'],
        ];
    }

    /**
     * Test that returns edition independent from camel case.
     *
     * @param string $edition
     *
     * @dataProvider providerGetCommunityEdition
     */
    public function testForcingEditionIsCaseInsensitive($edition)
    {
        $config = $this->getConfigStub($edition);

        $editionSelector = new EditionSelector($config);

        $this->assertSame('CE', $editionSelector->getEdition());
        $this->assertTrue($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());
        $this->assertFalse($editionSelector->isEnterprise());
    }

    /**
     * Test that returns community edition independent from camel case.
     *
     * @param string $edition
     *
     * @dataProvider providerGetCommunityEdition
     */
    public function testGetCommunityEdition($edition)
    {
        $config = $this->getConfigStub($edition);

        $editionSelector = new EditionSelector($config);

        $this->assertTrue($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());
        $this->assertFalse($editionSelector->isEnterprise());
    }

    public function providerGetProfessionalEdition()
    {
        return [
            ['PE'],
            ['pe'],
            ['pE'],
            ['Pe'],
        ];
    }

    /**
     * Test that returns professional edition independent from camel case.
     *
     * @param string $edition
     *
     * @dataProvider providerGetProfessionalEdition
     */
    public function testGetProfessionalEdition($edition)
    {
        $config = $this->getConfigStub($edition);

        $editionSelector = new EditionSelector($config);

        $this->assertFalse($editionSelector->isCommunity());
        $this->assertTrue($editionSelector->isProfessional());
        $this->assertFalse($editionSelector->isEnterprise());
    }

    public function providerGetEnterpriseEdition()
    {
        return [
            ['EE'],
            ['Ee'],
            ['eE'],
            ['Ee'],
        ];
    }

    /**
     * Test that returns professional edition independent from camel case.
     *
     * @param string $edition
     *
     * @dataProvider providerGetEnterpriseEdition
     */
    public function testGetEnterpriseEdition($edition)
    {
        $config = $this->getConfigStub($edition);

        $editionSelector = new EditionSelector($config);

        $this->assertFalse($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());
        $this->assertTrue($editionSelector->isEnterprise());
    }

    /**
     * Creates a stub for config file.
     * Allows to force an edition.
     *
     * @param string $edition Edition name to return, etc. 'CE'
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigStub($edition)
    {
        $config = $this->getMock('ConfigFile', ['getVar']);
        $config->method('getVar')->will($this->returnValue($edition));

        return $config;
    }
}
