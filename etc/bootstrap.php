<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

// Error reporting
ini_set('display_errors',  'on');
ini_set('error_reporting', E_ALL);

// Set the default timezone if not already set
if (!ini_get('date.timezone')) {
    date_default_timezone_set(@date_default_timezone_get());
}
