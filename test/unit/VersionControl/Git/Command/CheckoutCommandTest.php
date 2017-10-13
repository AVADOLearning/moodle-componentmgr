<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\CheckoutCommand;
use PHPUnit\Framework\TestCase;

class CheckoutCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new CheckoutCommand('some-branch');
        $this->assertEquals(
            ['checkout', 'some-branch'],
            $command->getCommandLine());
    }
}
