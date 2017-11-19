<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageFormat;

use ComponentManager\Exception\PackageFailureException;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Microsoft Web Deploy package format.
 *
 * Shells out to the msdeploy binary to facilitate packaging for IIS.
 *
 * @see http://www.iis.net/downloads/microsoft/web-deploy
 */
class WebDeployPackageFormat extends AbstractPackageFormat
        implements PackageFormat {
    /**
     * @override PackageFormat
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger) {
        $msdeploy = $this->platform->getExecutablePath('msdeploy');

        $args = [
            '-verb:sync',
            "-source:dirPath={$moodleDir}",
            "-dest:package={$destination}",
            '-skip:objectName=filePath,absolutePath=\'.git\''
        ];

        /* We can't use Symfony's ProcessBuilder here, as it wraps each argument
         * in double quotes, which cause msdeploy.exe to choke when it parses
         * its arguments. We'll manually escape each argument via ProcessUtils
         * and trim off the quotes. */
        $escapedArgs = [];
        foreach ($args as $arg) {
            $escapedArgs[] = substr(ProcessUtils::escapeArgument($arg), 1, -1);
        }

        /* Obviously, the binary itself must be quoted as its name contains
         * spaces. #enterprise */
        array_unshift($escapedArgs, ProcessUtils::escapeArgument($msdeploy));

        $commandLine = implode(' ', $escapedArgs);
        $process = new Process($commandLine);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PackageFailureException(
                    $process->getOutput() . $process->getErrorOutput(),
                    PackageFailureException::CODE_OTHER);
        }
    }
}
