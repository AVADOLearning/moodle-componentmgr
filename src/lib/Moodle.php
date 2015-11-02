<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;
use ComponentManager\Exception\MoodleException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Moodle installation client.
 */
class Moodle {
    /**
     * Get plugin types.
     *
     * @return string[]
     */
    public function getPluginTypes() {
        $process = $this->getProcess(['list-plugin-types']);
        $process->run();

        $this->ensureSuccess($process);

        return json_decode($process->getOutput());
    }

    /**
     * Get the root directory for a plugin type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getPluginTypeDirectory($type) {
        $pluginTypes = $this->getPluginTypes();

        return $pluginTypes->{$type};
    }

    /**
     * Get a ready-to-run process instance.
     *
     * @param mixed[] $arguments
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess($arguments) {
        $prefix = [
            PlatformUtil::phpExecutable(),
            PlatformUtil::phpScript(),
            'moodle',
        ];

        $arguments = array_merge($prefix, $arguments);
        $builder = new ProcessBuilder($arguments);

        return $builder->getProcess();
    }

    /**
     * Ensure the specified command executed successfully.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @throws \ComponentManager\Exception\MoodleException
     */
    protected function ensureSuccess(Process $process) {
        if (!$process->isSuccessful()) {
            $command = $process->getCommandLine();
            throw new MoodleException("Unable to execute command \"{$command}\"");
        }
    }
}
