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
        $fileContents   = is_file($filename)
                ? file_get_contents($filename) : '{}';
        $this->filename = $filename;
        $this->contents = json_decode($fileContents);

        if ($this->contents === null) {
            throw new InvalidProjectException(
                    sprintf("(%d) %s", json_last_error(), json_last_error_msg()));
        }
    }

    /**
     * Write updated content to the disk.
     *
     * @return void
     */
    public function commit() {
        $contents = json_encode($this->dump(), JSON_PRETTY_PRINT);
        file_put_contents($this->filename, $contents);
    }

    /**
     * Dump a representation of the file's contents for serialisation.
     *
     * @return mixed Anything serialisable to JSON.
     */
    abstract public function dump();
}
