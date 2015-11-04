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
            ->setHelp(static::HELP);
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $componentSpecifications = $this->getProject()->getProjectFile()->getComponentSpecifications();

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

        $this->logger->info('Writing project lock file');
        $this->getProject()->getProjectLockFile()->commit();
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

        /* Note that even at this late stage, we still might not have a final
         * version for the component:
         * -> If the package repository provides us with the Moodle
         *    $plugin->version value, we'll be using it here.
         * -> If the package repository is a version control system, the version
         *    will contain the name of a branch or tag and will need to be
         *    resolved to an individual commit. */
        $version = $component->getVersion($componentVersion);

        return new ResolvedComponentVersion(
                $specification, $packageRepository, $component, $version);
    }

    /**
     * Install a package.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     *
     * @return void
     */
    protected function installComponent(ResolvedComponentVersion $resolvedComponentVersion) {#
        $projectLockFile = $this->getProject()->getProjectLockFile();
        $component       = $resolvedComponentVersion->getComponent();
        $packageSource   = $this->getProject()->getPackageSource(
                $resolvedComponentVersion->getSpecification()->getPackageSource());

        $typeDirectory = $this->getMoodle()->getPluginTypeDirectory(
                $component->getPluginType());

        $targetDirectory = $typeDirectory . PlatformUtil::directorySeparator()
                         . $component->getPluginName();

        $tempDirectory = PlatformUtil::createTempDirectory();

        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->container->get('filesystem');

        $sourceDirectory = $packageSource->obtainPackage(
                $tempDirectory, $resolvedComponentVersion, $filesystem, $this->logger);

        if ($resolvedComponentVersion->getFinalVersion() === null) {
            $this->logger->warning('Package source did not indicate final version; defaulting to desired version', [
                'version' => $resolvedComponentVersion->getVersion()->getVersion(),
            ]);

            $resolvedComponentVersion->setFinalVersion(
                    $resolvedComponentVersion->getVersion()->getVersion());
        }

        $this->logger->debug('Downloaded component source', [
            'packageSource'   => $packageSource->getName(),
            'sourceDirectory' => $sourceDirectory,
        ]);

        if ($filesystem->exists($targetDirectory)) {
            $this->logger->info('Component directory already exists; removing', [
                'targetDirectory' => $targetDirectory,
            ]);

            $filesystem->remove($targetDirectory);
        }

        $this->logger->info('Copying component source to Moodle directory', [
            'sourceDirectory' => $sourceDirectory,
            'targetDirectory' => $targetDirectory,
        ]);
        $filesystem->mirror($sourceDirectory, $targetDirectory);

        $this->logger->info('Pinning component at installed final version', [
            'finalVersion' => $resolvedComponentVersion->getFinalVersion(),
        ]);
        $projectLockFile->addResolvedComponentVersion($resolvedComponentVersion);

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
