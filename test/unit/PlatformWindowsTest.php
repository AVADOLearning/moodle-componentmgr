<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

use ComponentManager\Platform\WindowsPlatform;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \ComponentManager\Platform\WindowsPlatform
 * @group platform
 * @group platform-windows
 */
class PlatformWindowsTest extends TestCase {
    /**
     * @var string
     */
    protected $oldPath;

    /**
     * @var string
     */
    protected $oldWorkingDirectory;

    /**
     * @var \ComponentManager\Platform\WindowsPlatform
     */
    protected $platform;

    public function setUp() {
        $this->oldPath = getenv('PATH');
        putenv("PATH=C:\\windows\\system32;{$this->oldPath}");

        $this->oldWorkingDirectory = getcwd();

        $this->platform = new WindowsPlatform();
    }

    public function tearDown() {
        putenv("PATH={$this->oldPath}");

        chdir($this->oldWorkingDirectory);
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::createTempDirectory
     */
    public function testCreateTempDirectory() {
        $temp = $this->platform->createTempDirectory();

        $this->assertFileExists($temp);

        rmdir($temp);
    }

    /**
     * @covers ::expandPath
     */
    public function testExpandPath() {
        $this->assertEquals('~\blah', $this->platform->expandPath('~\blah'));
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     */
    public function testGetDirectorySeparator() {
        $this->assertEquals('\\', $this->platform->getDirectorySeparator());
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     */
    public function testGetExecutablePath() {
        $this->assertEquals(
                'c:\\windows\\system32\\cmd.exe',
                strtolower($this->platform->getExecutablePath('cmd')));
    }

    /**
     * @covers ::getExecutablePath
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     *
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
        $home = $this->platform->getHomeDirectory();
        $user = getenv('USERNAME');

        $this->assertStringEndsWith($user, $home);
        $this->assertFileExists($home);
    }

    /**
     * @covers ::getLocalSharedDirectory
     */
    public function testGetLocalSharedDirectory() {
        $this->assertFileExists($this->platform->getLocalSharedDirectory());
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::getPhpExecutable
     */
    public function testGetPhpExecutable() {
        $this->assertFileExists($this->platform->getPhpExecutable());
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::getPhpScript
     */
    public function testGetPhpScript() {
        $this->assertFileExists($this->platform->getPhpScript());
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::getWorkingDirectory
     */
    public function testGetWorkingDirectory() {
        $target = 'c:\windows';

        $this->assertEquals(getcwd(), $this->platform->getWorkingDirectory());

        chdir($target);
        $this->assertEquals($target, $this->platform->getWorkingDirectory());
    }

    /**
     * @covers \ComponentManager\Platform\AbstractPlatform::joinPaths
     * @covers \ComponentManager\Platform\AbstractPlatform::getDirectorySeparator
     */
    public function testJoinPaths() {
        $this->assertEquals(
                'c:\\windows', $this->platform->joinPaths(['c:', 'windows']));

        $this->assertEquals(
                'c:\\windows\\system32',
                $this->platform->joinPaths(['c:', 'windows\\system32']));

        $this->assertEquals(
                'c:\\windows\\system32',
                $this->platform->joinPaths(['c:\\windows', 'system32']));
    }
}
