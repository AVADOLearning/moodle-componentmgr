<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

define('CM_ETC', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'etc');

require_once dirname(dirname(__DIR__))
        . DIRECTORY_SEPARATOR . 'vendor'
        . DIRECTORY_SEPARATOR . 'autoload.php';

require_once CM_ETC . DIRECTORY_SEPARATOR . 'bootstrap.php';
