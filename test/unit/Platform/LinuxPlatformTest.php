<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\Platform;

use ComponentManager\Platform\LinuxPlatform;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @coversDefaultClass \ComponentManager\Platform\LinuxPlatform
 * @group platform
 * @group platform-linux
 */
class LinuxPlatformTest extends TestCase {
    /**
     * @var string
     */
    protected $oldPath;

    /**
     * @var LinuxPlatform
     */
    protected $platform;

    /**
     * @inheritdoc TestCase
     */
    public function setUp() {
        $this->oldPath = getenv('PATH');
        putenv("PATH=/bin:{$this->oldPath}");

        $filesystem = $this->createMock(Filesystem::class);
        $this->platform = new LinuxPlatform($filesystem);
    }

    /**
     * @inheritdoc TestCase
     */
    public function tearDown() {
        putenv("PATH={$this->oldPath}");
    }

    /**
     * @covers ::expandPath
     * @covers \ComponentManager\Platform\AbstractPlatform::__construct
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\LinuxPlatform::getHomeDirectory
     */
    public function testExpandPath() {
        $expected = getenv('HOME') . '/test';
        $actual   = $this->platform->expandPath('~/test');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::__construct
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     */
    public function testGetExecutablePath() {
        $this->assertEquals(
                '/bin/sh', $this->platform->getExecutablePath('sh'));
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::__construct
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
     * @covers \ComponentManager\Platform\AbstractPlatform::__construct
     */
    public function testGetHomeDirectory() {
        $this->assertFileExists($this->platform->getHomeDirectory());
    }

    /**
     * @covers ::getHomeDirectory
     * @covers ::getLocalSharedDirectory
     * @covers \ComponentManager\Platform\AbstractPlatform::__construct
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     */
    public function testGetLocalSharedDirectory() {
        $this->assertFileExists($this->platform->getLocalSharedDirectory());
    }
}
