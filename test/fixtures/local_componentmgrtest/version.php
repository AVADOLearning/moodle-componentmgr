<?php

/**
 * Moodle component manager - build step test.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

defined('MOODLE_INTERNAL') || die;

/** @var stdClass $plugin */

$plugin->component = 'local_componentmgrtest';

$plugin->version = 2016042700;
$plugin->maturity = MATURITY_STABLE;

$plugin->requires = ANY_VERSION;
