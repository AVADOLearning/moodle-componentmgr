<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\InitCommand;
use PHPUnit\Framework\TestCase;

class InitCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new InitCommand();
        $this->assertEquals(['init'], $command->getCommandLine());
    }
}
