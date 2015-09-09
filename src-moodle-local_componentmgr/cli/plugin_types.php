<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

define('CLI_SCRIPT', true);
require_once dirname(dirname(dirname(__DIR__))) . '/config.php';

$pluginmgr = core_plugin_manager::instance();
echo json_encode($pluginmgr->get_plugin_types());
