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
use Webmozart\PathUtil\Path;
use Symfony\Component\Filesystem\Filesystem;
use OxidEsales\Facts\Config\ConfigFile;

class ConfigFileTest extends \PHPUnit_Framework_TestCase
{
    private $temporaryPath;
    private $vendorPath;
    private $targetPath;

    public function setUp()
    {
        $this->temporaryPath = Path::join(__DIR__, 'tmp');
        $this->vendorPath = Path::join(__DIR__, 'tmp', 'testData');
        $this->targetPath = Path::join(__DIR__, 'tmp', 'testTarget');
        $this->buildDirectory();
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->temporaryPath);
    }

    public function testIncludeAndParseConfigFile()
    {
        $configFile = new ConfigFile(Path::join($this->vendorPath, 'config.inc.php'));

        $this->assertSame('test', $configFile->getVar('dbName'));
    }

    private function buildDirectory()
    {
        $structure = [
            'config.inc.php' => '<?php $this->dbName = "test";'
        ];

        vfsStream::setup('root', null, $structure);
        $pathBlueprint = vfsStream::url('root');

        $filesystem = new Filesystem();

        $filesystem->remove($this->vendorPath);
        $filesystem->mirror($pathBlueprint, $this->vendorPath);
    }
}
