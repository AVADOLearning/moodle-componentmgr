<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

use ComponentManager\ComponentSpecification;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \ComponentManager\ComponentSpecification
 */
class ComponentSpecificationTest extends TestCase {
    public function testGetExtra() {
        $extra = (object) [
            'defined' => 'obviously',
        ];
        $componentSpecification = new ComponentSpecification(
                'type_name', '2015021800', null, null, $extra);
        $this->assertEquals(
                'obviously', $componentSpecification->getExtra('defined'));

        $this->expectException(OutOfBoundsException::class);
        $componentSpecification->getExtra('definitelyUndefined');
    }
}
