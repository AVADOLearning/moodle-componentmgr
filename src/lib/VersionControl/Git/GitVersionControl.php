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

        $process = $this->getProcess(['remote', 'add', $name, $uri]);
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
        $process = $this->getProcess(['checkout', $ref]);
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
        $process = $this->getProcess(
                ['checkout-index', '--all', "--prefix={$prefix}"]);
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
        $process = $this->getProcess(['fetch', $remote]);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_FETCH_FAILED);

        if ($withTags) {
            $process = $this->getProcess(['fetch', '--tags', $remote]);
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
            throw new VersionControlException(
                    $process->getCommandLine(), $code);
        }
    }

    /**
     * Get a ready-to-run Process instance.
     *
     * @param  mixed[] $arguments Arguments to pass to the Git binary.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess($arguments) {
        array_unshift($arguments, PlatformUtil::executable('git'));

        $builder = new ProcessBuilder($arguments);
        $builder->setWorkingDirectory($this->directory);

        return $builder->getProcess();
    }

    /**
     * Initialise Git repository.
     *
     * @throws VersionControlException
     * @throws \ComponentManager\Exception\PlatformException
     */
    public function init() {
        $process = $this->getProcess(['init']);
        $process->run();

        $this->ensureSuccess(
                $process, VersionControlException::CODE_INIT_FAILED);
    }
}
