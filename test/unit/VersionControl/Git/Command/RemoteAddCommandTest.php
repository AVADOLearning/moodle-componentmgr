<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\RemoteAddCommand;
use ComponentManager\VersionControl\Git\GitRemote;
use PHPUnit\Framework\TestCase;

class RemoteAddCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new RemoteAddCommand(new GitRemote(
                'origin',
                'git@github.com:LukeCarrier/moodle-componentmgr.git'));
        $expect = [
            'remote',
            'add',
            'origin',
            'git@github.com:LukeCarrier/moodle-componentmgr.git',
        ];
        $this->assertEquals($expect, $command->getCommandLine());
    }
}
