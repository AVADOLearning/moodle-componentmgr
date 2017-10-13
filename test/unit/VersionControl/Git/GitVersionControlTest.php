<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git;

use ComponentManager\Exception\VersionControlException;
use ComponentManager\VersionControl\Git\Command\Command;
use ComponentManager\VersionControl\Git\GitVersionControl;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass \ComponentManager\VersionControl\GitVersionControl
 */
class GitVersionControlTest extends TestCase {
    public function testCreateProcess() {
        $repo = new GitVersionControl('git', getcwd());
        $process = $repo->createProcess(['help']);

        $this->assertEquals(getcwd(), $process->getWorkingDirectory());
        $this->assertEquals('\'git\' \'help\'', $process->getCommandLine());
    }

    public function testRunCommand() {
        $repo = new GitVersionControl('git', getcwd());
        $command = $this->createMock(Command::class);
        $command->method('getCommandLine')
            ->willReturn(['help']);
        $process = $repo->runCommand($command);

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals(0, $process->getExitCode());
    }

    public function testRunCommandThrows() {
        $repo = new GitVersionControl('git', 'getcwd');
        $command = $this->createMock(Command::class);
        $command->method('getCommandLine')
            ->willReturn(['completely-invalid-command']);

        $this->expectException(VersionControlException::class);
        $this->expectExceptionCode(999);

        $repo->runCommand($command, 999);
    }
}
