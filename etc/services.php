<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
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
$commandFactory = new Definition('\ComponentManager\Command\CommandFactory');
$commandFactory->addTag('monolog.logger', [
    'channel' => 'console',
]);
$commandFactory->addMethodCall('setContainer', [new Reference('service_container')]);
$container->setDefinition('command.command_factory', $commandFactory);

/*
 * Package repository factory.
 */
$packageRepositoryFactory = new Definition('\ComponentManager\PackageRepository\PackageRepositoryFactory', [
    new Reference('filesystem'),
    '%package_repository.cache_directory%',
]);
$container->setDefinition('package_repository.package_repository_factory', $packageRepositoryFactory);

/*
 * Package source factory.
 */
$packageSourceFactory = new Definition('\ComponentManager\PackageSource\PackageSourceFactory', [
    new Reference('filesystem'),
]);
$container->setDefinition('package_source.package_source_factory', $packageSourceFactory);

/*
 * Package format factory.
 */
$packageFormatFactory = new Definition('\ComponentManager\PackageFormat\PackageFormatFactory', [
    new Reference('filesystem'),
]);
$container->setDefinition('package_format.package_format_factory', $packageFormatFactory);

/*
 * Individual commands.
 */
$commands = [
    'install' => 'Install',
    'moodle'  => 'Moodle',
    'package' => 'Package',
    'refresh' => 'Refresh',
];

$commandReferences = [];
foreach ($commands as $id => $className) {
    $qualifiedId        = "command.{$id}_command";
    $qualifiedClassName = CommandFactory::getCommandClassName($className);

    $command = new Definition($qualifiedClassName, [$className]);
    $command->setFactory([
        new Reference('command.command_factory'),
        'createCommand',
    ]);

    $container->setDefinition($qualifiedId, $command);

    $commandReferences[] = new Reference($qualifiedId);
}

/*
 * Register a filesystem for handling cache operations.
 */
$container->register('filesystem', '\Symfony\Component\Filesystem\Filesystem');

/*
 * Register a Moodle.org API client.
 */
$container->register('moodleApi', '\ComponentManager\MoodleApi');

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
 * Register zip package source.
 */
$packageSource = new Definition('\ComponentManager\PackageSource\ZipPackageSource');
$packageSource->setShared(false);
$container->setDefinition('package_source.zip_package_source', $packageSource);

/*
 * Register the console application entry point.
 */
$application = new Definition('\ComponentManager\ComponentManager',
                              [$commandReferences]);
$application->addMethodCall('setContainer', [new Reference('service_container')]);
$container->setDefinition('application', $application);
