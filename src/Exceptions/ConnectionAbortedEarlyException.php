<?php

namespace Swis\McpClient\Exceptions;

class ConnectionAbortedEarlyException extends \Exception
{
    /**
     * @var array<string> The error bag
     */
    private array $errorBag;

    /**
     * @param array<string> $errorBag
     * @return void
     */
    public function setErrorBag(array $errorBag): void
    {
        $this->errorBag = $errorBag;
    }

    /**
     * @return array<string>
     */
    public function getErrorBag(): array
    {
        return $this->errorBag;
    }
}
