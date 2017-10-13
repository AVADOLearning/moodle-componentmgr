<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\RevParseCommand;
use PHPUnit\Framework\TestCase;

class RevParseCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new RevParseCommand('remote/some-reference');
        $this->assertEquals(
                ['rev-parse', 'remote/some-reference'],
                $command->getCommandLine());
    }
}
