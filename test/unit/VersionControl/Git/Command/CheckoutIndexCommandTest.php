<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\Command\CheckoutIndexCommand;
use PHPUnit\Framework\TestCase;

class CheckoutIndexCommandTest extends TestCase {
    public function testGetCommandLine() {
        $command = new CheckoutIndexCommand('/some/dir/');
        $this->assertEquals(
                ['checkout-index', '--all', '--prefix=/some/dir/'],
                $command->getCommandLine());
    }
}
