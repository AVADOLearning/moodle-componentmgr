<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\PackageRepository\PackageRepository;
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
     * Package repositories.
     *
     * @var \ComponentManager\PackageRepositories\AbstractPackageRepository[]
     */
    protected $packageRepositories = [];

    /**
     * Initialiser.
     *
     * @param \Psr\Log\LoggerInterface $logger              The PSR-3 compatible
     *                                                      logger the command
     *                                                      should direct its
     *                                                      output to.
     * @param \ComponentManager\PackageRepositories\AbstractPackageRepository[]
     *                                 $packageRepositories Enabled package
     *                                                      repositories.
     */
    public function __construct(LoggerInterface $logger,
                                $packageRepositories=[]) {
        parent::__construct();

        $this->logger = $logger;

        foreach ($packageRepositories as $packageRepository) {
            $this->addPackageRepository($packageRepository);
        }
    }

    /**
     * Add a package repository.
     *
     * @param \ComponentManager\PackageRepositories\AbstractPackageRepository
     *        $packageRepository
     *
     * @return void
     */
    protected function addPackageRepository(PackageRepository $packageRepository) {
        $this->packageRepositories[] = $packageRepository;
    }
}
