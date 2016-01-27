#!/usr/bin/env php
<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor'
                              . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Configuration directory.
 *
 * @var string
 */
define('CM_ETC', dirname(__DIR__) . '/etc');

require CM_ETC . '/bootstrap.php';

$container = new ContainerBuilder();
$loader = new PhpFileLoader($container, new FileLocator(CM_ETC));
$loader->load('services.php');
$container->compile();

$app = $container->get('application');
$app->run();
