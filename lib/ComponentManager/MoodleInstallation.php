<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use ComponentManager\Exception\MoodleException;
use ComponentManager\Exception\PlatformException;
use ComponentManager\Platform\Platform;
use core_plugin_manager;
use stdClass;

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
     * Instance state: new.
     *
     * The instance has not yet been configured, and is not ready to use.
     *
     * @var integer
     */
    const STATE_NEW = 0;

    /**
     * Instance state: ready for use.
     *
     * The instance has been configured and is ready for use.
     *
     * @var integer
     */
    const STATE_READY = 1;

    /**
     * Instance state: disposed.
     *
     * The instance was configured, but has since been disposed. It is no longer
     * usable, and a new instance will need to be created to proceed.
     *
     * @var integer
     */
    const STATE_DISPOSED = 2;

    /**
     * The value of the Moodle $CFG object.
     *
     * We have to cache the result of the first call to getConfig() as
     * constants can be declared within this file, causing nasty warnings.
     *
     * @var stdClass
     */
    protected $config;

    /**
     * Platform support library.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * Root directory of the Moodle installation.
     *
     * @var string
     */
    protected $rootDirectory;

    /**
     * Instance state.
     *
     * @var integer
     */
    protected $state;

    /**
     * Initialiser.
     *
     * @param Platform $platform
     * @param string   $rootDirectory
     */
    public function __construct(Platform $platform, $rootDirectory) {
        $this->state = static::STATE_NEW;

        $this->platform      = $platform;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Assert that the instance's state is as expected.
     *
     * @param $expected
     *
     * @return void
     *
     * @throws MoodleException
     */
    protected function assertState($expected) {
        if ($this->state !== $expected) {
            throw new MoodleException(
                    sprintf('Expected state "%d", actual state was "%d"', $expected, $this->state),
                    MoodleException::CODE_INVALID_STATE);
        }
    }

    /**
     * Require the configuration file.
     *
     * @return void
     *
     * @throws MoodleException
     */
    public function configure() {
        $this->assertState(static::STATE_NEW);

        /* This value should be overwritten shortly, either by the definition in
         * the Moodle instance's configuration or by our own "fake"
         * configuration used to load the plugin manager. */
        $CFG = null;

        $constants = [
            'ABORT_AFTER_CONFIG',
            'CLI_SCRIPT',
            'IGNORE_COMPONENT_CACHE',
        ];

        foreach ($constants as $constant) {
            define($constant, true);
        }

        $path = $this->platform->joinPaths([
            $this->rootDirectory,
            static::CONFIG_FILENAME,
        ]);
        if (is_file($path)) {
            require_once $path;

            if (!is_object($CFG)) {
                throw new MoodleException(
                        "The Moodle configuration file \"{$path}\" did not define \$CFG",
                        MoodleException::CODE_NOT_CONFIGURED);
            }
        } else {
            /* We don't have a configured site, so we'll have to fake it.
             * Only an extremely slim portion of Moodle will function
             * correctly in this state, as we've not actually done most of
             * the Moodle setup dance. It seems to be enough to run the
             * plugin manager. */

            global $CFG;
            $CFG = (object) [
                'dirroot'  => $this->rootDirectory,
                'dataroot' => $this->platform->createTempDirectory(),
                'wwwroot'  => 'http://localhost',
            ];

            require_once "{$CFG->dirroot}/lib/setup.php";
        }

        $this->config = $CFG;
        $this->state  = static::STATE_READY;
    }

    public function dispose() {
        $this->assertState(static::STATE_READY);

        try {
            $this->platform->removeTempDirectory($this->config->dataroot);
        } catch (PlatformException $e) {
            if ($e->getCode() !== PlatformException::CODE_UNKNOWN_TEMP_DIRECTORY) {
                throw $e;
            }
        }

        $this->state = static::STATE_DISPOSED;
    }

    /**
     * Get plugin types.
     *
     * @return string[]
     */
    public function getPluginTypes() {
        $this->assertState(static::STATE_READY);

        $CFG = $this->config;
        require_once "{$CFG->dirroot}/lib/classes/plugin_manager.php";

        $pluginMgr = core_plugin_manager::instance();

        return $pluginMgr->get_plugin_types();
    }
}
