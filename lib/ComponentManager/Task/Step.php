<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Task;

use Psr\Log\LoggerInterface;

/**
 * Step interface.
 *
 * Steps define individual actions which should take place as part of an
 * installation or packaging operation.
 */
interface Step {
    /**
     * Execute this action.
     * 
     * @param \ComponentManager\Task\Task $task
     * @param \Psr\Log\LoggerInterface    $logger
     *
     * @return void
     */
    public function execute($task, LoggerInterface $logger);
}
