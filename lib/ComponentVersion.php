<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

class ComponentVersion {
    const MATURITY_ALPHA = 50;
    const MATURITY_BETA = 100;
    const MATURITY_RC = 150;
    const MATURITY_STABLE = 200;

    protected $version;
    protected $release;
    protected $maturity;

    public function __construct($version, $release, $maturity) {
        $this->version  = $version;
        $this->release  = $release;
        $this->maturity = $maturity;
    }
}
