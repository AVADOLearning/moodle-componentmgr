<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\Helper\InstallHelper;
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
        $helper = new InstallHelper(
                $this->getProject(), $this->getMoodle(),
                $this->container->get('filesystem'), $this->logger);

        $helper->installProjectComponents();
    }
}
