<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

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
     * Map of plugin types to paths on disk.
     *
     * @var string[]
     */
    protected $pluginTypes;

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

        /* This is important: if we don't unset this key, some Xdebug
         * configurations will break the subprocess on its first line and cause
         * the first process to hang until the timeout is reached. */
        $process->setEnv(['XDEBUG_CONFIG' => '']);

        $process->run();
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

        return $pluginTypes[$type];
    }

    /**
     * @return \string[]
     */
    public function getPluginTypes() {
        if ($this->pluginTypes === null) {
            $this->pluginTypes = (array) $this->execute('plugin_types');
        }

        return $this->pluginTypes;
    }
}
