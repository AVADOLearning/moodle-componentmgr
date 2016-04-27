<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\Component;
use ComponentManager\ComponentSource\DirectoryComponentSource;
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;

/**
 * Filesystem package repository.
 *
 * Allows sourcing components from a local filesystem.
 */
class FilesystemPackageRepository extends AbstractPackageRepository
        implements PackageRepository {
    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getId() {
        return 'Filesystem';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getName() {
        return 'Filesystem package repository';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getComponent(ComponentSpecification $componentSpecification) {
        return new Component($componentSpecification->getName(), [
            new ComponentVersion(null, null, null, [
                new DirectoryComponentSource($componentSpecification->getExtra('directory')),
            ]),
        ], $this);
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version) {
        return true;
    }
}
