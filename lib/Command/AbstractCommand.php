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
use Symfony\Component\Console\Command\Command;

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
     * Initialiser.
     *
     * @param \Psr\Log\LoggerInterface $logger The PSR-3 compatible logger the
     *                                         command should direct its output
     *                                         to.
     */
    public function __construct(LoggerInterface $logger) {
        parent::__construct();

        $this->logger = $logger;
    }
}
