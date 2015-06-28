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
use ComponentManager\Project;
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

        $project                 = $this->getProject();
        $componentSpecifications = $project->getComponents();

        foreach ($componentSpecifications as $componentSpecification) {
            $packageRepository = $project->getPackageRepository(
                    $componentSpecification->getPackageRepository());
            $packageSource     = $project->getPackageSource(
                    $componentSpecification->getPackageSource());

            $component = $packageRepository->getComponent($componentSpecification);
        }
    }
}
