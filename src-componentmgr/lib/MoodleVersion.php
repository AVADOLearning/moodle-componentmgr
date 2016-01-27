<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

/**
 * Moodle version structure.
 *
 * Used to represent available Moodle releases during packaging.
 */
class MoodleVersion {
    /**
     * Regular expression: match parts of a branch identifier.
     *
     * @var string
     */
    const REGEX_BRANCH = '/([0-9]+\.[0-9]+)(\+?)/';

    /**
     * Branch match: branch.
     *
     * @var integer
     */
    const REGEX_BRANCH_BRANCH = 1;

    /**
     * Branch match: with fixes?
     *
     * @var integer
     */
    const REGEX_BRANCH_FIXES = 2;

    /**
     * Build number.
     *
     * @var float
     */
    protected $build;

    /**
     * Release name.
     *
     * @var string
     */
    protected $release;

    /**
     * Branch name (S.M).
     *
     * @var string
     */
    protected $branch;

    /**
     * Maturity level.
     *
     * @var int
     */
    protected $maturity;

    /**
     * Download URI.
     *
     * @var string
     */
    protected $downloadUri;

    /**
     * Initialiser.
     *
     * @param float   $build
     * @param string  $release
     * @param string  $branch
     * @param integer $maturity
     * @param string  $downloadUri
     */
    public function __construct($build, $release, $branch, $maturity,
                                $downloadUri) {
        $this->build       = (float) $build;
        $this->release     = $release;
        $this->branch      = $branch;
        $this->maturity    = $maturity;
        $this->downloadUri = $downloadUri;
    }

    /**
     * Get the build number.
     *
     * @return float
     */
    public function getBuild() {
        return $this->build;
    }

    /**
     * Get the release name.
     *
     * @return string
     */
    public function getRelease() {
        return $this->release;
    }

    /**
     * Get the branch name.
     *
     * @return string
     */
    public function getBranch() {
        return $this->branch;
    }

    /**
     * Get the maturity level.
     *
     * @return int
     */
    public function getMaturity() {
        return $this->maturity;
    }

    /**
     * Get the download URI.
     *
     * @return string
     */
    public function getDownloadUri() {
        return $this->downloadUri;
    }

    /**
     * Does this Moodle version satisfy the supplied specification?
     *
     * @param string $specification
     *
     * @return integer
     */
    public function satisfies($specification) {
        if ((float) $specification === $this->build) {
            return true;
        }

        $parts = [];
        if (preg_match(static::REGEX_BRANCH, $specification, $parts)
                && $parts[static::REGEX_BRANCH_BRANCH] === $this->branch) {
            $hasFixes   = $this->isWithFixes();
            $wantsFixes = (array_key_exists(static::REGEX_BRANCH_FIXES, $parts)
                    && $parts[static::REGEX_BRANCH_FIXES] === '+');

            if ($wantsFixes) {
                return $hasFixes ? 100 : 50;
            } else {
                return $hasFixes ? 50 : 100;
            }
        }

        return 0;
    }

    /**
     * Does this version ship with fixes?
     *
     * i.e., is it a S.M+ release?
     *
     * @return boolean
     */
    public function isWithFixes() {
        // Decimal build numbers indicate weekly releases
        return floor($this->build) != $this->build;
    }
}
