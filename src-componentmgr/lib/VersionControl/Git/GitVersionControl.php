<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\VersionControl\Git;

use ComponentManager\Exception\VersionControlException;
use ComponentManager\PlatformUtil;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Git version control.
 */
class GitVersionControl {
    /**
     * The repository's on-disk location.
     *
     * @var string
     */
    protected $directory;

    /**
     * Remotes.
     *
     * @var \ComponentManager\VersionControl\Git\GitRemote[]
     */
    protected $remotes;

    /**
     * Initialiser.
     *
     * @param string $directory
     */
    public function __construct($directory) {
        $this->directory = $directory;
        $this->remotes   = [];
    }

    /**
     * Add the specified remote to the repository.
     *
     * @param \ComponentManager\VersionControl\Git\GitRemote $remote
     */
    public function addRemote(GitRemote $remote) {
        $name = $remote->getName();
        $uri  = $remote->getUri();

        $this->remotes[$name] = $remote;

        $git     = PlatformUtil::executable('git');
        $process = new Process("{$git} remote add {$name} {$uri}",
                               $this->directory);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_REMOTE_ADD_FAILED);
    }

    /**
     * Checkout the specified reference.
     *
     * @param string $ref
     *
     * @return void
     */
    public function checkout($ref) {
        $git     = PlatformUtil::executable('git');
        $process = new Process("{$git} checkout {$ref}",
                               $this->directory);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_CHECKOUT_FAILED);
    }

    /**
     * Checkout all files in the index to the specified directory.
     *
     * @param string $prefix
     *
     * @throws \ComponentManager\Exception\PlatformException
     */
    public function checkoutIndex($prefix) {
        $git     = PlatformUtil::executable('git');
        $process = new Process("{$git} checkout-index --all --prefix={$prefix}",
                               $this->directory);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_CHECKOUT_INDEX_FAILED);
    }

    /**
     * Fetch references from the specified remote.
     *
     * @param string  $remote
     * @param boolean $withTags
     *
     * @return void
     */
    public function fetch($remote, $withTags=true) {
        $git = PlatformUtil::executable('git');

        $process = new Process("{$git} fetch {$remote}",
                               $this->directory);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_FETCH_FAILED);

        if ($withTags) {
            $process = new Process("git fetch --tags {$remote}",
                                   $this->directory);
            $process->run();

            $this->ensureSuccess(
                    $process, VersionControlException::CODE_FETCH_FAILED);
        }
    }

    /**
     * Ensure the specified command executed successfully.
     *
     * @param \Symfony\Component\Process\Process $process
     * @param integer                            $code
     *
     * @throws \ComponentManager\Exception\VersionControlException
     */
    protected function ensureSuccess(Process $process, $code) {
        if (!$process->isSuccessful()) {
            var_dump($process->getWorkingDirectory());
            var_dump($process->getCommandLine());
            var_dump($process->getOutput());
            var_dump($process->getErrorOutput());

            throw new VersionControlException(
                    $process->getCommandLine(), $code);
        }
    }

    /**
     * Initialise \
     * @throws VersionControlException
     * @throws \ComponentManager\Exception\PlatformException
     */
    public function init() {
        $git = PlatformUtil::executable('git');

        $process = new Process("{$git} init",
                               $this->directory);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_INIT_FAILED);
    }
}
