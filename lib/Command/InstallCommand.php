<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\Argument;
use Symfony\Component\Console\Input\InputArgument;
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
    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Installs a component into the Moodle installation in the present working directory.

If no source is specified, the component's package will be sourced from the plugin directory on Moodle.org.

The latest available stable release for your installed Moodle version will be used if no version is specified.
HELP;

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this
            ->setName('install')
            ->setDescription('Installs a package')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputArgument(Argument::ARG_COMPONENT,
                                  InputArgument::REQUIRED),
                new InputOption(Argument::OPT_SOURCE, Argument::OPT_SOURCE_SHORT,
                                InputOption::VALUE_REQUIRED),
                new InputOption(Argument::OPT_RELEASE, Argument::OPT_RELEASE_SHORT,
                                InputOption::VALUE_REQUIRED),
            ]));
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->logger->info('Installing component', [
            'component' => $input->getArgument(Argument::ARG_COMPONENT),
        ]);

        $this->logger->emerg('SHIT! We didn\'t write that yet.');
    }
}
