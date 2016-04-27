<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\ComponentSource;

/**
 * Git repository component source.
 */
class GitComponentSource extends AbstractComponentSource
        implements ComponentSource {
    /**
     * Git repository URI.
     *
     * @var string
     */
    protected $repositoryUri;

    /**
     * Git ref name.
     *
     * @var string
     */
    protected $ref;

    /**
     * Initialiser.
     *
     * @param string $repositoryUri
     * @param string $ref
     */
    public function __construct($repositoryUri, $ref) {
        $this->repositoryUri = $repositoryUri;
        $this->ref           = $ref;
    }

    /**
     * @override \ComponentManager\ComponentSource\ComponentSource
     */
    public function getName() {
        return 'Git repository';
    }

    /**
     * @override \ComponentManager\ComponentSource\ComponentSource
     */
    public function getId() {
        return 'Git';
    }

    /**
     * Get the Git repository URI.
     *
     * @return string
     */
    public function getRepositoryUri() {
        return $this->repositoryUri;
    }

    /**
     * Get the Git tag name.
     *
     * @return string
     */
    public function getRef() {
        return $this->ref;
    }
}
