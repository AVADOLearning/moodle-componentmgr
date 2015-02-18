<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension;

/*
 * Register the console application entry point.
 */
$application = new Definition('\ComponentManager\ComponentManagerApplication', [
    [
        new Reference('command.install_command'),
    ]
]);
$container->setDefinition('application', $application);

/*
 * Command factory.
 */

$commandFactory = new Definition('\ComponentManager\Command\CommandFactory', [
    new Reference('logger'),
]);
$container->setDefinition('command.command_factory', $commandFactory);

/*
 * Individual commands.
 */
$command = new Definition('\ComponentManager\Command\InstallCommand', [
    'Install',
]);
$command->setFactory([new Reference('command.command_factory'), 'createCommand']);
$container->setDefinition('command.install_command', $command);

/*
 * Register Monolog for logging within commands.
 */
$loader = new MonologExtension();
$loader->load([
    [
        'handlers' => [
            'console' => [
                'type' => 'console',
            ],
        ],
    ]
], $container);
