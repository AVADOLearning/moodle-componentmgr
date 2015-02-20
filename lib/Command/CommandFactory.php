<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use Psr\Log\LoggerInterface;

/**
 * Command factory.
 *
 * Hides the finicky details of the DI container from the command classes and
 * just gives them their dependencies.
 */
class CommandFactory {
    /**
     * Command class name format.
     *
     * @var string
     */
    const CLASS_NAME_FORMAT = '\ComponentManager\Command\%sCommand';

    /**
     * PSR-3 compliant logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Initialiser.
     *
     * @param \Psr\Log\LoggerInterface $logger The PSR-3 compliant logger
     *                                         implementation commands should
     *                                         direct their output to.
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Create a command.
     *
     * @param string $name the name of the command.
     *
     * @return \ComponentManager\Command\AbstractCommand An instance of the
     *                                                   command's corresponding
     *                                                   class.
     */
    public function createCommand($name) {
        $classname = static::getCommandClassName($name);

        return new $classname($this->logger);
    }

    /**
     * Get the class name for the specified command.
     *
     * @param string $name The name of the command.
     *
     * @return string The fully-qualified name of the command's corresponding
     *                class.
     */
    protected static function getCommandClassName($name) {
        return sprintf(static::CLASS_NAME_FORMAT, $name);
    }
}
