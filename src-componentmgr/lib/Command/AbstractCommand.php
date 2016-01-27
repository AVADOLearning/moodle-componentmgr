<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract command.
 *
 * All Component Manager commands will extend this abstract class. Our objective
 * here is to avoid having make all of our commands container-aware so that
 * they're able to obtain their dependencies. See CommandFactory for the other
 * half of this effort.
 */
class AbstractCommand extends Command {
    /**
     * PSR-3 compatible logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Package repositories.
     *
     * @var \ComponentManager\PackageRepository\PackageRepository[]
     */
    protected $packageRepositories = [];

    /**
     * Initialiser.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        parent::__construct();

        $this->container = $container;

        $this->logger = $container->get('logger');
    }
}
