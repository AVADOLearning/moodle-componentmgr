<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\ComponentSpecification;
use ComponentManager\Console\Argument;
use ComponentManager\Exception\InstallationFailureException;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PlatformUtil;
use ComponentManager\ResolvedComponentVersion;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Install command.
 *
 * Installs a component into the Moodle installation in the present working
 * directory.
 */
class InstallCommand extends AbstractCommand {
    use ProjectAwareCommandTrait;

    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Installs, into the Moodle installation in the present working directory, all of the components listed in its componentmgr.json file.
HELP;

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this
            ->setName('install')
            ->setDescription('Installs all packages from componentmgr.json')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputOption(Argument::OPTION_DRY_RUN, null,
                                InputOption::VALUE_NONE,
                                Argument::OPTION_DRY_RUN_HELP),
            ]));
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if ($input->getOption(Argument::OPTION_DRY_RUN)) {
            $this->logger->info('Performing a dry run; not applying changes');
        }

        $project                 = $this->getProject();
        $componentSpecifications = $project->getProjectFile()->getComponentSpecifications();
        $projectLockFile         = $project->getProjectLockFile();

        /** @var \ComponentManager\ResolvedComponentVersion[] $resolvedComponents */
        $resolvedComponents = [];

        /* TODO: resolve dependencies before attempting installation. For the
         *       time being, we'll just assume that the developer has specified
         *       all necessary components in the project file. */

        foreach ($componentSpecifications as $componentSpecification) {
            $this->logger->info('Resolving component version', [
                'component'         => $componentSpecification->getName(),
                'packageRepository' => $componentSpecification->getPackageRepository(),
                'version'           => $componentSpecification->getVersion(),
            ]);

            $resolvedComponents[] = $this->resolveComponentVersion(
                    $componentSpecification);
        }

        foreach ($resolvedComponents as $resolvedComponent) {
            $this->logger->info('Installing component', [
                'component'         => $resolvedComponent->getComponent()->getName(),
                'packageRepository' => $resolvedComponent->getPackageRepository()->getName(),
                'version'           => $resolvedComponent->getVersion()->getVersion(),
                'release'           => $resolvedComponent->getVersion()->getRelease(),
            ]);

            $this->installComponent($resolvedComponent);
        }
    }

    /**
     * Resolve a component's version.
     *
     * @param \ComponentManager\ComponentSpecification $specification
     *
     * @return \ComponentManager\ResolvedComponentVersion
     */
    protected function resolveComponentVersion(ComponentSpecification $specification) {
        $packageRepository = $this->getProject()->getPackageRepository(
                $specification->getPackageRepository());

        $componentName         = $specification->getName();
        $componentVersion      = $specification->getVersion();
        $packageRepositoryName = $specification->getPackageRepository();

        if (!$component = $packageRepository->getComponent($specification)) {
            throw new InvalidProjectException(
                    "The component \"{$componentName}\" could not be found within repository \"{$packageRepositoryName}\"",
                    InvalidProjectException::CODE_MISSING_COMPONENT);
        }

        $version = $component->getVersion($componentVersion);

        return new ResolvedComponentVersion(
                $specification, $packageRepository, $component, $version);
    }

    /**
     * Install a package.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponent
     *
     * @return void
     */
    protected function installComponent(ResolvedComponentVersion $resolvedComponent) {
        $component     = $resolvedComponent->getComponent();
        $packageSource = $this->getProject()->getPackageSource(
                $resolvedComponent->getSpecification()->getPackageSource());

        $typeDirectory = $this->getMoodle()->getPluginTypeDirectory(
                $component->getPluginType());

        $targetDirectory = $typeDirectory . PlatformUtil::directorySeparator()
                         . $component->getPluginName();

        $tempDirectory = PlatformUtil::createTempDirectory();

        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->container->get('filesystem');

        $sourceDirectory = $packageSource->obtainPackage(
                $tempDirectory, $resolvedComponent->getComponent(),
                $resolvedComponent->getVersion(), $filesystem,
                $this->logger);

        $this->logger->debug('Downloaded component source', [
            'packageSource'   => $packageSource->getName(),
            'sourceDirectory' => $sourceDirectory,
            'targetDirectory' => $targetDirectory,
        ]);

        if ($filesystem->exists($targetDirectory)) {
            $this->logger->info('Component directory already exists; removing', [
                'targetDirectory' => $targetDirectory,
            ]);

            $filesystem->remove($targetDirectory);
        }

        $filesystem->mirror($sourceDirectory, $targetDirectory);

        $this->logger->info('Cleaning up after component installation', [
            'tempDirectory' => $tempDirectory,
        ]);

        try {
            $filesystem->chmod([$tempDirectory], 0750, 0000, true);
            $filesystem->remove([$tempDirectory]);
        } catch (IOException $e) {
            $this->logger->warning('Unable to clean up temporary directory', [
                'code'          => $e->getCode(),
                'message'       => $e->getMessage(),
                'tempDirectory' => $tempDirectory,
            ]);
        }
    }
}
