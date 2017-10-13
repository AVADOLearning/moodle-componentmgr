<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\VersionControl\Git;

use ComponentManager\Exception\VersionControlException;
use ComponentManager\VersionControl\Git\Command\CheckoutCommand;
use ComponentManager\VersionControl\Git\Command\CheckoutIndexCommand;
use ComponentManager\VersionControl\Git\Command\Command;
use ComponentManager\VersionControl\Git\Command\FetchCommand;
use ComponentManager\VersionControl\Git\Command\InitCommand;
use ComponentManager\VersionControl\Git\Command\RevParseCommand;
use ComponentManager\VersionControl\Git\Command\RemoteAddCommand;
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
     * Git executable.
     *
     * @var string
     */
    protected $gitExecutable;

    /**
     * Remotes.
     *
     * @var GitRemote[]
     */
    protected $remotes;

    /**
     * Initialiser.
     *
     * @param string $gitExecutable
     * @param string $directory
     */
    public function __construct($gitExecutable, $directory) {
        $this->gitExecutable = $gitExecutable;

        $this->directory = $directory;
        $this->remotes   = [];
    }

    /**
     * Get a ready-to-run Process instance.
     *
     * @param string[] $arguments Arguments to pass to the Git binary.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function createProcess($arguments) {
        array_unshift($arguments, $this->gitExecutable);

        $builder = new ProcessBuilder($arguments);
        $builder->setWorkingDirectory($this->directory);

        return $builder->getProcess();
    }

    /**
     * Execute and ensure successful execution of a command.
     *
     * @param Command $command
     * @param integer|null $exceptionCode
     *
     * @return Process
     *
     * @throws VersionControlException
     */
    public function runCommand(Command $command, $exceptionCode=null) {
        $process = $this->createProcess($command->getCommandLine());
        $process->run();

        if ($exceptionCode !== null && !$process->isSuccessful()) {
            throw new VersionControlException(
                $process->getCommandLine(), $exceptionCode);
        }

        return $process;
    }

    /**
     * Add the specified remote to the repository.
     *
     * @param GitRemote $remote
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function addRemote(GitRemote $remote) {
        $command = new RemoteAddCommand($remote);
        return $this->runCommand($command,
                VersionControlException::CODE_REMOTE_ADD_FAILED);
    }

    /**
     * Checkout the specified reference.
     *
     * @param string $ref
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function checkout($ref) {
        $command = new CheckoutCommand($ref);
        return $this->runCommand(
                $command, VersionControlException::CODE_CHECKOUT_FAILED);
    }

    /**
     * Checkout all files in the index to the specified directory.
     *
     * @param string $prefix
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function checkoutIndex($prefix) {
        $command = new CheckoutIndexCommand($prefix);
        return $this->runCommand(
                $command, VersionControlException::CODE_CHECKOUT_INDEX_FAILED);
    }

    /**
     * Fetch references from the specified remote.
     *
     * @param string|null $remote
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function fetch($remote=null) {
        $command = new FetchCommand($remote);
        return $this->runCommand(
                $command, VersionControlException::CODE_FETCH_FAILED);
    }

    /**
     * Fetch tags from the specified remote.
     *
     * @param string|null $remote
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function fetchTags($remote=null) {
        $command = new FetchCommand($remote);
        $command->setTags(true);
        return $this->runCommand(
                $command, VersionControlException::CODE_FETCH_FAILED);
    }

    /**
     * Initialise Git repository.
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function init() {
        $command = new InitCommand();
        return $this->runCommand($command);
    }

    /**
     * Get the commit hash for the specified ref.
     *
     * @param string $ref
     *
     * @return Process
     *
     * @throws VersionControlException
     *
     * @codeCoverageIgnore
     */
    public function parseRevision($ref) {
        $command = new RevParseCommand($ref);
        return $this->runCommand(
                $command, VersionControlException::CODE_REV_PARSE_FAILED);
    }
}
