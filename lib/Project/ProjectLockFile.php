<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Project;
use ComponentManager\ResolvedComponentVersion;

/**
 * Project lock file.
 *
 * The lock file pins components at specific package versions to allow for
 * reproducible project builds.
 */
class ProjectLockFile extends JsonFile {
    /**
     * Resolved component versions.
     *
     * @var \ComponentManager\ResolvedComponentVersion []
     */
    protected $resolvedComponentVersions;

    /**
     * Add a resolved component version.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     *
     * @return void
     */
    public function addResolvedComponentVersion(ResolvedComponentVersion $resolvedComponentVersion) {
        $componentName = $resolvedComponentVersion->getComponent()->getName();
        $this->resolvedComponentVersions[$componentName] = $resolvedComponentVersion;
    }

    /**
     * Clear resolved component versions.
     *
     * @return void
     */
    public function clearResolvedComponentVersions() {
        $this->resolvedComponentVersions = [];
    }

    /**
     * @override \ComponentManager\Project\JsonFile
     */
    public function dump() {
        return (object) [
            'componentVersions' => $this->resolvedComponentVersions,
        ];
    }
}
