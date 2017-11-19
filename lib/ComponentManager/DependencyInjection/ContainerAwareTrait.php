<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait {
    /**
     * Dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set the dependency injection container.
     *
     * @param ContainerInterface|null $container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container=null) {
        $this->container = $container;
    }
}
