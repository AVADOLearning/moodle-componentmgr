<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\ContainerAwareTrait;
use ComponentManager\PackageRepository\PackageRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Command factory.
 *
 * Hides the finicky details of the DI container from the command classes and
 * just gives them their dependencies.
 */
class CommandFactory implements ContainerAwareInterface {
    use ContainerAwareTrait;

    /**
     * Command class name format.
     *
     * @var string
     */
    const CLASS_NAME_FORMAT = '\ComponentManager\Command\%sCommand';

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

        return new $classname($this->container);
    }

    /**
     * Get the class name for the specified command.
     *
     * @param string $name The name of the command.
     *
     * @return string The fully-qualified name of the command's corresponding
     *                class.
     */
    public static function getCommandClassName($name) {
        return sprintf(static::CLASS_NAME_FORMAT, $name);
    }
}
