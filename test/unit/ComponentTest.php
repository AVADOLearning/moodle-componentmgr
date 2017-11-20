<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Test;

use ComponentManager\Component;
use ComponentManager\ComponentVersion;
use ComponentManager\Exception\UnsatisfiedVersionException;
use ComponentManager\PackageRepository\PackageRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \ComponentManager\Component
 */
class ComponentTest extends TestCase {
    public function testGetNameParts() {
        $component = new Component('type_name', []);
        $this->assertEquals(['type', 'name'], $component->getNameParts());
        $this->assertEquals('type', $component->getPluginType());
        $this->assertEquals('name', $component->getPluginName());
    }

    public function testGetVersion() {
        $goodPackageRepository = $this->createMock(PackageRepository::class);
        $goodPackageRepository->method('satisfiesVersion')
            ->willReturn(true);

        $badPackageRepository = $this->createMock(PackageRepository::class);
        $badPackageRepository->method('satisfiesVersion')
            ->willReturn(false);

        $componentVersion = new ComponentVersion(
            '2015021800', 'Genesis', ComponentVersion::MATURITY_STABLE, []);

        $component = new Component(
            'type_name', [$componentVersion], $goodPackageRepository);
        $this->assertEquals(
            $componentVersion, $component->getVersion('2015021800'));

        $this->expectException(UnsatisfiedVersionException::class);
        $this->expectExceptionCode(UnsatisfiedVersionException::CODE_UNKNOWN_VERSION);
        $component = new Component(
            'type_name', [$componentVersion], $badPackageRepository);
        $component->getVersion('2015021800');
    }
}
