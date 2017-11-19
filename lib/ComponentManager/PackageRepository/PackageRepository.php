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
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\HttpClient;
use ComponentManager\Platform\Platform;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package repository interface.
 *
 * Package repositories contain metadata about the different available
 * components, including their names, versions and descriptions.
 */
interface PackageRepository {
    /**
     * Initialiser.
     *
     * @param Filesystem $filesystem
     * @param HttpClient $httpClient
     * @param Platform   $platform
     * @param stdClass   $options
     */
    public function __construct(Filesystem $filesystem, HttpClient $httpClient,
                                Platform $platform, stdClass $options);

    /**
     * Get repository identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get repository name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get available versions for the specified component.
     *
     * @param ComponentSpecification $componentSpecification
     *
     * @return Component
     *
     * @throws InvalidProjectException
     */
    public function getComponent(ComponentSpecification $componentSpecification);

    /**
     * Determine whether the version specification is satisfied by the given version.
     *
     * @param string           $versionSpecification
     * @param ComponentVersion $version
     *
     * @return boolean
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version);
}
