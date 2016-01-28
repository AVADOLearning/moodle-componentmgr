<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Component manager application entry point.
 */
class ComponentManager extends Application implements ContainerAwareInterface {
    use ContainerAwareTrait;

    /**
     * Release name.
     *
     * @var string
     */
    const VERSION = '0.1.1';

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
}
