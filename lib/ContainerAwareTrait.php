<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait {
    /**
     * Dependency injection container.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

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
