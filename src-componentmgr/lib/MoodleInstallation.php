<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
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
            if (is_file($path)) {
                require_once $path;
            } else {
                /* We don't have a configured site, so we'll have to fake it.
                 * Only an extremely slim portion of Moodle will function
                 * correctly in this state, as we've not actually done most of
                 * the Moodle setup dance. It seems to be enough to run the
                 * plugin manager. */

                global $CFG;
                $CFG = (object) [
                    'dirroot'  => $this->rootDirectory,
                    'dataroot' => PlatformUtil::createTempDirectory(),
                    'wwwroot'  => 'http://localhost',
                ];

                require_once "{$CFG->dirroot}/lib/setup.php";
            }

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
