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
use ComponentManager\PlatformUtil;
use ComponentManager\ResolvedComponentVersion;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $componentSpecifications = $this->getProject()->getComponents();

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

        $component = $packageRepository->getComponent($specification);
        $version   = $component->getVersion($specification->getVersion());

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
        $packageSource = $this->getProject()->getPackageSource(
                $resolvedComponent->getSpecification()->getPackageSource());

        $tempDirectory = PlatformUtil::createTempDirectory();

        try {
            $packageSource->obtainPackage(
                    $tempDirectory, $resolvedComponent->getComponent(),
                    $resolvedComponent->getVersion(), $this->logger);
        } catch (InstallationFailureException $e) {
            $this->logger->emergency($e->getMessage());
        }
    }
}
