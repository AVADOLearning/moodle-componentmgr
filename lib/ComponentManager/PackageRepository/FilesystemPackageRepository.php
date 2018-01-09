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
use Psr\Log\LoggerInterface;

/**
 * Filesystem package repository.
 *
 * Allows sourcing components from a local filesystem.
 */
class FilesystemPackageRepository extends AbstractPackageRepository
        implements PackageRepository {
    /**
     * @inheritdoc PackageRepository
     */
    public function getId() {
        return 'Filesystem';
    }

    /**
     * @inheritdoc PackageRepository
     */
    public function getName() {
        return 'Filesystem package repository';
    }

    /**
     * @inheritdoc PackageRepository
     */
    public function resolveComponent(ComponentSpecification $componentSpecification,
                                     LoggerInterface $logger) {
        return new Component($componentSpecification->getName(), [
            new ComponentVersion(null, null, null, [
                new DirectoryComponentSource($componentSpecification->getExtra('directory')),
            ]),
        ], $this);
    }

    /**
     * @inheritdoc PackageRepository
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version) {
        return true;
    }
}
