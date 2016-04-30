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
use ComponentManager\PlatformUtil;
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
     * Filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Moodle.org plugin and update API.
     *
     * @var MoodleApi
     */
    protected $moodleApi;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\MoodleApi              $moodleApi
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory,
                                MoodleApi $moodleApi, Filesystem $filesystem,
                                LoggerInterface $logger) {
        $this->moodleApi  = $moodleApi;
        $this->filesystem = $filesystem;

        parent::__construct(
                $packageRepositoryFactory, $packageSourceFactory,
                $packageFormatFactory, $logger);
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
        $packageDestination = PlatformUtil::expandPath(
                $input->getOption(Argument::OPTION_PACKAGE_DESTINATION));
        $packageFormat   = $input->getOption(Argument::OPTION_PACKAGE_FORMAT);

        $tempDirectory       = PlatformUtil::createTempDirectory();
        $archive             = $tempDirectory
                             . PlatformUtil::directorySeparator()
                             . 'moodle.zip';
        $destination         = $tempDirectory
                             . PlatformUtil::directorySeparator() . 'moodle';
        $projectLockFilename = $destination . PlatformUtil::directorySeparator()
                             . 'componentmgr.lock.json';

        $moodle  = new Moodle($destination);
        $project = $this->getProject($projectFilename, $projectLockFilename);

        $task = new PackageTask(
                $this->moodleApi, $project, $archive, $destination,
                $this->filesystem, $moodle, $packageFormat,
                $packageDestination);
        $task->execute($this->logger);
    }
}
