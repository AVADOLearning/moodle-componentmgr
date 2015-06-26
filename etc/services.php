<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

use ComponentManager\Command\CommandFactory;
use ComponentManager\PlatformUtil;
use Symfony\Bundle\MonologBundle\DependencyInjection\Compiler\LoggerChannelPass;
use Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

/*
 * Configuration parameters.
 */
$cacheDirectory = PlatformUtil::localSharedDirectory()
        . PlatformUtil::directorySeparator() . 'componentmgr'
        . PlatformUtil::directorySeparator() . 'cache';
$container->setParameter('package_repository.cache_directory', $cacheDirectory);

/*
 * Command factory.
 */
$commandFactory = new Definition('\ComponentManager\Command\CommandFactory', [
    new Reference('logger'),
    [new Reference('package_repository.moodle_package_repository')],
]);
$commandFactory->addTag('monolog.logger', [
    'channel' => 'console',
]);
$container->setDefinition('command.command_factory', $commandFactory);

/*
 * Individual commands.
 */
$commands = [
    'install' => 'Install',
    'refresh' => 'Refresh',
];

$commandReferences = [];
foreach ($commands as $id => $className) {
    $qualifiedId        = "command.{$id}_command";
    $qualifiedClassName = CommandFactory::getCommandClassName($className);

    $command = new Definition($qualifiedClassName, [$className]);
    $command->setFactory([new Reference('command.command_factory'),
                          'createCommand']);

    $container->setDefinition($qualifiedId, $command);

    $commandReferences[] = new Reference($qualifiedId);
}

/*
 * Register a filesystem for handling cache operations.
 */
$container->register('filesystem', '\Symfony\Component\Filesystem\Filesystem');

/*
 * Register Monolog for logging within commands.
 */
$container->registerExtension(new MonologExtension());
$container->loadFromExtension('monolog', [
    'channels' => ['console'],
    'handlers' => [
        'console' => [
            'type'      => 'stream',
            'path'      => 'php://stdout',
            'formatter' => 'logger.console.formatter',
            'level'     => 'debug',
            'channels'  => ['console'],
        ],
    ],
]);

$container->register('logger.console.formatter',
                     'Bramus\Monolog\Formatter\ColoredLineFormatter');

$container->addCompilerPass(new LoggerChannelPass());

/*
 * Register Moodle.org package repository.
 */
$repository = new Definition('\ComponentManager\PackageRepository\MoodlePackageRepository', [
    new Reference('filesystem'),
    '%package_repository.cache_directory%'
            . PlatformUtil::directorySeparator() . 'moodle',
]);
$container->setDefinition('package_repository.moodle_package_repository',
                          $repository);

/*
 * Register the console application entry point.
 */
$application = new Definition('\ComponentManager\ComponentManager',
                              [$commandReferences]);
$application->addMethodCall('setContainer', [new Reference('service_container')]);
$container->setDefinition('application', $application);
