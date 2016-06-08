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
use ComponentManager\Moodle;
use ComponentManager\MoodleApi;
use ComponentManager\PackageFormat\PackageFormatFactory;
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSourceFactory;
use ComponentManager\Platform\Platform;
use ComponentManager\Task\PackageTask;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package command.
 *
 * Assembles an entire Moodle instance from the specified project file, then
 * packages it in the specified format.
 */
class PackageCommand extends ProjectAwareCommand {
    /**
     * Help.
     *
     * @var string
     */
    const HELP = <<<HELP
Packages a Moodle site from a project file.
HELP;

    /**
     * Moodle.org plugin and update API.
     *
     * @var MoodleApi
     */
    protected $moodleApi;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\PackageRepository\PackageRepositoryFactory $packageRepositoryFactory
     * @param \ComponentManager\PackageSource\PackageSourceFactory         $packageSourceFactory
     * @param \ComponentManager\PackageFormat\PackageFormatFactory         $packageFormatFactory
     * @param \ComponentManager\MoodleApi                                  $moodleApi
     * @param \Symfony\Component\Filesystem\Filesystem                     $filesystem
     * @param \ComponentManager\Platform\Platform                          $platform
     * @param \Psr\Log\LoggerInterface                                     $logger
     */
    public function __construct(PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory,
                                MoodleApi $moodleApi, Filesystem $filesystem,
                                Platform $platform, LoggerInterface $logger) {
        $this->moodleApi = $moodleApi;

        parent::__construct(
                $packageRepositoryFactory, $packageSourceFactory,
                $packageFormatFactory, $platform, $filesystem, $logger);
    }

    /**
     * @override \ComponentManager\Command\AbstractCommand
     */
    protected function configure() {
        $this
            ->setName('package')
            ->setDescription('Packages a Moodle site from a project file')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputOption(Argument::OPTION_PACKAGE_FORMAT, null,
                                InputOption::VALUE_REQUIRED,
                                Argument::OPTION_PACKAGE_FORMAT_HELP),
                new InputOption(Argument::OPTION_PROJECT_FILE, null,
                                InputOption::VALUE_REQUIRED,
                                Argument::OPTION_PROJECT_FILE_HELP),
                new InputOption(Argument::OPTION_PACKAGE_DESTINATION, null,
                                InputOption::VALUE_REQUIRED,
                                Argument::OPTION_PACKAGE_DESTINATION_HELP),
            ]));
    }

    /**
     * @override \ComponentManager\Command\AbstractCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $projectFilename = $input->getOption(Argument::OPTION_PROJECT_FILE);
        $packageDestination = $this->platform->expandPath(
                $input->getOption(Argument::OPTION_PACKAGE_DESTINATION));
        $packageFormat   = $input->getOption(Argument::OPTION_PACKAGE_FORMAT);

        $tempDirectory       = $this->platform->createTempDirectory();
        $archive             = $tempDirectory
                             . $this->platform->getDirectorySeparator()
                             . 'moodle.zip';
        $destination         = $tempDirectory
                             . $this->platform->getDirectorySeparator() . 'moodle';
        $projectLockFilename = $destination . $this->platform->getDirectorySeparator()
                             . 'componentmgr.lock.json';

        $moodle  = new Moodle($destination, $this->platform);
        $project = $this->getProject($projectFilename, $projectLockFilename);

        $task = new PackageTask(
                $this->moodleApi, $project, $archive, $destination,
                $this->platform, $this->filesystem, $moodle, $packageFormat,
                $packageDestination);
        $task->execute($this->logger);
    }
}
