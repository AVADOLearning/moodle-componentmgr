<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Console;

use ComponentManager\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Component manager application entry point.
 */
class Application extends ConsoleApplication
        implements ContainerAwareInterface {
    use ContainerAwareTrait;

    /**
     * Release name.
     *
     * @var string
     */
    const VERSION = '0.4.0';

    /**
     * Initialiser.
     */
    public function __construct() {
        parent::__construct('Component Manager', static::VERSION);
    }
}
