<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Project;

use ComponentManager\Exception\ComponentProjectException;
use ComponentManager\Exception\NotImplementedException;

/**
 * Individual component project.
 */
class ComponentProjectFile extends JsonFile {
    /**
     * Component project file.
     *
     * @var string
     */
    const FILENAME = 'componentmgr.component.json';

    /**
     * Script: build.
     *
     * @var string
     */
    const SCRIPT_BUILD = 'build';

    /**
     * @override JsonFile
     */
    public function dump() {
        throw new NotImplementedException();
    }

    /**
     * Get a named script.
     *
     * @param string $name
     *
     * @return string
     *
     * @throws ComponentProjectException
     */
    public function getScript($name) {
        if (!property_exists($this->contents, 'scripts')
                || !property_exists($this->contents->scripts, $name)) {
            throw new ComponentProjectException(
                    "Missing script entry \"{$name}\"",
                    ComponentProjectException::CODE_MISSING_SCRIPT);
        }

        return $this->contents->scripts->{$name};
    }
}
