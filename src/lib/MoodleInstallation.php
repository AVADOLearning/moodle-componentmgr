<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

use core_plugin_manager;
use Symfony\Component\Process\Process;

/**
 * Moodle installation.
 *
 * Facilitates interaction with a Moodle installation. This class should be
 * used only by the MoodleCommand class. Elsewhere, use the Moodle class
 * instead. */
class MoodleInstallation {
    /**
     * Configuration file filename.
     *
     * @var string
     */
    const CONFIG_FILENAME = 'config.php';

    /**
     * The value of the Moodle $CFG object.
     *
     * We have to cache the result of the first call to getConfig() as
     * constants can be declared within this file, causing nasty warnings.
     *
     * @var \stdClass
     */
    protected $config;

    /**
     * Root directory of the Moodle installation.
     *
     * @var string
     */
    protected $rootDirectory;

    /**
     * Initialiser.
     *
     * @param string $rootDirectory
     */
    public function __construct($rootDirectory) {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Require the configuration file.
     *
     * @return \stdClass The $CFG object.
     */
    protected function getConfig() {
        if ($this->config === null) {
            $constants = [
                'ABORT_AFTER_CONFIG',
                'CLI_SCRIPT',
                'IGNORE_COMPONENT_CACHE',
            ];

            foreach ($constants as $constant) {
                define($constant, true);
            }

            $path = $this->rootDirectory . PlatformUtil::directorySeparator()
                  . static::CONFIG_FILENAME;
            require_once $path;

            $this->config = $CFG;
        }

        return $this->config;
    }

    /**
     * Get plugin types.
     *
     * @return string[]
     */
    public function getPluginTypes() {
        $CFG = $this->getConfig();
        require_once "{$CFG->dirroot}/lib/classes/plugin_manager.php";

        $pluginMgr = core_plugin_manager::instance();

        return $pluginMgr->get_plugin_types();
    }
}
