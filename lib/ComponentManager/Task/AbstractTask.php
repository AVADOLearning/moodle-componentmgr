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
 * Abstract task implementation.
 *
 * Provides task-related utility methods.
 */
abstract class AbstractTask {
    /**
     * Steps.
     *
     * @var Step[]
     */
    protected $steps;

    /**
     * Initialiser.
     */
    public function __construct() {
        $this->steps = [];
    }

    /**
     * @inheritdoc Task
     */
    public function addStep(Step $step) {
        $this->steps[] = $step;
    }

    /**
     * @inheritdoc Task
     */
    public function execute(LoggerInterface $logger) {
        foreach ($this->steps as $step) {
            $step->execute($this, $logger);
        }
    }
}
