<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

if (!ini_get('date.timezone')) {
    date_default_timezone_set(@date_default_timezone_get());
}
