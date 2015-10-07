<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\Exception\MoodleException;
use Symfony\Component\Process\Process;

/**
 * Moodle installation.
 *
 * Facilitates interaction with our accompanying Moodle plugin, allowing us to
 * query Moodle's plugin types, installed plugins, etc. This design allows us
 * to avoid duplication whilst not including the entire Moodle framework.
 */
class Moodle {
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

        $pluginCliDirectory = ['local', 'componentmgr', 'cli'];
        $this->pluginCliDirectory = $this->rootDirectory;
        foreach ($pluginCliDirectory as $directory) {
            $this->pluginCliDirectory .= PlatformUtil::directorySeparator()
                                      .  $directory;
        }
    }

    /**
     * Execute a CLI script within the plugin.
     *
     * @param string $script
     *
     * @return mixed JSON-decoded output.
     */
    protected function execute($script) {
        $php = PlatformUtil::phpExecutable();

        $scriptFilename = $this->pluginCliDirectory
                        . PlatformUtil::directorySeparator()
                        . $script . '.php';

        $process = new Process("{$php} {$scriptFilename}",
                               $this->rootDirectory);

        /* For the most part, we want to pass environment variables straight
         * through to our children. This is especially necessary for the unlucky
         * ones using SQL Server with the FreeTDS driver, which requires its
         * configuration file's location to be indicated via the FREETDS
         * environment variable. */
        $environment = $_ENV;

        /* This override is important: if we don't unset this index, some Xdebug
         * configurations will break the subprocess on its first line and cause
         * the first process to hang until the timeout is reached. */
        unset($environment['XDEBUG_CONFIG']);

        $process->setEnv($environment);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new MoodleException(
                    "Unable to execute CLI script \"{$script}\"; is the local_componentmgr plugin installed?",
                    MoodleException::CODE_EXECUTION_FAILED);
        }
        $output = $process->getOutput();
        $result = json_decode($output);

        return $result;
    }

    /**
     * Get a plugin type's root directory.
     *
     * @param string $type
     *
     * @return string
     */
    public function getPluginTypeDirectory($type) {
        $pluginTypes = $this->getPluginTypes();

        if (!array_key_exists($type, $pluginTypes)) {
            throw new InvalidProjectException(
                    "Plugin type \"{$type}\" is not known; cannot determine target directory",
                    InvalidProjectException::CODE_INVALID_PLUGIN_TYPE);
        }

        return $pluginTypes[$type];
    }

    /**
     * Retrieve a list of all known plugin types.
     *
     * @return \string[]
     */
    public function getPluginTypes() {
        return (array) $this->execute('plugin_types');
    }
}
