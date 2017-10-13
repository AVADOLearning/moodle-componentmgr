<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\FetchCommand;
use PHPUnit\Framework\TestCase;

class FetchCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new FetchCommand();
        $this->assertEquals(['fetch'], $command->getCommandLine());

        $command = new FetchCommand('upstream');
        $this->assertEquals(['fetch', 'upstream'], $command->getCommandLine());
    }

    public function testGetCommandLineWithTags() {
        $command = new FetchCommand();
        $command->setTags(true);
        $this->assertEquals(['fetch', '--tags'], $command->getCommandLine());

        $command = new FetchCommand('upstream');
        $command->setTags(false);
        $this->assertEquals(
                ['fetch', '--no-tags', 'upstream'],
                $command->getCommandLine());
    }
}
