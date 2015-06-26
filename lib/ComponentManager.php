<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

use ComponentManager\Command\InstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Component manager application entry point.
 */
class ComponentManager extends Application implements ContainerAwareInterface {
    /**
     * Release name.
     *
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * Dependency injection container.
     *
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Command\AbstractCommand[] Commands to handle
     *                                                    within the
     *                                                    application.
     */
    public function __construct($commands) {
        parent::__construct('Component Manager', static::VERSION);

        foreach ($commands as $command) {
            $this->add($command);
        }
    }

    /**
     * Set the dependency injection container.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container=null) {
        $this->container = $container;
    }
}
