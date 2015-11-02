<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Project;

use ComponentManager\Exception\InvalidProjectException;

abstract class JsonFile {
    /**
     * Decoded file contents.
     *
     * @var \stdClass
     */
    protected $contents;

    /**
     * File name.
     *
     * @var string
     */
    protected $filename;

    /**
     * Initialiser.
     *
     * @param string $filename
     */
    public function __construct($filename) {
        if (!is_file($filename)) {
            file_put_contents($filename, '{}');
        }

        $fileContents   = file_get_contents($filename);
        $this->filename = $filename;
        $this->contents = json_decode($fileContents);

        if ($this->contents === null) {
            throw new InvalidProjectException(
                sprintf("(%d) %s", json_last_error(), json_last_error_msg()));
        }
    }
}
