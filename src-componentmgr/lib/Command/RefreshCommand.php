<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\Console\Argument;
use ComponentManager\PackageRepository\CachingPackageRepository;
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
class RefreshCommand extends AbstractCommand {
    use ProjectAwareCommandTrait;

    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Refreshes cached package repository meta for all caching package repositories.
HELP;

    /**
     * @override \Symfony\Component\Console\Command\Command
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
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $projectFilename = $input->getOption(Argument::OPTION_PROJECT_FILE);

        $packageRepositories = $this->getProject($projectFilename)
            ->getPackageRepositories();

        foreach ($packageRepositories as $packageRepository) {
            $logContext = ['repository' => $packageRepository];

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
