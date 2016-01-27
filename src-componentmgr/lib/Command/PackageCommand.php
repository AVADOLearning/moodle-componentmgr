<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Console\Argument;
use ComponentManager\Exception\InstallationFailureException;
use ComponentManager\Helper\InstallHelper;
use ComponentManager\Helper\PackageHelper;
use ComponentManager\PackageFormat\ZipArchivePackageFormat;
use ComponentManager\PlatformUtil;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

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
        $tempDirectory       = PlatformUtil::createTempDirectory();
        $moodleArchive       = $tempDirectory
                             . PlatformUtil::directorySeparator()
                             . 'moodle.zip';
        $moodleDir           = $tempDirectory
                             . PlatformUtil::directorySeparator() . 'moodle';
        $projectLockFilename = $moodleDir . PlatformUtil::directorySeparator()
                             . 'componentmgr.lock.json';

        $destination     = $input->getOption(Argument::OPTION_PACKAGE_DESTINATION);
        $projectFilename = $input->getOption(Argument::OPTION_PROJECT_FILE);
        $packageFormat   = $input->getOption(Argument::OPTION_PACKAGE_FORMAT);

        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->container->get('filesystem');

        $projectFile = $this->getProject($projectFilename, $projectLockFilename)
                            ->getProjectFile();
        $installHelper = new InstallHelper(
                $this->getProject(), $this->getMoodle($moodleDir),
                $filesystem, $this->logger);
        $packageHelper = new PackageHelper($this->logger);

        $version = $packageHelper->resolveMoodleVersion($projectFile->getMoodleVersion());
        $this->logger->info('Selected Moodle version', [
            'specification' => $projectFile->getMoodleVersion(),
            'build'         => $version->getBuild(),
            'release'       => $version->getRelease(),
        ]);

        $this->logger->info('Downloading Moodle', [
            'downloadUri'   => $version->getDownloadUri(),
            'moodleArchive' => $moodleArchive,
        ]);
        $packageHelper->downloadMoodle($version->getDownloadUri(),
                                       $moodleArchive);

        $this->logger->info('Extracting Moodle archive');
        $moodleArchiveObj = new ZipArchive();
        if (!$moodleArchiveObj->open($moodleArchive)) {
            throw new InstallationFailureException(
                    "Unable to open archive \"{$moodleArchive}\"",
                    InstallationFailureException::CODE_EXTRACTION_FAILED);
        }
        $moodleArchiveObj->extractTo($tempDirectory);
        $moodleArchiveObj->close();

        $this->logger->info('Installing components');
        $installHelper->installProjectComponents();

        $this->logger->info('Packaging');
        $this->getProject()->getPackageFormat($packageFormat)->package(
                $moodleDir, $destination, $projectFile,
                $this->getProject()->getProjectLockFile(), $this->logger);

        $this->logger->info('Cleaning up');
        $filesystem->remove($tempDirectory);
    }
}
