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
use ComponentManager\PlatformUtil;
use ComponentManager\Task\PackageTask;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PackageCommand extends AbstractCommand {
        use ProjectAwareCommandTrait;
    /**
     * Help.
     *
     * @var string
     */
    const HELP = <<<HELP
Packages a Moodle site from a project file.
HELP;

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

        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->container->get('filesystem');
        /** @var \ComponentManager\MoodleApi $moodleApi */
        $moodleApi  = $this->container->get('moodleApi');

        $moodle  = new Moodle($destination);
        $project = $this->getProject($projectFilename, $projectLockFilename);

        $task = new PackageTask(
                $moodleApi, $project, $archive, $destination, $filesystem,
                $moodle, $packageFormat, $packageDestination);
        $task->execute($this->logger);
    }
}
