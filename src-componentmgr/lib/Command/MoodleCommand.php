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
use ComponentManager\Exception\MoodleException;
use ComponentManager\MoodleInstallation;
use ComponentManager\PlatformUtil;
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
class MoodleCommand extends AbstractCommand {
    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Queries properties of the Moodle installation in the present working directory.
HELP;

    /**
     * @override \Symfony\Component\Console\Command\Command
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
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $action = $input->getArgument(Argument::ARGUMENT_ACTION);
        if (!$moodleDir = $input->getOption(Argument::ARGUMENT_MOODLE_DIR)) {
            $moodleDir = PlatformUtil::workingDirectory();
        }
        $moodle = new MoodleInstallation($moodleDir);

        switch ($action) {
            case Argument::ARGUMENT_ACTION_LIST_PLUGIN_TYPES:
                $result = $moodle->getPluginTypes();
                break;
            default:
                throw new MoodleException("Invalid action \"{$action}\"", MoodleException::CODE_INVALID_ACTION);
        }

        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));
    }
}
