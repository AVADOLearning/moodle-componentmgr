<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

use ComponentManager\Platform\LinuxPlatform;

/**
 * @coversDefaultClass \ComponentManager\Platform\LinuxPlatform
 * @group platform
 * @group platform-linux
 */
class PlatformLinuxTest extends PHPUnit_Framework_TestCase {
    /**
     * @var string
     */
    protected $oldPath;

    /**
     * @var \ComponentManager\Platform\LinuxPlatform
     */
    protected $platform;

    public function setUp() {
        $this->oldPath = getenv('PATH');
        putenv("PATH=/bin:{$this->oldPath}");

        $this->platform = new LinuxPlatform();
    }

    public function tearDown() {
        putenv("PATH={$this->oldPath}");
    }

    /**
     * @covers ::expandPath
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     */
    public function testExpandPath() {
        $expected = getenv('HOME') . '/test';
        $actual   = $this->platform->expandPath('~/test');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     */
    public function testGetExecutablePath() {
        $this->assertEquals(
                '/bin/sh', $this->platform->getExecutablePath('sh'));
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     * @expectedException \ComponentManager\Exception\PlatformException
     * @expectedExceptionCode 2
     */
    public function testGetExecutablePathThrows() {
        $this->platform->getExecutablePath('likelynotathing');
    }

    /**
     * @covers ::getHomeDirectory
     */
    public function testGetHomeDirectory() {
        $this->assertFileExists($this->platform->getHomeDirectory());
    }

    /**
     * @covers ::getLocalSharedDirectory
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     */
    public function testGetLocalSharedDirectory() {
        $this->assertFileExists($this->platform->getLocalSharedDirectory());
    }
}
