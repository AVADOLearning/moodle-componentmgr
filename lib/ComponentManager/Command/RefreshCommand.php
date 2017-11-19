<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Console\Argument;
use ComponentManager\PackageRepository\CachingPackageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Refresh command.
 *
 * Refreshes cached package repository meta for all caching package
 * repositories.
 */
class RefreshCommand extends ProjectAwareCommand {
    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Refreshes cached package repository meta for all caching package repositories.
HELP;

    /**
     * @override Command
     */
    protected function configure() {
        $this
            ->setName('refresh')
            ->setDescription('Refreshes cached package information')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputOption(Argument::OPTION_PROJECT_FILE, null,
                                InputOption::VALUE_REQUIRED,
                                Argument::OPTION_PROJECT_FILE_HELP),
            ]));
    }

    /**
     * @override Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $projectFilename = $input->getOption(Argument::OPTION_PROJECT_FILE);

        $packageRepositories = $this->getProject($projectFilename)
            ->getPackageRepositories();

        foreach ($packageRepositories as $name => $packageRepository) {
            $logContext = ['repository' => $name];

            if ($packageRepository instanceof CachingPackageRepository) {
                $this->logger->info('Refreshing repository package cache',
                                    $logContext);
                $packageRepository->refreshMetadataCache($this->logger);
            } else {
                $this->logger->info('Skipping non-caching repository',
                                    $logContext);
            }
        }
    }
}
