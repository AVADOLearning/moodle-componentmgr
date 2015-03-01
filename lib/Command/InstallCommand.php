<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

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
     * Argument: component.
     *
     * @var string
     */
    const ARG_COMPONENT = 'component';

    /**
     * Option: source.
     *
     * @var string
     */
    const OPT_SOURCE = 'source';

    /**
     * Short option: source.
     *
     * @var string
     */
    const OPT_SOURCE_SHORT = 's';

    /**
     * Option: release.
     *
     * @var string
     */
    const OPT_RELEASE = 'release';

    /**
     * Short option: release.
     *
     * @var string
     */
    const OPT_RELEASE_SHORT = 'r';

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
                new InputArgument(static::ARG_COMPONENT,
                                  InputArgument::REQUIRED),
                new InputOption(static::OPT_SOURCE, static::OPT_SOURCE_SHORT,
                                InputOption::VALUE_REQUIRED),
                new InputOption(static::OPT_RELEASE, static::OPT_RELEASE_SHORT,
                                InputOption::VALUE_REQUIRED),
            ]));
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->logger->info('Installing component', [
            'component' => $input->getArgument(static::ARG_COMPONENT),
        ]);

        $this->logger->emerg('SHIT! We didn\'t write that yet.');
    }
}
