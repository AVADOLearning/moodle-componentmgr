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
use ComponentManager\ComponentSource\GitComponentSource;
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use Psr\Log\LoggerInterface;

/**
 * Git package repository.
 *
 * Allows sourcing components from arbitrary Git repositories.
 */
class GitPackageRepository extends AbstractCachingPackageRepository
        implements PackageRepository {
    /**
     * @inheritdoc PackageRepository
     */
    public function getId() {
        return 'Git';
    }

    /**
     * @inheritdoc PackageRepository
     */
    public function getName() {
        return 'Git package repository';
    }

    /**
     * @inheritdoc PackageRepository
     */
    public function resolveComponent(ComponentSpecification $componentSpecification,
                                     LoggerInterface $logger) {
        return new Component($componentSpecification->getName(), [
            new ComponentVersion(null, null, null, [
                new GitComponentSource($componentSpecification->getExtra('uri'), $componentSpecification->getVersion())
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
