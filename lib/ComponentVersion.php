<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;
use ComponentManager\ComponentSource\ComponentSource;

/**
 * Component version.
 *
 * A component version represents an individual release of a component.
 */
class ComponentVersion {
    /**
     * Maturity: alpha.
     *
     * @var integer
     */
    const MATURITY_ALPHA = 50;

    /**
     * Maturity: beta.
     *
     * @var integer
     */
    const MATURITY_BETA = 100;

    /**
     * Maturity: release candidate (RC).
     *
     * @var integer
     */
    const MATURITY_RC = 150;

    /**
     * Maturity: stable.
     *
     * @var integer
     */
    const MATURITY_STABLE = 200;

    /**
     * Moodle component version.
     *
     * @var integer
     */
    protected $version;

    /**
     * Release name.
     *
     * @var string
     */
    protected $release;

    /**
     * Version maturity.
     *
     * One of the MATURITY_* constants.
     *
     * @var integer
     */
    protected $maturity;

    /**
     * Component sources.
     *
     * @var integer
     */
    protected $sources;

    /**
     * Initialiser.
     *
     * @param integer                                             $version
     * @param string                                              $release
     * @param integer                                             $maturity
     * @param \ComponentManager\ComponentSource\ComponentSource[] $sources
     */
    public function __construct($version, $release, $maturity, $sources=null) {
        $this->version  = $version;
        $this->release  = $release;
        $this->maturity = $maturity;

        if ($sources !== null) {
            foreach ($sources as $source) {
                $this->addSource($source);
            }
        }
    }

    /**
     * Add a component source.
     *
     * @param \ComponentManager\ComponentSource\ComponentSource $source
     *
     * @return void
     */
    public function addSource(ComponentSource $source) {
        $this->sources[] = $source;
    }

    /**
     * Get release maturity.
     *
     * One of the MATURITY_* constants.
     *
     * @return integer
     */
    public function getMaturity() {
        return $this->maturity;
    }

    /**
     * Get release name.
     *
     * @return string
     */
    public function getRelease() {
        return $this->release;
    }

    /**
     * Get Moodle component version.
     *
     * @return integer
     */
    public function getVersion() {
        return $this->version;
    }
}
