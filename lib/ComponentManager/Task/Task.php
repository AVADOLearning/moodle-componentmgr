<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Task;

use ComponentManager\Step\Step;
use Psr\Log\LoggerInterface;

/**
 * Task harness.
 *
 * A task is a collection of steps and accompanying configuration. Different
 * implementations are available with different configuration options.
 */
interface Task {
    /**
     * Add a step.
     *
     * @param \ComponentManager\Step\Step $step
     *
     * @return void
     */
    public function addStep(Step $step);

    /**
     * Execute steps.
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function execute(LoggerInterface $logger);
}
