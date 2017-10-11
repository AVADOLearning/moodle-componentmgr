<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

use ComponentManager\MoodleVersion;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \ComponentManager\MoodleVersion
 */
class MoodleVersionTest extends TestCase {
    public function testSatisfies() {
        $version = new MoodleVersion(
                '2017051502.06', '3.3.2+ (Build: 20171006)', '3.3', 200, '');
        $this->assertEquals(100, $version->satisfies('2017051502.06'));
        $this->assertEquals(100, $version->satisfies('3.3+'));
        $this->assertEquals(50, $version->satisfies('3.3'));
        $this->assertEquals(0, $version->satisfies('2017051502'));
    }
}
