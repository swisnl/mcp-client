<?php

namespace Swis\McpClient\Factories;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use Swis\McpClient\Exceptions\ProcessStartException;
use Swis\McpClient\Transporters\StdioTransporter;

/**
 * Factory for creating processes to be used with StdioTransporter.
 */
class ProcessFactory
{
    /**
     * Create a transporter connected to a process
     *
     * @param string $command The command to execute
     * @param array<string, string> $env Additional environment variables for the process
     * @param string|null $cwd Working directory for the process
     * @param LoggerInterface|null $logger Optional logger
     * @param int $autoRestartAmount Whether to automatically restart the process if it terminates
     * @param LoopInterface|null $loop Optional event loop
     * @return array{0: StdioTransporter, 1: resource} Transporter and process resource
     * @throws \Swis\McpClient\Exceptions\ProcessStartException If the process could not be started
     */
    public static function createTransporterForProcess(
        string $command,
        array $env = [],
        ?string $cwd = null,
        ?LoggerInterface $logger = null,
        int $autoRestartAmount = 0,
        ?LoopInterface $loop = null
    ): array {
        // Create a start process function that we can reuse for auto-healing
        $startProcess = function () use ($command, $env, $cwd) {
            $descriptorSpec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w'],   // stderr
            ];

            // Merge environment variables
            $processEnv = array_merge(getenv(), $env);

            // Start the process
            $process = proc_open($command, $descriptorSpec, $pipes, $cwd, $processEnv);

            if (! is_resource($process)) {
                throw new ProcessStartException("Failed to start process: $command");
            }

            // Set pipes to non-blocking mode
            stream_set_blocking($pipes[0], false);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            return [$process, $pipes];
        };

        // Start the initial process
        [$process, $pipes] = $startProcess();

        // Create transporter using process pipes
        // Note: We don't want the transporter to close the pipes when disconnecting
        // because we want to handle that manually when closing the process
        //
        // pipes[0] is the process's stdin (we write to it)
        // pipes[1] is the process's stdout (we read from it)
        $transporter = new StdioTransporter($pipes[1], $pipes[0], false, $logger, $loop, $pipes[2]);

        // Register auto-restart callback if enabled
        if ($autoRestartAmount) {
            $transporter->onReconnectAttempt(function () use ($startProcess, $logger, $command, &$process, &$autoRestartAmount) {

                if ($autoRestartAmount <= 0) {
                    $logger?->debug('Auto-restart limit reached, not restarting process');

                    return null;
                }

                $autoRestartAmount--;

                // Close the old process if it's still running
                if (is_resource($process)) {
                    $status = proc_get_status($process);
                    if ($status['running']) {
                        $logger?->debug('Terminating old process before restart');
                        proc_terminate($process, 15); // SIGTERM
                    }
                    proc_close($process);
                }

                $logger?->info('Auto-restarting process: ' . $command);

                try {
                    // Start a new process
                    [$newProcess, $newPipes] = $startProcess();

                    // Update the reference to the current process
                    $process = $newProcess;

                    // Return the new streams
                    return [$newPipes[1], $newPipes[0], $newPipes[2]];
                } catch (\Throwable $e) {
                    $logger?->error('Failed to restart process: ' . $e->getMessage());

                    throw $e;
                }
            });
        }

        return [$transporter, $process];
    }
}
