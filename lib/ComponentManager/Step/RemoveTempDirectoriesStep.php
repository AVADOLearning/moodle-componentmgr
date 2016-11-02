<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Platform\Platform;
use Psr\Log\LoggerInterface;

/**
 * Remove temporary directories.
 */
class RemoveTempDirectoriesStep implements Step {
    /**
     * Platform.
     *
     * @var \ComponentManager\Platform\Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Platform\Platform $platform
     */
    public function __construct(Platform $platform) {
        $this->platform = $platform;
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function execute($task, LoggerInterface $logger) {
        $logger->info('Removing temporary directories');
        $this->platform->removeTempDirectories();
    }
}
