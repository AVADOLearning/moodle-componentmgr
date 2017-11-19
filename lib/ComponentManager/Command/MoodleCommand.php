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
use ComponentManager\Exception\MoodleException;
use ComponentManager\MoodleInstallation;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Moodle command.
 *
 * Provides a query interface to a Moodle installation.
 */
class MoodleCommand extends ProjectAwareCommand {
    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Queries properties of the Moodle installation in the present working directory.
HELP;

    /**
     * @override Command
     */
    protected function configure() {
        $this
            ->setName('moodle')
            ->setDescription('Queries properties of a Moodle installation')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputArgument(Argument::ARGUMENT_ACTION,
                                  InputArgument::REQUIRED,
                                  Argument::ARGUMENT_ACTION_HELP),
                new InputOption(Argument::ARGUMENT_MOODLE_DIR, null,
                                InputOption::VALUE_REQUIRED,
                                Argument::ARGUMENT_MOODLE_DIR_HELP),
            ]));
    }

    /**
     * @override Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $action = $input->getArgument(Argument::ARGUMENT_ACTION);
        if (!$moodleDir = $input->getOption(Argument::ARGUMENT_MOODLE_DIR)) {
            $moodleDir = $this->platform->getWorkingDirectory();
        }
        $moodle = new MoodleInstallation($this->platform, $moodleDir);
        $moodle->configure();

        switch ($action) {
            case Argument::ARGUMENT_ACTION_LIST_PLUGIN_TYPES:
                $result = $moodle->getPluginTypes();
                break;
            default:
                throw new MoodleException(
                        sprintf('Invalid action "%s"', $action),
                        MoodleException::CODE_INVALID_ACTION);
        }

        $moodle->dispose();

        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));
    }
}
