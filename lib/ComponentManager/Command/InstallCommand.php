<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Moodle;
use ComponentManager\PackageFormat\PackageFormatFactory;
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSourceFactory;
use ComponentManager\Platform\Platform;
use ComponentManager\Task\InstallTask;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install command.
 *
 * Installs a component into the Moodle installation in the present working
 * directory.
 */
class InstallCommand extends ProjectAwareCommand {
    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Installs, into the Moodle installation in the present working directory, all of the components listed in its componentmgr.json file.
HELP;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\PackageRepository\PackageRepositoryFactory $packageRepositoryFactory
     * @param \ComponentManager\PackageSource\PackageSourceFactory $packageSourceFactory
     * @param \ComponentManager\PackageFormat\PackageFormatFactory $packageFormatFactory
     * @param \ComponentManager\Platform\Platform                  $platform
     * @param \Symfony\Component\Filesystem\Filesystem             $filesystem
     * @param \Psr\Log\LoggerInterface                             $logger
     */
    public function __construct(PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory,
                                Platform $platform, Filesystem $filesystem,
                                LoggerInterface $logger) {
        parent::__construct(
                $packageRepositoryFactory, $packageSourceFactory,
                $packageFormatFactory, $platform, $filesystem, $logger);
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this
            ->setName('install')
            ->setDescription('Installs all packages from componentmgr.json')
            ->setHelp(static::HELP);
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $project = $this->getProject();
        $moodle  = new Moodle(
                $this->platform->getWorkingDirectory(), $this->platform);

        $task = new InstallTask($project, $this->filesystem, $moodle);
        $task->execute($this->logger);
    }
}
